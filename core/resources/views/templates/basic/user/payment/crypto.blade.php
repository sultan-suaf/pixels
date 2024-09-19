@extends($activeTemplate . $masterBlade)

@section('content')
@if ($masterBlade == 'layouts.frontend')
<div class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-9">
                @endif
                <div class="card card-deposit text-center">
                    <div class="card-header card-header-bg">
                        <h3>@lang('Payment Preview')</h3>
                    </div>
                    <div class="card-body card-body-deposit text-center">
                        <h4 class="my-2"> @lang('PLEASE SEND EXACTLY') <span class="text--success"> {{ $data->amount }}</span> {{__($data->currency)}}</h4>
                        <h5 class="mb-2">@lang('TO') <span class="text--success"> {{ $data->sendto }}</span></h5>
                        <img src="{{$data->img}}" alt="Image">
                        <h4 class="text-white bold my-4">@lang('SCAN TO SEND')</h4>
                    </div>
                </div>
                @if ($masterBlade == 'layouts.frontend')
            </div>
        </div>
    </div>
</div>
@endif
@endsection
