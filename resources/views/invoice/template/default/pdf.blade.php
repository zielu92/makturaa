<?php
$colspan = 5;
if($showQty)
    $colspan += 1;
if($showDiscount)
    $colspan += 1;
?>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Faktura {{$invoice->no}}</title>

    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
<div class="max-w-7xl mx-auto">
    <header class="flex justify-between text-center px-10 py-5">

        <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl flex items-end">
            @if($invoice->type=='proforma')
                Proforma
            @else
                Faktura
            @endif
        </h1>
    </header>

    <section class="float-right text-right px-10 py-1">
        <p class="text-xs">
            <strong>Faktura nr. / Invoice no:</strong>
            {{$invoice->no}}
        </p>
        <p class="text-xs">
            Oryginał /  Original
        </p>
        <p class="text-xs">
            <strong>Miejscowość / Place:</strong>
            {{$invoice->place}}
        </p>
        <p class="text-xs">
            <strong>Data wystawienia / Date of issue:</strong>
            {{$invoice->issue_date}}
        </p>
        <p class="text-xs">
            <strong>Data sprzedaży / Date of sell:</strong>
            {{$invoice->sale_date}}
        </p>
    </section>

    <div class="clear-both"></div>

    <section class="flex justify-between px-10 py-1">
        <p class="text-xs">
            <strong>Sprzedawca (Seller):</strong><br>

        </p>

        <p class="text-xs">
            <strong>Nabywca (Buyer):</strong><br>
            {{$invoice->buyer->company_name}}<br>
            {{$invoice->buyer->address}}<br>
            {{$invoice->buyer->postal_code}}
            {{$invoice->buyer->city}}<br>
            @if($invoice->buyer->nip!='')
                NIP/Tax ID: {{$invoice->buyer->nip}}<br>
            @endif
            @if($invoice->buyer->regon!='')
                REGON: {{$invoice->buyer->regon}}<br>
            @endif
        </p>
    </section>

    <section class="px-6 py-5">
        <div class="overflow-hidden border border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <strong>L.p.</strong><br>
                        No
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <strong>Nazwa Usługi</strong><br>
                        Description
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <strong>Wartość netto</strong><br>
                        Net value
                    </th>
                    <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-right">
                        <strong>Stawka VAT</strong><br>
                        VAT
                    </th>
                    @if($showDiscount)
                        <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-right">
                            <strong>Rabat</strong><br>
                            Discount
                        </th>
                    @endif
                    @if($showQty)
                        <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-right">
                            <strong>Ilość</strong><br>
                            QTY
                        </th>
                    @endif
                    <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-right">
                        <strong>Kwota VAT</strong><br>
                        Vat amount
                    </th>
                    <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-right">
                        <strong>Wartość brutto</strong><br>
                        Total amount
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr class="bg-white">
                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-900">
                            {{$loop->index+1}}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-500">
                            {{$item->name}}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-500 text-right">
                            {{$item->price_net}} {{html_entity_decode($invoice->currency->symbol)}}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-500 text-right">
                            {{--                        TODO: WRITE HELPER--}}
                            {{is_numeric($item->tax_rate) ? $item->tax_rate.'%': $item->tax_rate}}
                        </td>
                        @if($showQty)
                            <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-500 text-right">
                                {{$item->quantity}}
                            </td>
                        @endif
                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-500 text-right">
                            {{$item->tax_amount}} {{html_entity_decode($invoice->currency->symbol)}}
                        </td>
                        @if($showDiscount)
                            <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-500 text-right">
                                {{$item->total_discount}} {{html_entity_decode($invoice->currency->symbol)}}
                            </td>
                        @endif
                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-500 text-right">
                            {{$item->price_gross}} {{html_entity_decode($invoice->currency->symbol)}}
                        </td>
                    </tr>
                @endforeach
                @if($invoice->total_gross!=$invoice->total_net)
                    <tr class="bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-900 font-medium text-right" colspan="{{$colspan}}">
                            <strong>Wartość netto</strong> / Sub-Total:
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-500 text-right font-medium">
                            {{$invoice->total_net}} {{html_entity_decode($invoice->currency->symbol)}}
                        </td>
                    </tr>
                @endif
                @if($invoice->total_discount!='0.00')
                    <tr class="bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-900 font-medium text-right" colspan="{{$colspan}}">
                            <strong>Wartość rabatu</strong> / Discount:
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-500 text-right font-medium">
                            {{$invoice->total_discount}} {{html_entity_decode($invoice->currency->symbol)}}
                        </td>
                    </tr>
                @endif
                @if($invoice->total_tax!='0.00')
                    <tr class="bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-900 font-medium text-right" colspan="{{$colspan}}">
                            <strong>Wartość podatku</strong> / Tax:
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-500 text-right font-medium">
                            {{$invoice->total_tax}} {{html_entity_decode($invoice->currency->symbol)}}
                        </td>
                    </tr>
                @endif
                <tr class="bg-gray-50">
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 font-medium text-right" colspan="{{$colspan}}">
                        <strong>Do Zapłaty</strong> / Total
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-right font-medium">
                        {{$invoice->total_gross}} {{html_entity_decode($invoice->currency->symbol)}}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>
    <section>
        <div class="text-center mt-2">
{{--            @if($paymentMethod && isset($paymentMethod['template']))--}}
{{--                @include($paymentMethod['template'])--}}
{{--            @endif--}}
        </div>
    </section>
    <section>
        <div class="text-center pb-8">
            {{$invoice->comment}}
        </div>
    </section>
</div>
</body>
</html>
