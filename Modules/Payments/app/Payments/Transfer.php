<?php

namespace Modules\Payments\Payments;

use Illuminate\Support\Facades\Auth;
use Modules\Payments\Models as TransferModel;

class Transfer extends Payment
{
    protected string $code = 'transfer';
    protected string $name = 'Transfer';

    public function registerMethod($id = null)
    {

        return redirect()->route('payments.transfer.create', ['id'=> $id]);
    }

    public function getEditView(): string | null
    {
        return 'payments::transfer.edit';
    }

    public function getMethodData($id): array | null
    {
        $tm = TransferModel::withTrashed()->where('payment_method_id', $id)->first();
        return $tm?->toArray();
    }

    public function setMethodData(int $id, array $data): array | null
    {
        $data['user_id'] = Auth::user()->id;

        $tm = TransferModel::withTrashed()->updateOrCreate(
            ['payment_method_id' => $id],
            $data
        );

        return $tm ? $tm->toArray() : null;
    }

    /**
    * Method which return path of blade template which can be displayed in invoice
    */
    public function getMethodTemplate(int $id): array | null
    {
        $tm = TransferModel::withTrashed()->where('payment_method_id', $id)->first();

        return [
            'template' => 'payments::transfer.default.info',
            'data' => $tm
        ];

    }
}
