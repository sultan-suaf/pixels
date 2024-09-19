@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $content = getContent('login.content', true);
    @endphp
    <div class="section login-section"
        style="background-image: url({{ getImage('assets/images/frontend/login/' . @$content->data_values->background_image, '1920x800') }})">
        <div class="section">
            <div class="container">
                <div class="row g-4 justify-content-between align-items-center">
                    <div class="col-lg-6">
                        <img src="{{ getImage('assets/images/frontend/login/' . @$content->data_values->image, '690x550') }}" alt="@lang('images')"
                            class="img-fluid">
                    </div>
                    <div class="col-lg-6 col-xxl-5">
                        <div class="login-form">
                            <h3 class="login-form__title">{{ __(@$content->data_values->form_title) }}</h3>
                            <form action="{{ route('user.login') }}" class="row  verify-gcaptcha" method="post" autocomplete="off">
                                @csrf
                                <div class="col-12 mb-3">
                                    <label class="form-label">@lang('Username')</label>
                                    <input type="text" class="form-control form--control" name="username" required />
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">@lang('Password')</label>
                                    <input type="password" class="form-control form--control" name="password" required />
                                </div>

                                <x-captcha googleCaptchaClass="col-12 mb-3" customCaptchaDiv="col-12 mb-3" customCaptchaCode="mb-3" />

                                <div class="col-sm-6 mb-3">
                                    <div class="form-check form--check">
                                        <input class="form-check-input custom--check" type="checkbox" id="rememberMe" name="remember"
                                            @checked(old('remember')) />
                                        <label class="form-check-label form-label" for="rememberMe">@lang('Remember Me')</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <a href="{{ route('user.password.request') }}"
                                        class="t-link d-block text-sm-end text--base t-link--base form-label lh-1">
                                        @lang('Forgot Password?')
                                    </a>
                                </div>
                                <div class="mb-3 col-12">
                                    <button class="btn btn--lg btn--base w-100 rounded">@lang('LOGIN')</button>
                                </div>
                                <div class="col-12 mb-3">
                                    <p class="m-0 sm-text text-center lh-1">
                                        @lang('Don\'t have an account?') <a href="{{ route('user.register') }}"
                                            class="t-link t-link--base text--base">@lang('Create Account')</a>
                                    </p>
                                </div>
                                @include($activeTemplate . 'partials.social_login')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
