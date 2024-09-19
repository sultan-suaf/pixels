@extends($activeTemplate.$masterBlade)

@section('content')
@if ($masterBlade == 'layouts.frontend')
<div class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-9">
                @endif
                <div class="card custom--card">
                    <div class="card-header card-header-bg">
                        <h5 class="card-title">{{__($pageTitle)}}</h5>
                    </div>
                    <div class="card-body ">

                        <form action="    @if(auth()->check()) {{ route('user.deposit.manual.update') }} @else {{ route('donation.manual.update') }} @endif" method="POST" class="disableSubmission" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-primary">
                                        <p class="mb-0"><i class="las la-info-circle"></i> @lang('You are requesting') <b>{{ showAmount($data['amount'])  }}</b> @lang('to deposit.') @lang('Please pay')
                                            <b>{{showAmount($data['final_amount'],currencyFormat:false) .' '.$data['method_currency'] }} </b> @lang('for successful payment.')</p>
                                    </div>

                                    <div class="mb-2">@php echo  $data->gateway->description @endphp</div>

                                </div>

                                <x-viser-form identifier="id" identifierValue="{{ $gateway->form_id }}" />

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn--base w-100">@lang('Pay Now')</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @if ($masterBlade == 'layouts.frontend')
            </div>
        </div>
    </div>
</div>
@endif
@endsection
