@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @include($activeTemplate . 'partials.member_nav')
    <div class="user-gallery d-flex align-items-center">
        <div class="container custom--container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-xl-5">
                    @if ($member->followers->count() || $member->followings->count())
                        <div class="card custom--card">
                            <div class="card-body">
                                <div class="row g-4">
                                    @if ($member->followers->count())
                                        <div class="col-12 followers-div">
                                            <h6 class="mt-0">@lang('FOLLOWERS')</h6>
                                            <ul class="list list--row flex-wrap followers-ul" style="--gap: 0.5rem">
                                                @include($activeTemplate . 'partials.follower_following_avatar', [
                                                    'users' => $member->followers,
                                                    'relation' => 'followerProfile',
                                                ])
                                            </ul>
                                            @if ($member->followers_count > 21)
                                                <a href="{{ route('member.followers', $member->username) }}" class="follower-route">@lang('see all')</a>
                                            @endif
                                        </div>
                                    @endif
                                    @if ($member->followings->count())
                                        <div class="col-12">
                                            <h6 class="mt-0">@lang('FOLLOWINGS')</h6>
                                            <ul class="list list--row flex-wrap followings-ul" style="--gap: 0.5rem">
                                                @include($activeTemplate . 'partials.follower_following_avatar', [
                                                    'users' => $member->followings,
                                                    'relation' => 'followingProfile',
                                                ])
                                            </ul>
                                            @if ($member->followings_count > 21)
                                                <a href="{{ route('member.followings', $member->username) }}">@lang('see all')</a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center">
                            <img src="{{ getImage('assets/images/empty_message.png') }}" alt="@lang('Image')">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
