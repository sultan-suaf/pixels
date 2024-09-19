@php
    $user = auth()->user();
@endphp
<div class="user-profile" id="profileCoverImage"
    style="background-image:url('{{ getImage(getFilePath('userProfile') . '/' . $member->cover_photo, null, 'cover-photo') }}')">
    <div class="custom--container container">
        <div class="row">
            <div class="col-12">
                <div class="user-profile__content">
                    <div class="user-profile__info">
                        <h2 class="user-profile__info-name">
                            {{ __($member->fullname) }}
                            @if ($member->id == @$user->id)
                                <button class="user-profile__cover-photo updateProfile">
                                    <i class="las la-pen"></i>
                                </button>
                            @endif
                        </h2>
                        <ul class="list list--row user-profile__info-list justify-content-center flex-wrap">
                            <li>
                                <span class="user-profile__info-text">
                                    @lang('Member since') {{ showDateTime($member->created_at, 'F d, Y') }}
                                </span>
                            </li>
                        </ul>
                    </div>
                    <div class="user-profile__img">
                        <img class="user-profile__img-is" id="profilePicture"
                            src="{{ getImage(getFilePath('userProfile') . '/' . $member->image, null, 'user') }}" alt="@lang('member')">
                        @if ($member->id == @$user->id)
                            <input id="profile-picture" name="image" type="file" accept=".png, .jpg, .jpeg" hidden>
                            <label class="user-profile__upload" for="profile-picture">
                                <i class="las la-camera-retro"></i>
                            </label>
                        @endif
                    </div>
                    <div class="user-profile__links">
                        @php
                            $socialLink = App\Models\SocialLink::where('user_id', $member->id)->first();
                        @endphp
                        @if ($socialLink)
                            <ul class="list list--row social-list justify-content-center flex-wrap">
                                @if ($socialLink->facebook)
                                    <li>
                                        <a class="t-link social-list__icon" href="{{ $socialLink->facebook }}">
                                            <i class="lab la-facebook-f"></i>
                                        </a>
                                    </li>
                                @endif

                                @if ($socialLink->twitter)
                                    <li>
                                        <a class="t-link social-list__icon" href="{{ $socialLink->twitter }}">
                                            <i class="lab la-twitter"></i>
                                        </a>
                                    </li>
                                @endif

                                @if ($socialLink->linkedin)
                                    <li>
                                        <a class="t-link social-list__icon" href="{{ $socialLink->linkedin }}">
                                            <i class="lab la-linkedin-in"></i>
                                        </a>
                                    </li>
                                @endif

                                @if ($socialLink->instagram)
                                    <li>
                                        <a class="t-link social-list__icon" href="{{ $socialLink->instagram }}">
                                            <i class="lab la-instagram"></i>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        @endif
                        <ul class="list list--row user-profile__list justify-content-center flex-wrap">
                            <li>
                                <span class="user-profile__icons" data-bs-toggle="tooltip" title="@lang('Images')">
                                    <span class="user-profile__icons-is">
                                        <i class="lar la-image"></i>
                                    </span>
                                    <span class="user-profile__icons-text"> {{ shortNumber($member->images_count) }} </span>
                                </span>
                            </li>
                            <li>
                                <span class="user-profile__icons" data-bs-toggle="tooltip" title="@lang('Likes')">
                                    <span class="user-profile__icons-is">
                                        <i class="las la-thumbs-up"></i>
                                    </span>
                                    <span class="user-profile__icons-text total-like"> {{ shortNumber($member->likes->count()) }} </span>
                                </span>
                            </li>
                            <li>
                                <span class="user-profile__icons" data-bs-toggle="tooltip" title="@lang('Downloads')">
                                    <span class="user-profile__icons-is">
                                        <i class="las la-download"></i>
                                    </span>
                                    <span class="user-profile__icons-text"> {{ shortNumber($member->downloads_count) }} </span>
                                </span>
                            </li>
                            <li>
                                <span class="user-profile__icons" data-bs-toggle="tooltip" title="@lang('Collections')">
                                    <span class="user-profile__icons-is">
                                        <i class="las la-folder-plus"></i>
                                    </span>

                                    @php
                                        $privateCollections = @$user->id == $member->id ? $member->private_collections_count : 0;
                                        $totalCollections = $member->public_collections_count + $privateCollections;
                                    @endphp
                                    <span class="user-profile__icons-text"> {{ shortNumber($totalCollections) }} </span>
                                </span>
                            </li>
                            <li>
                                <span class="user-profile__icons" data-bs-toggle="tooltip" title="@lang('Followers')">
                                    <span class="user-profile__icons-is">
                                        <i class="las la-users"></i>
                                    </span>
                                    <span class="user-profile__icons-text total-follower"> {{ shortNumber($member->followers_count) }} </span>
                                </span>
                            </li>
                        </ul>
                        @if ($member->id != @$user->id)
                            <ul class="list list--row justify-content-center flex-wrap" style="--gap: 0.5rem">
                                <li>
                                    @php
                                        $follow = null;
                                        if (auth()->check()) {
                                            $follow = auth()
                                                ->user()
                                                ->followings->where('following_id', $member->id)
                                                ->first();
                                        }
                                    @endphp
                                    @if ($follow)
                                        <button class="user-profile__btn active unfollow" data-following_id="{{ $member->id }}"
                                            data-followers_route="{{ route('member.followings', $member->username) }}">@lang('Unfollow')</button>
                                    @else
                                        <button class="user-profile__btn follow" data-following_id="{{ $member->id }}"
                                            data-followers_route="{{ route('member.followings', $member->username) }}">@lang('Follow')</button>
                                    @endif
                                </li>
                            </ul>
                        @else
                            <input id="cover-image" name="cover_photo" type="file" accept=".png, .jpg, .jpeg" hidden>
                            <label class="user-profile__cover-photo" for="cover-image">
                                <i class="las la-camera-retro"></i>
                            </label>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Tab Menu  -->
