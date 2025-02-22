<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Invoice;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\InvoiceItem;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationGroup = 'Invoices';
    protected static ?string $navigationLabel = 'Invoices';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->columns(12)
            ->schema(Invoice::getForm());
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label('Invoice No.')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('buyer.name')
                    ->label('Buyer'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment Status'),

                Tables\Columns\TextColumn::make('total_gross')
                    ->label('Total Gross')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'template' => 'Template',
                        'published' => 'Published',
                        'deleted' => 'Deleted',
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        $invoice = static::getModel()::create($data);

        foreach ($data['items'] as $item) {

            InvoiceItem::updateOrCreate(
                [
                    'invoice_id' => $invoice->id,
                    'id' => $item['id'] ?? null,
                ],
                [
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price_net' => $item['price_net'],
                    'tax_rate' => $item['tax_rate'],
                    'discount' => $item['discount'],
                    'total_net' => $item['total_net'],
                    'total_gross' => $item['total_gross'],
                    'total_tax' => $item['total_tax'],
                    'total_discount' => $item['total_discount']
                ]
            );
        }
        return $invoice;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
