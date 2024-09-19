@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $content = getContent('contact.content', true);

    @endphp
    <div class="section login-section" style="background-image: url({{ getImage('assets/images/frontend/contact/' . @$content->data_values->background_image, '1920x800') }})">
        <div class="section">
            <div class="container">
                <div class="row justify-content-center">

                    <div class="col-sm-10">
                        <div class="row g-0 justify-content-center flex-wrap-reverse">
                            <div class="col-lg-6">
                                <div class="contact__left">
                                    <div class="contact-item">
                                        <div class="contact-item__content">
                                            <span class="contact-item__icon"><i class="las la-phone"></i></span>
                                            <h4 class="contact-item__title">@lang('CALL US') </h4>
                                        </div>
                                        <a class="contact-item__number" href="tel:{{ @$content->data_values->mobile }}">{{ @$content->data_values->mobile }}</a>
                                    </div>
                                    <div class="contact-item">
                                        <div class="contact-item__content">
                                            <span class="contact-item__icon"><i class="las la-map-marker-alt"></i></span>
                                            <h4 class="contact-item__title">@lang('ADDRESS') </h4>
                                        </div>
                                        <p class="contact-item__text">{{ __(@$content->data_values->address) }}</p>
                                    </div>
                                    <div class="contact-item">
                                        <div class="contact-item__content">
                                            <span class="contact-item__icon"><i class="las la-envelope"></i></span>
                                            <h4 class="contact-item__title"> @lang('EMAIL ADDRESS') </h4>
                                        </div>
                                        <a class="contact-item__text" href="mailto:{{ @$content->data_values->email }}">{{ @$content->data_values->email }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="login-form">
                                    <h3 class="title">{{ __(@$content->data_values->form_title) }}</h3>
                                    <form class="verify-gcaptcha" method="post" action="">
                                        @csrf
                                        <div class="form-group">
                                            <label class="form-label">@lang('Name')</label>
                                            <input class="form-control form--control" name="name" type="text" value="{{ old('name', @$user->fullname) }}" @if ($user && $user->profile_complete) readonly @endif required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">@lang('Email')</label>
                                            <input class="form-control form--control" name="email" type="email" value="{{ old('email', @$user->email) }}" @if ($user) readonly @endif required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">@lang('Subject')</label>
                                            <input class="form-control form--control" name="subject" type="text" value="{{ old('subject') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">@lang('Message')</label>
                                            <textarea class="form-control form--control" name="message" wrap="off" required>{{ old('message') }}</textarea>
                                        </div>
                                        <div class="mt-3">
                                            <x-captcha />

                                        </div>
                                        <div class="form-group mt-3">
                                            <button class="btn btn--base w-100 contact--button" type="submit">@lang('Submit')</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .form-group {
            margin-bottom: 1rem;
        }

        .contact-item {
            margin-bottom: 40px;
        }

        .contact-item:last-child {
            margin-bottom: 0;
        }

        .contact-item__content {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .contact-item__icon {
            font-size: 35px;
            margin-right: 15px;
            line-height: 1;
            color: hsl(var(--base));
        }

        .contact-item__title {
            display: inline-block;
            margin: 0;
        }

        .login-form .title {
            margin-top: 0;
            text-align: center;
        }

        .contact__left {
            padding: 90px 50px;
            background-color: hsl(var(--base)/.1);
            height: 100%;
        }

        @media (max-width:991px) {
            .contact__left {
                padding: 60px 50px;
            }
        }

        .login-form {
            box-shadow: none;
            border-radius: 0 !important;
            background-color: hsl(var(--white));
            padding: 50px !important;
        }

        @media (max-width:574px) {
            .login-form {
                padding: 35px !important;
            }

            .contact__left {
                padding: 50px 35px;
            }

            .contact-item {
                margin-bottom: 30px;
            }
        }

        .contact--button {
            height: 43px;
        }

        .contact-item__number,
        .contact-item__text {
            color: unset;
        }
    </style>
@endpush
