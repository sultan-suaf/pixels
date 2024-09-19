@php
    $fileTypesQuery = $fileTypes = App\Models\FileType::active()
        ->withWhereHas('categories', function ($category) {
            $category->where('categories.status', Status::ENABLE);
        })
        ->approvedImageCount();

    $fileTypesCount = (clone $fileTypesQuery)->count();
    $fileTypes = $fileTypesQuery->limit(6)->get();
@endphp

<header class="header-fixed header-primary header-two">
    <div class="custom--container container">
        <div class="header-primary__content">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="logo" href="{{ route('home') }}">
                    @if (gs('banner') == 2)
                        <img class="img-fluid logo__is" src="{{ request()->routeIs('home') ? siteLogo('dark') : siteLogo() }}" alt="logo">
                    @else
                        <img class="img-fluid logo__is" src="{{ siteLogo() }}" alt="logo">
                    @endif
                </a>

                <div class="language_switcher_wrapper d-lg-none me-3">
                    <div class="language_switcher">
                        @if (gs('multi_language'))
                            @php
                                $language = App\Models\Language::all();
                                $selectLang = $language->where('code', config('app.locale'))->first();
                            @endphp
                            <div class="language_switcher__caption">
                                <span class="icon">
                                    <img src="{{ getImage(getFilePath('language') . '/' . @$selectLang->image, getFileSize('language')) }}"
                                        alt="@lang('image')">
                                </span>
                                <span class="text"> {{ __(@$selectLang->name) }} </span>
                            </div>
                            <div class="language_switcher__list">
                                @foreach ($language as $item)
                                    <div class="language_switcher__item    @if (session('lang') == $item->code) selected @endif"
                                        data-value="{{ $item->code }}">
                                        <a href="{{ route('lang', $item->code) }}" class="thumb">
                                            <span class="icon">
                                                <img src="{{ getImage(getFilePath('language') . '/' . $item->image, getFileSize('language')) }}"
                                                    alt="@lang('image')">
                                            </span>
                                            <span class="text"> {{ __($item->name) }}</span>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarToggle" type="button" aria-expanded="false">
                    <span class="menu-toggle"></span>
                </button>
            </nav>
            <div class="navbar-collapse collapse" id="navbarToggle">
                <div class="nav-container">
                    <ul class="list primary-menu">
                        @auth
                            <li class="nav-item pb-lg-0 pb-2">
                                <a class="btn btn--base btn-sm upload--button" href="{{ route('user.image.add') }}"><i
                                        class="las la-cloud-upload-alt"></i> @lang('Upload')</a>
                            </li>
                        @endauth
                        <li class="nav-item has-sub">
                            <a class="primary-menu__link" href="javascript:void(0)">@lang('Explore')</a>
                            <ul class="primary-menu__sub">
                                <li>
                                    <a class="t-link primary-menu__sub-link d-flex gap-2" href="{{ route('members') }}">
                                        <span class="d-inline-block xl-text lh-1">
                                            <i class="las la-user-friends"></i>
                                        </span>
                                        <span class="d-block flex-grow-1">
                                            @lang('Members')
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a class="t-link primary-menu__sub-link d-flex gap-2" href="{{ route('collections') }}">
                                        <span class="d-inline-block xl-text lh-1">
                                            <i class="las la-plus-square"></i>
                                        </span>
                                        <span class="d-block flex-grow-1">
                                            @lang('Collections')
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a class="t-link primary-menu__sub-link d-flex gap-2" href="{{ route('images', ['scope' => 'premium']) }}">
                                        <span class="d-inline-block xl-text lh-1 text--warning">
                                            <i class="las la-crown"></i>
                                        </span>
                                        <span class="d-block flex-grow-1">
                                            @lang('Premium')
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <hr class="primary-menu__divider">
                                </li>
                                <li>
                                    <a class="t-link primary-menu__sub-link"
                                        href="{{ route('images', ['scope' => 'featured']) }}">@lang('Featured')</a>
                                </li>
                                <li>
                                    <a class="t-link primary-menu__sub-link"
                                        href="{{ route('images', ['scope' => 'popular']) }}">@lang('Popular')</a>
                                </li>

                                <li>
                                    <a class="t-link primary-menu__sub-link"
                                        href="{{ route('images', ['scope' => 'most-download']) }}">@lang('Most downloads')</a>
                                </li>
                            </ul>
                        </li>

                        {{-- file type --}}
                        @foreach ($fileTypes as $fileType)
                            <li class="nav-item has-sub">
                                <a class="primary-menu__link" href="javascript:void(0)">{{ __($fileType->name) }}</a>
                                <div class="category-mega-menu primary-menu__sub">
                                    <div class="mega-menu-wrapper">
                                        <div class="mega-menu-wrapper__list category">
                                            <p class="mega-menu-wrapper__title">
                                                {{ __($fileType->name) }} @lang('Categories')
                                            </p>
                                            <div class="content-item">
                                                <ul class="mega-menu__list">
                                                    @foreach ($fileType->categories->take(13) as $category)
                                                        <li class="mega-menu__item"> <a
                                                                href="{{ route('search', ['type' => 'image', 'category' => $category->slug]) }}">
                                                                {{ __($category->name) }} </a> </li>
                                                    @endforeach
                                                    @if ($fileType->categories->count() > 13)
                                                        <li class="mega-menu__item"> <a
                                                                href="{{ route('search', ['type' => 'image', 'category' => '']) }}">
                                                                @lang('All Category') </a> </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="mega-menu-wrapper__list photo">
                                            <a href="{{ route('search', ['fileType' => $fileType->slug, 'type' => 'collection']) }}"
                                                class="mega-menu-wrapper__title collection d-block">
                                                {{ $fileType->name }} @lang('Collections')
                                            </a>
                                            <a class="content-item__thumb"
                                                href="{{ route('search', ['fileType' => $fileType->slug, 'type' => 'collection']) }}">
                                                <img src="{{ getImage(getFilePath('fileTypeCollection') . '/' . $fileType->collection_image, getFileSize('fileTypeCollection')) }}"
                                                    alt="File type">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach

                        @if ($fileTypesCount > 6)
                            <li class="nav-item ">
                                <a class="primary-menu__link"
                                    href="{{ route('search', ['type' => 'image', 'category' => '']) }}">@lang('All Types')</a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a class="primary-menu__link" href="{{ route('plans') }}">@lang('Pricing')</a>
                        </li>
                        @if (gs('contact_system'))
                            <li class="nav-item">
                                <a class="primary-menu__link ps-0" href="{{ route('contact') }}">@lang('Contact')</a>
                            </li>
                        @endif

                        <li class="language_switcher d-none d-lg-block me-3">
                            @if (gs('multi_language'))
                                @php
                                    $language = App\Models\Language::all();
                                    $selectLang = $language->where('code', config('app.locale'))->first();
                                    $currentLang = session('lang')
                                        ? $language->where('code', session('lang'))->first()
                                        : $language->where('is_default', Status::YES)->first();
                                @endphp
                                <div class="language_switcher__caption">
                                    <span class="icon">
                                        <img src="{{ getImage(getFilePath('language') . '/' . @$currentLang->image, getFileSize('language')) }}"
                                            alt="@lang('image')">
                                    </span>
                                    <span class="text"> {{ __(@$selectLang->name) }} </span>
                                </div>
                                <div class="language_switcher__list">
                                    @foreach ($language as $item)
                                        <div class="language_switcher__item    @if (session('lang') == $item->code) selected @endif"
                                            data-value="{{ $item->code }}">
                                            <a href="{{ route('lang', $item->code) }}" class="thumb">
                                                <span class="icon">
                                                    <img src="{{ getImage(getFilePath('language') . '/' . $item->image, getFileSize('language')) }}"
                                                        alt="@lang('image')">
                                                </span>
                                                <span class="text"> {{ __($item->name) }}</span>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </li>

                        @auth
                            <li class="nav-item has-sub user-dropdown">
                                @include($activeTemplate . 'partials.user_profile_menu')
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="signup-btn signup-btn--dark" href="{{ route('user.login') }}">@lang('Login')</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
<div class="overlay"></div>
@push('script')
    <script>
        (function($) {
            "use strict";
            $(".langSel").on("change", function() {
                window.location.href = "{{ route('home') }}/change/" + $(this).val();
            });

            $('.language_switcher > .language_switcher__caption').on('click', function() {
                $(this).parent().toggleClass('open');
            });

            $(document).on('keyup', function(evt) {
                if ((evt.keyCode || evt.which) === 27) {
                    $('.language_switcher').removeClass('open');
                }
            });

            $(document).on('click', function(evt) {
                if ($(evt.target).closest(".language_switcher > .language_switcher__caption").length === 0) {
                    $('.language_switcher').removeClass('open');
                }
            });

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        /* language */
        .language_switcher {
            position: relative;
            padding-right: 20px;
            min-width: max-content;
        }

        @media(max-width: 991px) {
            .language_switcher {
                padding-block: 6px;
                display: inline-flex;
            }

            .language_switcher_wrapper {
                flex: 1;
                text-align: right;
            }
        }

        .language_switcher::after {
            font-family: 'Line Awesome Free';
            content: "\f107";
            font-weight: 900;
            font-size: 14px;
            position: absolute;
            margin: 0;
            color: black;
            top: 50%;
            right: 0;
            -webkit-transform: translateY(-50%);
            transform: translateY(-50%);
            transition: all ease 350ms;
            -webkit-transition: all ease 350ms;
            -moz-transition: all ease 350ms;
        }

        .language_switcher.open:after {
            -webkit-transform: translateY(-50%) rotate(180deg);
            transform: translateY(-50%) rotate(180deg);
        }

        .language_switcher__caption {
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 4px;
            flex-wrap: nowrap;
        }

        .language_switcher__caption .icon {
            position: relative;
            height: 20px;
            width: 20px;
            display: flex;
        }

        .language_switcher__caption .icon img {
            height: 100%;
            width: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .language_switcher__caption .text {
            font-size: 0.875rem;
            font-weight: 500;
            flex: 1;
            color: hsl(var(--dark));
            line-height: 1;
        }

        .language_switcher__list {
            width: 100px;
            border-radius: 4px;
            padding: 0;
            max-height: 105px;
            overflow-y: auto !important;
            background: #fff;
            -webkit-box-shadow: 0px 12px 24px rgba(21, 18, 51, 0.13);
            opacity: 0;
            overflow: hidden;
            -webkit-transition: all 0.15s cubic-bezier(0.25, 0, 0.25, 1.75),
                opacity 0.1s linear;
            transition: all 0.15s cubic-bezier(0.25, 0, 0.25, 1.75), opacity 0.1s linear;
            -webkit-transform: scale(0.85);
            transform: scale(0.85);
            -webkit-transform-origin: 50% 0;
            transform-origin: 50% 0;
            position: absolute;
            top: calc(100% + 18px);
            z-index: -1;
            visibility: hidden;
            border: 1px solid rgb(0 0 0 / 10%);
        }

        .language_switcher__list::-webkit-scrollbar-track {
            border-radius: 3px;
            background-color: hsl(var(--base) / 0.3);
        }

        .language_switcher__list::-webkit-scrollbar {
            width: 3px;
        }

        .language_switcher__list::-webkit-scrollbar-thumb {
            border-radius: 3px;
            background-color: hsl(var(--base) / 0.8);
        }

        .language_switcher__list .text {
            font-size: 0.875rem;
            font-weight: 500;
            color: black;
        }

        .language_switcher.open .language_switcher__list {
            -webkit-transform: scale(1);
            transform: scale(1);
            opacity: 1;
            z-index: 1;
            visibility: visible;
        }

        .language_switcher__item a {
            cursor: pointer;
            padding: 5px;
            border-bottom: 1px solid hsl(var(--heading-color) / 0.2);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .language_switcher__item img {
            height: 20px;
            width: 20px;
            display: block;
            border-radius: 50%;
        }

        .language_switcher__item:last-of-type {
            border-bottom: 0;
        }

        .language_switcher__item.selected {
            background: rgba(36, 60, 187, 0.02);
            pointer-events: none;
        }
    </style>
@endpush
