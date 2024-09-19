@extends($activeTemplate . 'layouts.app')
@section('panel')
    <div id="invoiceholder">
        <div id="invoice">
            <div id="invoice-top">
                <div class="logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ siteLogo() }}" alt="image">
                    </a>
                </div>
                <div class="title">
                    <h1>@lang('Invoice') #{{ @$transaction->trx }}</h1>
                    <p>@lang('Issued'): {{ showDateTime(@$invoice->created_at, 'd M Y') }}</p>
                </div>
            </div>
            <div id="invoice-mid">
                <div class="info">
                    <h2>{{ @$transaction->user->fullname }}</h2>
                    <p>{{ @$transaction->user->email }}<br />
                        {{ @$transaction->user->address }}<br />
                        {{ @$transaction->user->country_name }}
                </div>
                <div id="project" style="text-align: right;">
                    <h2 class="mb-0">@lang('Total Amount')</h2>
                    <h3 class="mt-0">{{ showAmount(@$transaction->amount) }}</h3>
                </div>
            </div>
            @php
                $title = null;
                $titleValue = null;
                $title = @$plan ? 'Plan' : 'Resource';
                $titleValue = @$plan ? $plan->name : $image->title;
            @endphp
            <div id="invoice-bot">
                <div id="table">
                    <table>
                        <thead>
                            <tr class="tabletitle">
                                <th class="item">
                                    <h2 class="m-0 p-1 text-center">@lang('S.N')</h2>
                                </th>
                                <th class="Hours">
                                    <h2 class="m-0 p-1 text-center">{{ __($title) }}</h2>
                                </th>
                                <th class="subtotal">
                                    <h2 class="m-0 p-1 text-center">@lang('Price')</h2>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="service">
                                <td class="tableitem">
                                    <p class="itemtext">1</p>
                                </td>
                                <td class="tableitem">
                                    <p class="itemtext">{{ __($titleValue) }}</p>
                                </td>
                                <td class="tableitem" style="text-align: right; padding-right:10px;">
                                    <p class="itemtext">
                                        {{ showAmount($transaction->amount) }}
                                </td>
                            </tr>
                            <tr class="tabletitle">
                                <td colspan="3">
                                    <h2 class="me-2 m-0 p-1 text-end">@lang('Total:') {{ showAmount(@$transaction->amount) }}</h2>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!--End Table-->
                <div class="pay-btn-wrapper no-print">
                    <button class="btn btn--base btn-sm" id="dwn" type="button"><i class="las la-print"></i> @lang('Print')</button>
                    <a class="btn btn--base btn-sm" href="{{route('user.home')}}" ><i class="las la-undo"></i> @lang('Dashboard')</a>
                </div>
            </div>
            <!--End InvoiceBot-->
        </div>
        <!--End Invoice-->
    </div>
@endsection

@push('script')
    <script>
        var dwnldBtn = document.getElementById('dwn')
        dwnldBtn.addEventListener('click', function() {
            window.print();
        })
    </script>
@endpush

@push('style')
    <style>
        @import url(https://fonts.googleapis.com/css?family=Roboto:100,300,400,900,700,500,300,100);

        @media print {
            .no-print {
                visibility: hidden;
            }
        }

        @media screen,
        print {
            * {
                margin: 0;
                box-sizing: border-box;
            }

            body {
                background: #E0E0E0;
                font-family: 'Roboto', sans-serif;
                background-image: url('');
                background-repeat: repeat-y;
                background-size: 100%;
                -moz-print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            ::selection {
                background: hsl(var(--base));
                color: #FFF;
            }

            ::moz-selection {
                background: #F31544;
                color: #FFF;
            }

            h1 {
                font-size: 1.5em;
                color: #222;
            }

            h2 {
                font-size: .9em;
            }

            h3 {
                font-size: 1.2em;
                font-weight: 300;
                line-height: 2em;
            }

            p {
                font-size: 12px;
                color: #666;
                line-height: 1.2em;
            }

            #invoiceholder {
                width: 100%;
                padding-top: 50px;
            }

            #invoice {
                position: relative;
                margin: 0 auto;
                width: 700px;
                background: #FFF;
            }

            [id*='invoice-'] {
                border-bottom: 1px solid #EEE;
                padding: 20px;
            }

            #invoice-top {
                min-height: 110px;
                background-color: unset;
            }

            #invoice-mid {
                min-height: 120px;
            }

            .logo {
                float: left;
                height: 60px;
                width: 60px;
            }

            .logo img {
                max-width: 190px;
            }

            .info {
                display: block;
                float: left;
            }

            .title {
                float: right;
            }

            .title p {
                text-align: right;
            }

            #project {
                margin-left: 52%;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            td {
                padding: 5px 0 5px 15px;
                border: 1px solid #EEE
            }

            .tabletitle {
                padding: 5px;
                background: #EEE;
            }

            .service {
                border: 1px solid #EEE;
            }

            .item {
                width: 50px;
            }

            .itemtext {
                font-size: .9em;
            }

            #legalcopy {
                margin-top: 30px;
            }

            #legalcopy .btn {
                display: block;
                width: 100%;
                text-align: center;
                padding: 15px 15px;
                border-radius: 5px;
            }

            #invoice-bot .select {
                padding: 0.625rem 1.25rem;
                width: 100%;
                border: 1px solid #CACACA;
                cursor: pointer;
                color: #464646;
                background-color: #fff;
                height: 3.125rem;
                border-radius: 4px;
            }

            #invoice-bot label {
                color: #535353;
                margin-bottom: 10px;
                font-size: 0.8125rem;
                font-weight: 500;
                display: block;
            }

            #invoice-bot .select:focus {
                outline: none;
            }

            #invoice-bot .select option {
                padding: 0.625rem 0;
                display: block;
                border-top: 1px solid #E5E5E5;
            }

            .pay-btn-wrapper {
                margin-top: 30px;
                margin-bottom: 30px;
                text-align: right;
            }

            .legal {
                width: 70%;
            }

            .unpaid,
            .paid {
                padding: 5px 10px;
                display: inline-block;
                font-size: 14px;
                color: #fff;
            }

            .unpaid {
                background-color: #F31544;
            }

            .paid {
                background-color: #28C76F;
            }
        }
    </style>
@endpush
