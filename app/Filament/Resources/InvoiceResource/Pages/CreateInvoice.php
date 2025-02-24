<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions;
use App\Models\InvoiceItem;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\InvoiceResource;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $invoice = $this->record;
        $totals = [
            'grand_total_net' => 0,
            'grand_total_tax' => 0,
            'grand_total_gross' => 0,
            'grand_total_discount' => 0,
        ];

        foreach ($invoice->invoiceItems as $item) {
            // Accumulate totals
            $totals['grand_total_net'] += $item['total_net'];
            $totals['grand_total_tax'] += $item['total_tax'];
            $totals['grand_total_gross'] += $item['total_gross'];
            $totals['grand_total_discount'] += $item['total_discount'];
        }

        $invoice->update($totals);
    }
}
