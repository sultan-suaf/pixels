@php
    $categories = App\Models\Category::active()->limit(5)->get();
@endphp
<div class="header-top">
    <div class="container-fluid">
        <div class="header-top__content">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="logo" href="{{ route('home') }}">
                    <img class="img-fluid logo__is" src="{{ siteLogo() }}" alt="@lang('Logo')">
                </a>
                <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarToggle" type="button" aria-expanded="false">
                    <span class="menu-toggle"></span>
                </button>
            </nav>
            <div class="collapse navbar-collapse" id="navbarToggle">
                <div class="nav-container">
                    <!-- Primary Menu  -->
                    <ul class="list primary-menu">
                        <li class="nav-item">
                            <a class="primary-menu__link" href="{{ route('home') }}"> @lang('Home') </a>
                        </li>
                        <li class="nav-item has-sub">
                            <a class="primary-menu__link" href="javascript:void(0)">@lang('Explore')</a>
                            <ul class="primary-menu__sub">
                                <li>
                                    <a class="t-link primary-menu__sub-link" href="{{ route('members') }}">
                                        <span class="d-inline-block xl-text lh-1">
                                            <i class="las la-user-friends"></i>
                                        </span>
                                        <span class="d-block flex-grow-1">
                                            @lang('Members')
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a class="t-link primary-menu__sub-link" href="{{ route('collections') }}">
                                        <span class="d-inline-block xl-text lh-1">
                                            <i class="las la-plus-square"></i>
                                        </span>
                                        <span class="d-block flex-grow-1">
                                            @lang('Collections')
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a class="t-link primary-menu__sub-link" href="{{ route('images', ['scope' => 'premium']) }}">
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

                        <li class="nav-item">
                            <a class="primary-menu__link" href="{{ route('plans') }}">@lang('Pricing')</a>
                        </li>

                        @auth
                            <li class="nav-item has-sub user-dropdown user-dropdown--sm ms-lg-auto">
                                @include($activeTemplate . 'partials.user_profile_menu')
                            </li>
                        @else
                            <li class="nav-item ms-lg-auto">
                                <a class="primary-menu__link" href="{{ route('user.login') }}">@lang('Login')</a>
                            </li>
                            <li class="nav-item">
                                <a class="signup-btn my-2 ms-3 ms-lg-0" href="{{ route('user.register') }}">@lang('Sign Up')</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="header-secondary">
    <div class="container-fluid">
        <div class="primary-search">
            <a class="logo" href="{{ route('home') }}">
                <img class="img-fluid logo__is" src="{{ siteLogo() }}" alt="@lang('logo')">
            </a>
            <div class="search-bar">
                <div class="search-bar__icon">
                    <i class="las la-search"></i>
                </div>
                <input class="form-control form--control search-bar__input search-input" type="search" value="{{ request()->filter ?? '' }}"
                    placeholder="@lang('Search anything')..">
            </div>
            <button class="primary-search__filter" type="button">
                <i class="las la-sliders-h"></i>
            </button>
        </div>
    </div>
</div>
