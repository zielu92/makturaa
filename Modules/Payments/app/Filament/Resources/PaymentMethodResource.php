<?php

namespace Modules\Payments\Filament\Resources;

use Modules\Payments\Filament\Resources\PaymentMethodModelResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Payments\Models\PaymentMethodModel;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethodModel::class;

    protected static ?string $navigationGroup = 'Payment Methods';
    protected static ?string $navigationLabel = 'Basic settings';
    public static function form(Form $form): Form
    {
        return $form
            ->schema(PaymentMethodModel::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('method')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('active')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentMethods::route('/'),
            'create' => Pages\CreatePaymentMethod::route('/create'),
            'edit' => Pages\EditPaymentMethod::route('/{record}/edit'),
        ];
    }
}
