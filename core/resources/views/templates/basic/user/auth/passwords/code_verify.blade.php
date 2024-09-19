@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @include($activeTemplate . 'partials.breadcrumb')
    <div class="section--xl">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-7 col-xl-5">
                    <div class="d-flex justify-content-center">
                        <div class="verification-code-wrapper">
                            <div class="verification-area">
                                <form action="{{ route('user.password.verify.code') }}" method="POST" class="submit-form">
                                    @csrf
                                    <p class="verification-text">@lang('A 6 digit verification code sent to your email address') : {{ showEmailAddress($email) }}</p>
                                    <input type="hidden" name="email" value="{{ $email }}">

                                    @include($activeTemplate . 'partials.verification_code')

                                    <button type="submit" class="base-btn w-100">@lang('Submit')</button>

                                    <div class="col-12">
                                        @lang('Please check including your Junk/Spam Folder. if not found, you can')
                                        <a class="t-link t-link--base text--base" href="{{ route('user.password.request') }}">@lang('Try to send again')</a>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
