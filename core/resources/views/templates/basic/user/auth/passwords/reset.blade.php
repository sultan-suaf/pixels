@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @include($activeTemplate . 'partials.breadcrumb')
    <div class="section--xl">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-7 col-xl-5">
                    <div class="card custom--card">
                        <div class="card-body">
                            <div class="mb-4">
                                <p>@lang('Your account is verified successfully. Now you can change your password. Please enter a strong password and don\'t share it with anyone.')</p>
                            </div>
                            <form method="POST" action="{{ route('user.password.update') }}">
                                @csrf
                                <input type="hidden" name="email" value="{{ $email }}">
                                <input type="hidden" name="token" value="{{ $token }}">
                                <div class="col-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Password')</label>
                                        <input type="password" class="form-control form--control @if(gs('secure_password')) secure-password @endif" name="password" required>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">@lang('Confirm Password')</label>
                                    <input type="password" class="form-control form--control" name="password_confirmation" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="base-btn w-100"> @lang('Submit')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
