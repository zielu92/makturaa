<?php

namespace App\Filament\Pages;

use Closure;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;

class Settings extends BaseSettings
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    public function schema(): array|Closure
    {
        return [
            Tabs::make('General')
                ->schema([
                    Tabs\Tab::make('Seller')
                    ->columns(4)
                    ->schema([
                        TextInput::make('seller.company_name')
                            ->required(),
                        TextInput::make('seller.address')
                            ->label('Company Address (Street)')
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('seller.scity')
                            ->label('Company Address (City)')
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('seller.postal_code')
                            ->label('Company Address (Postal Code)')
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('seller.country')
                            ->label('Company Address (Country)')
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('seller.nip')
                            ->label('Company NIP')
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('seller.email')
                            ->label('Seller Email')
                            ->email()
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('seller_.hone')
                            ->label('Seller Phone')
                            ->tel()
                            ->maxLength(255)
                            ->required(),
                    ]),
                    Tabs\Tab::make('Invoice')
                    ->columns(4)
                    ->schema([
                        TextInput::make('invoice.default_issuer')
                            ->label('Default Invoice Issuer')
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('invoice.default_place')
                            ->label('Default Invoice Issuance Place')
                            ->maxLength(255)
                            ->required(),
                        Select::make('invoice.default_tax_rate')
                            ->label('Default VAT')
                            ->options([
                                '23' => '23%',
                                '22' => '22%',
                                '8' => '8%',
                                '5' => '5%',
                                '0' => '0%',
                                'zw' => 'Exempt',
                                'np' => 'Not Applicable',
                            ])
                            ->default(23),
                        TextInput::make('invoice.default_pattern')
                            ->label('Default Invoice Generation Pattern')
                            ->maxLength(255)
                            ->default('{nm}/{m}/{y}')
                            ->helperText("
                                {nm} - Previous invoice number this month;
                                {ny} - Previous invoice number this year;
                                {m} - Current month number;
                                {y} - Current year number;
                                {random} - Random number;
                            ")
                    ]),
                ]),
        ];
    }
}
