@php
    $text = isset($register) ? 'Register' : 'Login';
    $credentials = gs('socialite_credentials');
@endphp

@if (
    $credentials->google->status == Status::ENABLE ||
        $credentials->facebook->status == Status::ENABLE ||
        $credentials->linkedin->status == Status::ENABLE)
    <p class="text-center sm-text">@lang("Or $text with")</p>
    <ul class="list list--row justify-content-center social-list">
        @if ($credentials->google->status == Status::ENABLE)
            <li><a href="{{ route('user.social.login', 'google') }}" class="t-link social-list__icon"><i class="lab la-google"></i></a></li>
        @endif
        @if ($credentials->facebook->status == Status::ENABLE)
            <li><a href="{{ route('user.social.login', 'facebook') }}" class="t-link social-list__icon"><i class="lab la-facebook-f"></i></a></li>
        @endif
        @if ($credentials->linkedin->status == Status::ENABLE)
            <li><a href="{{ route('user.social.login', 'linkedin') }}" class="t-link social-list__icon"><i class="lab la-linkedin-in"></i></a></li>
        @endif
    </ul>
    </div>
@endif
