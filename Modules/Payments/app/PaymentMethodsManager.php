<?php

namespace Modules\Payments;

use Illuminate\Database\Console\Migrations\StatusCommand;
use Illuminate\Support\Arr;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Facades\Config;

class PaymentMethodsManager
{
    /**
     * Returns all supported payment methods
     *
     * @return array
     */
    public static function getPaymentMethods(): array
    {
        return collect(Config::get('payment_methods'))
            ->map(function ($paymentMethod) {
                $object = app($paymentMethod['class']);

                if ($object->isAvailable()) {
                    return [
                        'method' => $object->getCode(),
                        'method_title' => $object->getTitle(),
                        'description' => $object->getDescription(),
                        'sort' => $object->getSortOrder(),
                    ];
                }

                return null;
            })
            ->filter()
            ->sortBy('sort')
            ->values()
            ->all();
    }

    /** Execute additional features (like auth, to external service or anything like this)
     * @param string $method
     * @param int $id
     * @return object
     */
    public static function registerMethod(string $method, int $id = null): object
    {
        $object = app(config('payment_methods.' . $method . '.class'));
        return $object->registerMethod($id);
    }

    /**
     * Get title of the payment methods
     * @parm string $method
     * @return object
     */
    public static function getTitle(string $method)
    {
        $object = app(config('payment_methods.' . $method . '.class'));
        return $object->getTitle();
    }

    /**
     * get the URL - if there is any
     * @param string $method
     * @param int $id
     * @return mixed
     */
    public static function getPaymentURL(string $method, int $id): object
    {
        $object = app(config('payment_methods.' . $method . '.class'));
        return $object->getChannelURL($id);
    }

    /**
     * check if payment method have url
     * @param string $method
     * @return object
     */
    public static function haveURL(string $method): object
    {
        $object = app(config('payment_methods.' . $method . '.class'));
        return $object->haveURL();
    }

    /**
     * method to retrieve view to edit payment method
     */
    public static function getEditView(string $method): string | null
    {
        $object = app(config('payment_methods.' . $method . '.class'));
        return view()->exists($object->getEditView()) ? $object->getEditView(): null;
    }

    /**
     * Method to get data about specific payment method from db
     */
    public static function getPaymentDataMethod(string $method, int $id): array | null
    {
        $object = app(config('payment_methods.' . $method . '.class'));
        return  $object->getMethodData($id);
    }

    /**
     * method to set data about specific payment method
     */
    public static function setPaymentDataMethod(string $method, int $id, array $data): array | null
    {
        $object = app(config('payment_methods.' . $method . '.class'));
        return $object->setMethodData($id, $data);
    }

    public static function getPaymentMethodTemplate(string $method, int $id): array | null
    {
        $object = app(config('payment_methods.' . $method . '.class'));
        return $object->getMethodTemplate($id);
    }

}
