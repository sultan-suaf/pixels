@php
    $user = auth()->user();
@endphp

<a href="javascript:void(0)" class="primary-menu__link p-0">
    <span class="custom-dropdown__user">
        <img src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image, null, 'user') }}" alt=""
            class="custom-dropdown__user-img">
    </span>
    <span class="ps-3 d-lg-none">{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</span>
</a>
<ul class="primary-menu__sub">
    <li>
        <a href="{{ route('user.home') }}" class="t-link primary-menu__sub-link d-flex gap-2">
            <span class="d-inline-block xl-text lh-1">
                <i class="las la-home"></i>
            </span>
            <span class="d-block flex-grow-1">
                @lang('Dashboard')
            </span>
        </a>
    </li>

    <li>
        <a href="{{ route('user.image.all') }}" class="t-link primary-menu__sub-link d-flex gap-2">
            <span class="d-inline-block xl-text lh-1">
                <i class="las la-image"></i>
            </span>
            <span class="d-block flex-grow-1">
                @lang('My Images')
            </span>
        </a>
    </li>
    <li>
        <a href="{{ route('user.collection.all') }}" class="t-link primary-menu__sub-link d-flex gap-2">
            <span class="d-inline-block xl-text lh-1">
                <i class="las la-folder-plus"></i>
            </span>
            <span class="d-block flex-grow-1">
                @lang('My Collections')
            </span>
        </a>
    </li>
    <li>
        <a href="{{ route('member.images', $user->username) }}" class="t-link primary-menu__sub-link d-flex gap-2">
            <span class="d-inline-block xl-text lh-1">
                <i class="las la-user-circle"></i>
            </span>
            <span class="d-block flex-grow-1">
                @lang('My Profile')
            </span>
        </a>
    </li>
    <li>
        <a href="{{ route('user.logout') }}" class="t-link primary-menu__sub-link d-flex gap-2">
            <span class="d-inline-block xl-text lh-1">
                <i class="las la-sign-out-alt"></i>
            </span>
            <span class="d-block flex-grow-1">
                @lang('Logout')
            </span>
        </a>
    </li>
</ul>
