<?php

namespace App\Models;

use Filament\Forms\Get;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'no',
        'buyer_id',
        'type',
        'payment_status',
        'place',
        'sale_date',
        'due_date',
        'issue_date',
        'parent_id',
        'user_id',
        'comment',
        'currency',
        'issuer_name',
        'grand_total_net',
        'grand_total_gross',
        'grand_total_tax',
        'grand_total_discount',
        'paid',
        'due',
        'path',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'buyer_id' => 'integer',
        'sale_date' => 'date',
        'due_date' => 'date',
        'parent_id' => 'integer',
        'user_id' => 'integer',
        'total_net' => 'decimal:2',
        'total_gross' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'paid' => 'decimal:2',
        'due' => 'decimal:2',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public static function getForm(int $buyerId = null): array
    {
        return [
            TextInput::make('no')
                ->label('Invoice Number')
                ->autocomplete(false)
                ->columnSpan(2)
                ->required(),

            Select::make('type')
                ->label('Invoice Type')
                ->columnSpan(2)
                ->options([
                    'regular' => 'Final',
                    'proforma' => 'Proforma',
                ])
                ->default('regular')
                ->required(),

            Select::make('payment_status')
                ->label('Payment Status')
                ->columnSpan(2)
                ->options([
                    'notpaid' => 'Pending Payment',
                    'paid' => 'Paid',
                ])
                ->default('notpaid')
                ->required(),

            Select::make('buyer_id')
                ->hidden(function() use ($buyerId) {
                    return $buyerId !== null;
                })
                ->relationship('buyer', 'name')
                ->columnSpan(2)
                ->preload()
                ->required(),

            // Forms\Components\Select::make('payment_method_id')
            //     ->label('Payment Method')
            //     ->relationship('paymentMethod', 'name')
            //     ->required(),

            TextInput::make('place')
                ->label('Place of Issue')
                ->columnSpan(2)
                ->nullable(),

            DatePicker::make('sale_date')
                ->label('Sale Date')
                ->columnSpan(2)
                ->nullable(),

            DatePicker::make('issue_date')
                ->label('Issue Date')
                ->columnSpan(2)
                ->required(),

            DatePicker::make('due_date')
                ->label('Due Date')
                ->columnSpan(2)
                ->required(),

            TextInput::make('comment')
                ->label('Comment')
                ->columnSpan(2)
                ->nullable(),

            TextInput::make('issuer_name')
                ->label('Issuer Name')
                ->columnSpan(2)
                ->nullable(),

            Repeater::make('items')
                ->label('Invoice Items')
                ->columnSpanFull()
                ->reorderableWithButtons()
                ->columns(12)
                ->schema([
                    Textarea::make('name')
                        ->label('Item Name')
                        ->columnSpan(2)
                        ->required(),

                    TextInput::make('quantity')
                        ->label('Quantity')
                        ->columnSpan(1)
                        ->lazy()
                        ->debounce(500)
                        ->numeric()
                        ->minValue(1)
                        ->required()
                        ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateGrandTotals($set, $get))
                        ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateTotals($set, $get)),

                    TextInput::make('price_net')
                        ->label('Net Price')
                        ->numeric()
                        ->lazy()
                        ->debounce(500)
                        ->columnSpan(2)
                        ->minValue(0.01)
                        ->suffix("zł")
                        ->required()
                        ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateGrandTotals($set, $get))
                        ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateTotals($set, $get)),

                    Select::make('tax_rate')
                        ->label('Tax Rate')
                        ->columnSpan(1)
                        ->lazy()
                        ->debounce(500)
                        ->options([
                            '23' => '23%',
                            '22' => '22%',
                            '8' => '8%',
                            '5' => '5%',
                            '0' => '0%',
                            'zw' => 'Exempt',
                            'np' => 'Not Applicable',
                        ])
                        ->default('23')
                        ->required()
                        ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateGrandTotals($set, $get))
                        ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateTotals($set, $get)),

                    TextInput::make('discount')
                        ->label('Discount')
                        ->lazy()
                        ->debounce(500)
                        ->columnSpan(2)
                        ->nullable()
                        ->numeric()
                        ->suffix("zł")
                        ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateGrandTotals($set, $get))
                        ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateTotals($set, $get)),

                    TextInput::make('price_gross')
                        ->label('Gross Price')
                        ->numeric()
                        ->columnSpan(2)
                        ->suffix("zł")
                        ->readOnly(),

                        TextInput::make('tax_amount')
                        ->label('Tax Amount')
                        ->columnSpan(2)
                        ->readOnly()
                        ->suffix("zł")
                        ->numeric(),

                    TextInput::make('total_net')
                        ->label('Total Net')
                        ->columnSpan(3)
                        ->readOnly()
                        ->suffix("zł")
                        ->numeric(),

                    TextInput::make('total_gross')
                        ->label('Total Gross')
                        ->columnSpan(3)
                        ->readOnly()
                        ->suffix("zł")
                        ->numeric(),

                    TextInput::make('total_tax')
                        ->label('Total Tax')
                        ->columnSpan(3)
                        ->readOnly()
                        ->suffix("zł")
                        ->numeric(),

                    TextInput::make('total_discount')
                        ->label('Total Discount')
                        ->columnSpan(3)
                        ->readOnly()
                        ->suffix("zł")
                        ->numeric(),
                ])
                ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateGrandTotals($set, $get))
                ->afterStateHydrated(fn(callable $set, callable $get) => self::updateGrandTotals($set, $get))
                ->cloneable()
                ->relationship('InvoiceItems')
                ->required(),
                Section::make("Grand total summary")
                ->columns(12)
                ->schema([
                    Placeholder::make('grand_total_net')
                        ->label('Grand Total Net')
                        ->content(fn(Get $get) => number_format($get('grand_total_net') ?? 0, 2) . ' zł')
                        ->columnSpan(3),

                    Placeholder::make('grand_total_tax')
                        ->label('Grand Total Tax')
                        ->content(fn(Get $get) => number_format($get('grand_total_tax') ?? 0, 2) . ' zł')
                        ->columnSpan(3),

                    Placeholder::make('grand_total_gross')
                        ->label('Grand Total Gross')
                        ->content(fn(Get $get) => number_format($get('grand_total_gross') ?? 0, 2) . ' zł')
                        ->columnSpan(3),

                    Placeholder::make('grand_total_discount')
                        ->label('Grand Total Discount')
                        ->content(fn(Get $get) => number_format($get('grand_total_discount') ?? 0, 2) . ' zł')
                        ->columnSpan(3),
                ]),
            ];
    }


    public static function updateTotals(callable $set, callable $get): array
    {
        // Ensure values are numeric and default to 0 if not
        $quantity = is_numeric($get('quantity')) ? (int) $get('quantity') : 0;
        $priceNet = is_numeric($get('price_net')) ? (float) $get('price_net') : 0.00;
        $discount = is_numeric($get('discount')) ? (float) $get('discount') : 0.00;
        $taxRate = $get('tax_rate') ?? '23';

        // Determine the tax rate
        $taxPercentage = in_array($taxRate, ['zw', 'np']) ? 0 : (int) $taxRate;

        // Calculate values
        $totalNet = max(($quantity * $priceNet) - $discount, 0);
        $totalTax = round(($totalNet * $taxPercentage) / 100, 2);
        $priceGross = round(($priceNet - $discount) + (($priceNet * $taxPercentage) / 100), 2);
        $taxAmount = round((($priceNet - $discount) * $taxPercentage) / 100, 2);
        $totalGross = round($totalNet + $totalTax, 2);
        $discount = round($discount, 2);

        // Create a mapping of keys to values
        $fields = [
            'price_gross'    => $priceGross,
            'tax_amount'     => $taxAmount,
            'total_net'      => $totalNet,
            'total_tax'      => $totalTax,
            'total_gross'    => $totalGross,
            'total_discount' => $discount,
        ];

        // Iterate and set values dynamically
        foreach ($fields as $key => $value) {
            $set($key, $value);
        }
        // Call grand totals update
        self::updateGrandTotals($set, $get);

        return $fields;
    }


    public static function updateGrandTotals(callable $set, callable $get): void
    {
        $items = $get('items') ?? [];

        // Reset totals before summing
        $totalNet = 0.00;
        $totalTax = 0.00;
        $totalGross = 0.00;
        $totalDiscount = 0.00;

        foreach ($items as $index => $item) {
            // Ensure values are numeric, default to 0 if not
            $quantity = is_numeric($item['quantity']) ? (int) $item['quantity'] : 0;
            $priceNet = is_numeric($item['price_net']) ? (float) $item['price_net'] : 0.00;
            $discount = is_numeric($item['discount']) ? (float) $item['discount'] : 0.00;
            $taxRate = $item['tax_rate'] ?? '23';

            // Recalculate item totals
            $itemTotalNet = max(($quantity * $priceNet) - $discount, 0);
            $taxPercentage = in_array($taxRate, ['zw', 'np']) ? 0 : (int) $taxRate;
            $itemTotalTax = round(($itemTotalNet * $taxPercentage) / 100, 2);
            $itemTotalGross = round($itemTotalNet + $itemTotalTax, 2);

            // Sum up the recalculated values
            $totalNet += $itemTotalNet;
            $totalTax += $itemTotalTax;
            $totalGross += $itemTotalGross;
            $totalDiscount += $discount;
        }

        // Round final totals to 2 decimal places
        $totalNet = round($totalNet, 2);
        $totalTax = round($totalTax, 2);
        $totalGross = round($totalGross, 2);
        $totalDiscount = round($totalDiscount, 2);

        // Update the grand total fields
        $set('grand_total_net', $totalNet);
        $set('grand_total_tax', $totalTax);
        $set('grand_total_gross', $totalGross);
        $set('grand_total_discount', $totalDiscount);
    }
}
