@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-header">
            <h5 class="card-title">@lang('KYC Form')</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('user.kyc.submit') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <x-viser-form identifier="act" identifierValue="kyc" />
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn--base w-100 h-45">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('script')
    <script>
        "use strict";
        $.each($('.form-group'), function(i, element) {
            $(element).addClass('col-12 mb-3').removeClass('form-group');
        });
    </script>
@endpush
