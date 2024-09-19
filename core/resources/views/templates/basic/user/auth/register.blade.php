@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @if (gs('registration'))
        @php
            $content = getContent('register.content', true);
            $policyPages = getContent('policy_pages.element', false, null, true);
        @endphp
        <div class="section login-section"
            style="background-image: url({{ getImage('assets/images/frontend/register/' . @$content->data_values->background_image, '1920x800') }})">
            <div class="section">
                <div class="container">
                    <div class="row g-4 justify-content-between align-items-center">
                        <div class="mb-3 col-lg-6">
                            <img src="{{ getImage('assets/images/frontend/register/' . @$content->data_values->image, '690x550') }}" alt="@lang('images')"
                                class="img-fluid">
                        </div>
                        <div class="mb-3 col-lg-6 ">
                            <div class="login-form">
                                <h3 class="login-form__title">{{ __(@$content->data_values->form_title) }}</h3>
                                <form action="{{ route('user.register') }}" class="row verify-gcaptcha" method="post" autocomplete="off">
                                    @csrf
                                    @if (session()->has('reference'))
                                        <div class="mb-3 col-md-12">
                                            <label for="referenceBy" class="form-label">@lang('Reference by')</label>
                                            <input type="text" name="referBy" id="referenceBy" class="form-control form--control"
                                                value="{{ session()->get('reference') }}" readonly>
                                        </div>
                                    @endif

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">@lang('First Name')</label>
                                        <input type="text" class="form-control form--control" name="firstname" value="{{ old('firstname') }}" required>

                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">@lang('Last Name')</label>
                                        <input type="text" class="form-control form--control" name="lastname" value="{{ old('lastname') }}" required>

                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">@lang('E-Mail Address')</label>
                                        <input type="email" class="form-control form--control checkUser" name="email" value="{{ old('email') }}"
                                            required>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Password')</label>
                                            <input type="password"
                                                class="form-control form--control @if (gs('secure_password')) secure-password @endif"
                                                name="password" required>
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Confirm Password')</label>
                                            <input type="password" class="form-control form--control" name="password_confirmation" required>
                                        </div>
                                    </div>

                                    <x-captcha googleCaptchaClass="mb-3" customCaptchaDiv="mb-3 col-12" customCaptchaCode="mb-3" />

                                    @if (gs('agree'))
                                        <div class="mb-3 col-sm-12">
                                            <div class="form-check form--check">
                                                <input class="form-check-input custom--check" type="checkbox" id="agree" name="agree"
                                                    @checked(old('agree')) required />
                                                <label class="form-check-label form-label" for="agree">
                                                    @lang('I agree with')
                                                    @foreach ($policyPages as $policy)
                                                        <a class="t-link t-link--base text--base" href="{{ route('policy.pages', $policy->slug) }}"
                                                            target="_blank">{{ __($policy->data_values->title) }}</a>
                                                        @if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </label>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <button class="btn btn--lg btn--base w-100 rounded">@lang('REGISTER')</button>
                                    </div>

                                    <div class="mb-3">
                                        <p class="m-0 sm-text text-center lh-1">
                                            @lang('Already have an account?') <a href="{{ route('user.login') }}"
                                                class="t-link t-link--base text--base">@lang('Login Now')</a>
                                        </p>
                                    </div>
                                    @include($activeTemplate . 'partials.social_login', [($register = 'true')])
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade custom--modal" id="existModalCenter" tabindex="-1" role="dialog" aria-labelledby="existModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                        <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </span>
                    </div>
                    <div class="modal-body">
                        <p class="text-center">@lang('You already have an account please Login ')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
                        <a href="{{ route('user.login') }}" class="btn btn--base btn-sm">@lang('Login')</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        @include($activeTemplate . 'partials.registration_disabled')
    @endif
@endsection

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';

                var data = {
                    email: value,
                    _token: token
                }

                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $('#existModalCenter').modal('show');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
