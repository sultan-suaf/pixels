@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="card custom--card">
        <h5 class="card-header">
            <span class="card-header__icon">
                <i class="las la-key"></i>
            </span>
            {{ __($pageTitle) }}
        </h5>
        <div class="card-body">
            <form action="" method="post" class="row g-3">
                @csrf
                <div class="form-group col-md-12">
                    <label class="form-label">@lang('Current Password')</label>
                    <input type="password" class="form-control form--control" name="current_password" required autocomplete="current-password">
                </div>
                <div class="form-group col-md-12">
                    <label class="form-label">@lang('Password')</label>
                    <input type="password" class="form-control form--control @if(gs('secure_password')) secure-password @endif" name="password" required autocomplete="current-password">
                </div>
                <div class="form-group col-12">
                    <label class="form-label">@lang('Confirm Password')</label>
                    <input type="password" class="form-control form--control" name="password_confirmation" required autocomplete="current-password">
                </div>
                <div class="form-group col-12">
                    <button type="submit" class="btn btn--lg btn--base w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>

@endsection
@if(gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