<div class="tab-menu">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="tab-menu__content">
                    <a class="tab-menu__link {{ menuActive('member.images') }}" href="{{ route('member.images', $member->username) }}">
                        @lang('IMAGES') </a>
                    <a class="tab-menu__link {{ menuActive('member.followers.followings') }}"
                        href="{{ route('member.followers.followings', $member->username) }}"> @lang('FOLLOWERS & FOLLOWINGS') </a>
                    <a class="tab-menu__link {{ menuActive('member.collections') }}" href="{{ route('member.collections', $member->username) }}">
                        @lang('COLLECTION') </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Tab Menu End -->

@push('modal')
    @include($activeTemplate . 'partials.login_modal')
    @auth
        @include($activeTemplate . 'partials.profile_modal')
    @endauth
@endpush

@push('script')
    <script>
        "use strict";

        (function($) {
            $('#cover-image').on('change', function(e) {
                let data = new FormData();
                data.append('cover_photo', e.target.files[0]);
                if (!['image/jpg', 'image/jpeg', 'image/png'].includes(e.target.files[0].type)) {
                    notify('error', `@lang('File type is not supported')`);
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: "{{ route('user.cover.update') }}",
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    enctype: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            document.getElementById('profileCoverImage').style.backgroundImage = "url('" + response.cover_photo +
                                "')";
                            notify('success', response.success);
                        } else {
                            notify('error', response.error);
                        }
                    }
                });
            })

            $('#profile-picture').on('change', function(e) {
                let data = new FormData();
                data.append('image', e.target.files[0]);
                if (!['image/jpg', 'image/jpeg', 'image/png'].includes(e.target.files[0].type)) {
                    notify('error', `@lang('File type is not supported')`);
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: "{{ route('user.profile.picture.update') }}",
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    enctype: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            document.getElementById('profilePicture').src = response.image;
                            notify('success', response.success);
                        } else {
                            notify('error', response.error);
                        }
                    }
                });
            })

            $('.updateProfile').on('click', function() {
                let modal = $('#profileModal');
                modal.modal('show');
            })
        })(jQuery);

        let followRoutes = {
            updateFollow: "{{ route('user.follow.update') }}"
        }

        let followParams = {
            loggedStatus: @json(Auth::check()),
            csrfToken: "{{ csrf_token() }}",
            appendStatus: 1
        }
    </script>
    <script src="{{ asset($activeTemplateTrue . 'js/follow.js') }}"></script>
@endpush
