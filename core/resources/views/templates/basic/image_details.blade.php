@extends($activeTemplate . 'layouts.frontend')
@section('content')
@php
$user = auth()->user();
@endphp
<div class="photo-page">
    <div class="container">
        <div class="row g-4 gy-md-0">
            <div class="col-md-7 col-lg-8 col-xl-9">
                <div class="photo-view item-details-video">
                    @if($image->video)
                    <video src="{{ asset(getFilePath('stockVideo') . '/' . $image->video) }}" autoplay muted
                        loop></video>
                    @else
                    <img class="photo-view__img" src="{{ imageUrl(getFilePath('stockImage'), $image->image_name) }}"
                        alt="@lang('Image')">
                    @endif
                </div>
                <div class="photo-info">
                    @php echo $image->description @endphp
                </div>
                <div class="related-category">
                    <h5 class="related-category__title">@lang('Keywords')</h5>
                    <ul class="list list--row related-category__list flex-wrap">
                        @foreach ($image->tags as $tag)
                        <li>
                            <a class="search-category__btn"
                                href="{{ route('search', ['type' => 'image', 'tag' => $tag]) }}">{{ __($tag) }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="col-md-5 col-lg-4 col-xl-3">
                <div class="user align-items-center">
                    <div class="user__img user__img--lg">
                        <img class="user__img-is"
                            src="{{ getImage(getFilePath('userProfile') . '/' . @$image->user->image, null, 'user') }}"
                            alt="@lang('image')">
                    </div>
                    <div class="user__content">
                        <span class="user__name"><a href="{{ route('member.images', @$image->user->username) }}">{{
                                __(@$image->user->fullname) }}</a>/{{ shortNumber($image->user->images->count()) }}
                            @lang('images')</span>
                        @if ($image->user_id != @$user->id)
                        <ul class="list list--row flex-wrap" style="--gap: 0.5rem">
                            @php
                            $liked = null;
                            $followed = null;

                            if ($user) {
                            $liked = $user->likes->where('image_id', $image->id)->first();
                            $followed = $user->followings->where('following_id', $image->user->id)->first();
                            }
                            @endphp

                            @if ($liked)
                            <li>
                                <button class="follow-btn unlike-btn active" data-has_icon="0"
                                    data-image="{{ $image->id }}" type="button">@lang('Unlike')</button>
                            </li>
                            @else
                            <li>
                                <button class="follow-btn like-btn" data-has_icon="0" data-image="{{ $image->id }}"
                                    type="button">@lang('Like')</button>
                            </li>
                            @endif

                            @if ($followed)
                            <li>
                                <button class="follow-btn unfollow active" data-following_id="{{ $image->user->id }}"
                                    type="button">@lang('Unfollow')</button>
                            </li>
                            @else
                            <li>
                                <button class="follow-btn follow" data-following_id="{{ $image->user->id }}"
                                    type="button">@lang('Follow')</button>
                            </li>
                            @endif
                        </ul>
                        @endif
                    </div>
                </div>

                <div class="photo-details my-4">
                    <div class="photo-details__head">
                        <div class="photo-details__title">
                            <span class="photo-details__icon">
                                <i class="las la-camera-retro"></i>
                            </span>
                            <span class="photo-details__title-link">{{ __($image->title) }} </span>

                            @if (@$user->id == @$image->user_id)
                            <a class="btn btn-sm btn--base" data-bs-toggle="tooltip"
                                href="{{ route('user.image.edit', $image->id) }}" title="Edit">
                                <i class="las la-pen"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    <div class="photo-details__body">
                        <ul class="list" style="--gap: 0.5rem">
                            <li class="py-2">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <span class="d-inline-block sm-text lh-1"> @lang('Image type') </span>
                                    <span class="d-inline-block sm-text lh-1">
                                        @if ($image->extensions)
                                        {{ __(strtoupper(implode(', ', $image->extensions))) }}
                                        @endif
                                    </span>
                                </div>
                            </li>

                            <li class="py-2">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <span class="d-inline-block sm-text lh-1"> @lang('Published') </span>
                                    <span class="d-inline-block sm-text lh-1">
                                        {{ showDateTime($image->upload_date, 'F d, Y') }}
                                    </span>
                                </div>
                            </li>
                            <li class="py-2">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <span class="d-inline-block sm-text lh-1"> @lang('Views') </span>
                                    <span class="d-inline-block sm-text lh-1"> {{ $image->total_view }} </span>
                                </div>
                            </li>
                            <li class="py-2">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <span class="d-inline-block sm-text lh-1"> @lang('Downloads') </span>
                                    <span class="d-inline-block sm-text lh-1"> {{ $image->totalDownloads }} </span>
                                </div>
                            </li>

                        </ul>
                    </div>
                </div>

                <div class="photo-details my-4">
                    <div class="photo-details__body">

                        @foreach ($imageFiles as $key => $imageFile)
                        <div
                            class="d-flex align-items-center justify-content-between {{ !$key ? '' : 'border-top' }} py-2">
                            <div>
                                <span class="d-inline-block sm-text lh-1"> {{ $imageFile->resolution }} </span>
                                <span class="px-2">|</span>
                                @if ($imageFile->is_free == Status::PREMIUM)
                                <span class="d-inline-block sm-text lh-1">{{ showAmount($imageFile->price) }}
                                </span>
                                @else
                                <span class="d-inline-block sm-text lh-1"> @lang('Free') </span>
                                @endif
                            </div>
                            <div class="d-flex">
                                @php
                                $downloadActionClass = 'login-btn';
                                if (auth()->check() || $imageFile->is_free) {
                                $downloadActionClass = 'confirmationBtn';
                                }
                                @endphp
                                <button class="btn btn--base btn-sm {{ $downloadActionClass }}"
                                    data-action="{{ route('image.download', encrypt($imageFile->id)) }}"
                                    data-question="@lang('Are you sure to download of this file ?')" type="button">
                                    <i class="las la-download"></i>
                                </button>
                                @if (@$user->id == @$image->user_id)
                                @if ($imageFile->status == Status::ENABLE)
                                <button class="btn btn btn-sm btn-success confirmationBtn ms-2"
                                    data-action="{{ route('user.image.file.status', $imageFile->id) }}"
                                    data-question="@lang('Are you sure to change status?')" data-bs-toggle="tooltip"
                                    type="button" title="@lang('Make disabled')">
                                    <i class="la la-eye"></i>
                                </button>
                                @else
                                <button class="btn btn btn-sm btn-danger confirmationBtn ms-2"
                                    data-action="{{ route('user.image.file.status', $imageFile->id) }}"
                                    data-question="@lang('Are you sure to change status?')" data-bs-toggle="tooltip"
                                    type="button" title="@lang('Make enabled')">
                                    <i class="la la-eye-slash"></i>
                                </button>
                                @endif
                                @endif

                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if (gs('donation_module') && @$imageFiles?->where('is_free', 1)->isNotEmpty() && $image->user_id !=
                @$user->id)
                <div class="photo-details my-4">
                    <div class="photo-details__body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="d-inline-block sm-text lh-1"> {{ __(gs('donation_setting')?->subtitle)
                                    }}</span>
                            </div>
                            <div class="d-flex">
                                <button class="btn btn--base btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#donationModal" type="button">
                                    <i class="las la-hand-holding-usd"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif



                <div class="mt-4">
                    <h5 class="mb-2 mt-0">@lang('Share')</h5>
                    <ul class="list list--row social-list">
                        <li>
                            <a class="t-link social-list__icon"
                                href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                target="_blank">
                                <i class="lab la-facebook-f"></i>
                            </a>
                        </li>
                        <li>
                            <a class="t-link social-list__icon"
                                href="https://twitter.com/intent/tweet?text={{ $image->title }}&amp;url={{ urlencode(url()->current()) }}"
                                target="_blank">
                                <i class="lab la-twitter"></i>
                            </a>
                        </li>
                        <li>
                            <a class="t-link social-list__icon"
                                href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title={{ $image->title }}&amp;summary={{ $image->title }}"
                                target="_blank">
                                <i class="lab la-linkedin-in"></i>
                            </a>
                        </li>
                        <li>
                            <a class="t-link social-list__icon"
                                href="http://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&description={{ $image->description }}"
                                target="_blank">
                                <i class="lab la-pinterest-p"></i>
                            </a>

                        </li>
                    </ul>
                </div>
            </div>
            @if ($relatedImages->count())
            <div class="col-12">
                <div class="related-photo">
                    <h5 class="related-photo__title">@lang('Related Photos')</h5>
                    @include($activeTemplate . 'partials.image_grid', [
                    'images' => $relatedImages,
                    'class' => 'gallery',
                    ])
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="photo-modal">
        <div class="photo-modal__img">
            <img class="photo-modal__image" src="{{ imageUrl(getFilePath('stockImage'), $image->thumb) }}" alt="image">
        </div>
        <div class="photo-modal__content">
            <h6 class="photo-modal__title">@lang('Give Thanks!')</h6>
            <p class="photo-modal__description">
                @lang('Give thanks to ')@<span class="fw-bold">{{ @$image->user->username }}</span> @lang('for sharing
                this photo, the easiest way, sharing on social network')
            </p>
            <ul class="list list--row social-list">
                <li>
                    <a class="t-link social-list__icon"
                        href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                        target="_blank">
                        <i class="lab la-facebook-f"></i>
                    </a>
                </li>
                <li>
                    <a class="t-link social-list__icon"
                        href="https://twitter.com/intent/tweet?text={{ $image->title }}&amp;url={{ urlencode(url()->current()) }}"
                        target="_blank">
                        <i class="lab la-twitter"></i>
                    </a>
                </li>
                <li>
                    <a class="t-link social-list__icon"
                        href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title={{ $image->title }}&amp;summary={{ $image->title }}"
                        target="_blank">
                        <i class="lab la-linkedin-in"></i>
                    </a>
                </li>
                <li>
                    <a class="t-link social-list__icon"
                        href="http://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&description={{ $image->description }}"
                        target="_blank">
                        <i class="lab la-pinterest-p"></i>
                    </a>

                </li>
            </ul>
            <button class="photo-modal__close" type="button">
                <i class="las la-times"></i>
            </button>
        </div>
    </div>
</div>


@if (gs('donation_module') && @$imageFiles?->where('is_free', 1)->isNotEmpty() && $image->user_id != @$user->id)

@push('modal')
<!--  Purchase Modal  -->
<div class="modal custom--modal fade" id="donationModal" aria-hidden="true" aria-labelledby="title" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-12">
                    <h5 class="payment-card-title"> @lang('Buy ') <i>{{ __(@$image->user->fullname) }}</i> @lang('a') {{
                        keyToTitle(__(gs('donation_setting')?->item)) }}</h5>
                </div>
            </div>
            <div class="modal-body">
                <form action="{{ route('donation.insert', $image->id) }}" method="post" class="deposit-form">
                    @csrf
                    <input type="hidden" name="currency">
                    <div class="gateway-card mt-0">
                        <div class="row justify-content-center gy-sm-4 gy-3">

                            <div class="card__box">
                                <div class="d-flex align-items-center justify-center">
                                    <span class="icon">@php echo gs('donation_setting')?->icon @endphp </span>
                                    <span class="icon"><i class="las la-times"></i></span>
                                    <input class="form--control form-control" id="donation" name="donation_quantity"
                                        data-donation_amount="{{ gs('donation_setting')?->amount }}" type="text"
                                        type="number" value="1" min="1" max="9">
                                    <nav aria-label="Page navigation example">
                                        <ul class="donation-quantity">

                                            <li class="quantity-item"><button class="quantity-button active"
                                                    data-donation_amount="{{ gs('donation_setting')?->amount }}"
                                                    type="button">@lang('1')</button></li>
                                            <li class="quantity-item"><button class="quantity-button"
                                                    data-donation_amount="{{ gs('donation_setting')?->amount }}"
                                                    type="button">@lang('3')</button></li>
                                            <li class="quantity-item"><button class="quantity-button"
                                                    data-donation_amount="{{ gs('donation_setting')?->amount }}"
                                                    type="button">@lang('5')</button></li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="payment-system-list is-scrollable gateway-option-list">
                                    @auth
                                    <label for="account_balance" class="payment-item gateway-option">
                                        <div class="payment-item__info">
                                            <span class="payment-item__check"></span>
                                            <span class="payment-item__name">@lang('Account Balance')
                                                ({{ showAmount(auth()->user()->balance) }})</span>
                                        </div>
                                        <div class="payment-item__thumb">
                                            <img class="payment-item__thumb-img"
                                                src="{{ getImage(null, avatar: true) }}" alt="@lang('account-balance')">
                                        </div>
                                        <input class="payment-item__radio gateway-input" id="account_balance" hidden
                                            type="radio" name="gateway" value="balance" @if (old('gateway'))
                                            @checked(old('gateway')=='wallet' ) @endif>
                                    </label>
                                    @endauth

                                    @foreach ($gatewayCurrency as $data)
                                    <label for="{{ titleToKey($data->name) }}"
                                        class="payment-item @if ($loop->index > 4) d-none @endif gateway-option">
                                        <div class="payment-item__info">
                                            <span class="payment-item__check"></span>
                                            <span class="payment-item__name">{{ __($data->name) }}</span>
                                        </div>
                                        <div class="payment-item__thumb">
                                            <img class="payment-item__thumb-img"
                                                src="{{ getImage(getFilePath('gateway') . '/' . $data->method->image) }}"
                                                alt="@lang('payment-thumb')">
                                        </div>

                                        <input class="payment-item__radio gateway-input"
                                            id="{{ titleToKey($data->name) }}" hidden data-gateway='@json($data)'
                                            type="radio" name="gateway" value="{{ $data->method_code }}" 
                                            @if(old('gateway')) @checked(old('gateway')==$data->method_code) @else
                                        @checked($loop->first) @endif
                                        data-min-amount="{{ showAmount($data->min_amount) }}"
                                        data-max-amount="{{ showAmount($data->max_amount) }}">
                                    </label>
                                    @endforeach
                                    @if ($gatewayCurrency->count() > 4)
                                    <button type="button" class="payment-item__btn more-gateway-option">
                                        <p class="payment-item__btn-text">@lang('Show All Payment Options')</p>
                                        <span class="payment-item__btn__icon"><i
                                                class="fas fa-chevron-down"></i></i></span>
                                    </button>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="payment-system-list p-3">
                                    <div class="deposit-info    @guest mb-3 @endguest">
                                        <div class="deposit-info__title">
                                            <p class="text mb-0">@lang('Amount')</p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <div class="deposit-info__input-group input-group">
                                                <span class="deposit-info__input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="text" class="form-control form--control amount" readonly
                                                    name="amount" placeholder="@lang('00.00')"
                                                    value="{{ gs('donation_setting')?->amount }}" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    @guest
                                    <div class="deposit-info mb-3">

                                        <div class="deposit-info__title">
                                            <p class="text mb-0">@lang('Name')</p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <input class="form-control form--control" name="name" type="text"
                                                value="{{ old('name', @$user->fullname) }}" required>
                                        </div>

                                    </div>

                                    <div class="deposit-info mb-3" >

                                        <div class="deposit-info__title">
                                            <p class="text mb-0">@lang('Email')</p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <input class="form-control form--control" name="email" type="email"
                                            value="{{ old('email', @$user->email) }}" required>
                                        </div>

                                    </div>
                                    <div class="deposit-info">

                                        <div class="deposit-info__title">
                                            <p class="text mb-0">@lang('Mobile')</p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <input class="form-control form--control" name="mobile" type="tel"
                                            value="{{ old('email', @$user->mobile) }}" required>
                                        </div>

                                    </div>
                                    @endguest
                                    <hr>
                                    <div class="deposit-info hideInfo">
                                        <div class="deposit-info__title">
                                            <p class="text has-icon">@lang('Processing Charge')
                                                <span data-bs-toggle="tooltip"
                                                    title="@lang('Processing charge for payment gateways')"
                                                    class="proccessing-fee-info"><i class="las la-info-circle"></i>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text"><span class="processing-fee">@lang('0.00')</span>
                                                {{ __(gs('cur_text')) }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="deposit-info total-amount pt-3">
                                        <div class="deposit-info__title">
                                            <p class="text">@lang('Total')</p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text"><span class="final-amount">@lang('0.00')</span>
                                                {{ __(gs('cur_text')) }}</p>
                                        </div>
                                    </div>

                                    <div class="deposit-info gateway-conversion d-none total-amount hideInfo pt-2">
                                        <div class="deposit-info__title">
                                            <p class="text">@lang('Conversion')
                                            </p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text"></p>
                                        </div>
                                    </div>
                                    <div class="deposit-info conversion-currency d-none total-amount  hideInfopt-2">
                                        <div class="deposit-info__title">
                                            <p class="text">
                                                @lang('In') <span class="gateway-currency"></span>
                                            </p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text">
                                                <span class="in-currency"></span>
                                            </p>

                                        </div>
                                    </div>
                                    <div class="d-none hideInfo crypto-message mb-3">
                                        @lang('Conversion with') <span class="gateway-currency"></span> @lang('and final
                                        value will Show on next step')
                                    </div>
                                    <button type="submit" class="btn btn--base w-100">
                                        @lang('Confirm Donation')
                                    </button>
                                    <div class="info-text pt-3">
                                        <p class="text">@lang('Ensuring your funds grow safely through our secure
                                            donation
                                            process with world-class payment options.')</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endpush
@endif





<x-confirmation-modal />

@include($activeTemplate . 'partials.collection_modal')
@include($activeTemplate . 'partials.share_modal')
@include($activeTemplate . 'partials.login_modal')


@endsection

@push('script')
<script>
    "use strict";

        let likeRoutes = {
            updateLike: "{{ route('user.image.like.update') }}"

        };
        let likeParams = {
            loggedStatus: @json(Auth::check()),
            csrfToken: "{{ csrf_token() }}"
        }

        let followRoutes = {
            updateFollow: "{{ route('user.follow.update') }}",
        }

        let followParams = {
            loggedStatus: @json(Auth::check()),
            csrfToken: "{{ csrf_token() }}",
            appendStatus: 0
        }

        $('.login-btn').on('click', function() {
            let modal = $('#loginModal');
            modal.modal('show');
        });

        $('.photo-modal__close').on('click', function() {
            $('.photo-modal').removeClass('active');
        });
        $('.download-form').on('submit', function() {
            downloadModal.modal('hide');
            setTimeout(() => {
                $('.photo-modal').addClass('active');
            }, 3000);
        })
        $('#confirmationModal [type="submit"]').on('click', function() {
            $('#confirmationModal').modal('hide');
        })
</script>
<script src="{{ asset($activeTemplateTrue . 'js/like.js') }}"></script>
<script src="{{ asset($activeTemplateTrue . 'js/follow.js') }}"></script>
@endpush



@if (gs('donation_module') && @$imageFiles?->where('is_free', 1)->isNotEmpty() && $image->user_id != @$user->id)
@push('script')
<script>
    (function($) {
            "use strict";
      

            var amount = parseFloat($('.amount').val() || 0);
            var gateway;
       
            
            $('.amount').on('input', function(e) {
                amount = parseFloat($(this).val());
                if (!amount) {
                   amount = 0;
                }
                calculation();
            });

            $('.gateway-input').on('change', function(e) {
                gatewayChange();
            });

            function gatewayChange() {
                let gatewayElement = $('.gateway-input:checked');
                let methodCode = gatewayElement.val();

                let gatewayValue = $('.gateway-input:checked').val();
             
                if (gatewayValue == 'balance') {
                    @if (@auth()->user()->balance <= 0)
                        $(".deposit-form button[type=submit]").attr('disabled', true);
                    @else
                        $(".deposit-form button[type=submit]").removeAttr('disabled');
                    @endif

                    $('.hideInfo').addClass('d-none')
                    $(".final-amount").text(amount);
                }else{
                    $('.hideInfo').removeClass('d-none')

                    gateway = gatewayElement.data('gateway');
                 
                    let processingFeeInfo =
                        `${parseFloat(gateway.percent_charge).toFixed(2)}% with ${parseFloat(gateway.fixed_charge).toFixed(2)} {{ __(gs('cur_text')) }} charge for payment gateway processing fees`
                    $(".proccessing-fee-info").attr("data-bs-original-title", processingFeeInfo);
                    calculation();
                }
        
            }

            gatewayChange();

            $(".more-gateway-option").on("click", function(e) {
                let paymentList = $(".gateway-option-list");
                paymentList.find(".gateway-option").removeClass("d-none");
                $(this).addClass('d-none');
                paymentList.animate({
                    scrollTop: (paymentList.height() - 60)
                }, 'slow');
            });

            function calculation() {
                if (!gateway) return;
            
                let percentCharge = 0;
                let fixedCharge = 0;
                let totalPercentCharge = 0;

                if (amount) {
                    percentCharge = parseFloat(gateway.percent_charge);
                    fixedCharge = parseFloat(gateway.fixed_charge);
                    totalPercentCharge = parseFloat(amount / 100 * percentCharge);
                }

                let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                let totalAmount = parseFloat((amount || 0) + totalPercentCharge + fixedCharge);

                $(".final-amount").text(totalAmount.toFixed(2));
                $(".processing-fee").text(totalCharge.toFixed(2));
                $("input[name=currency]").val(gateway.currency);
                $(".gateway-currency").text(gateway.currency);


                if (gateway.currency != "{{ gs('cur_text') }}" && gateway.method.crypto != 1) {
                    $('.deposit-form').addClass('adjust-height')

                    $(".gateway-conversion, .conversion-currency").removeClass('d-none');
                    $(".gateway-conversion").find('.deposit-info__input .text').html(
                        `1 {{ __(gs('cur_text')) }} = <span class="rate">${parseFloat(gateway.rate).toFixed(2)}</span>  <span class="method_currency">${gateway.currency}</span>`
                    );
                    $('.in-currency').text(parseFloat(totalAmount * gateway.rate).toFixed(gateway.method.crypto == 1 ? 8 : 2))
                } else {
                    $(".gateway-conversion, .conversion-currency").addClass('d-none');
                    $('.deposit-form').removeClass('adjust-height')
                }

                if (gateway.method.crypto == 1) {
                    $('.crypto-message').removeClass('d-none');
                } else {
                    $('.crypto-message').addClass('d-none');
                }
            }

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            $('.gateway-input').change();



            $(document).on('change', '[name="donation_quantity"]',function() {
              
          
                $(`.quantity-button`).removeClass('active');
                if ($(this).val()) $(`.quantity-button:contains("${$(this).val()}")`).addClass('active');

                var value = $(this).val()
                var unitAmount = $(this).data('donation_amount');

                amount = parseFloat(value * unitAmount);
                setDonationAmount(amount);
                gatewayChange()
            }).change();


            $(".quantity-button").on('click', function(event) {
              
                $(this).toggleClass('active');
                if ($(this).hasClass('active')) {
                
                    var unitAmount = $(this).data('donation_amount');
                    $('.quantity-button').removeClass('active');
                    $(this).addClass('active');
                    var linkText = $(this).text();
                    var totalItem = parseInt(linkText)
                    amount = parseFloat(totalItem * unitAmount);
                    setDonationAmount(amount);
                    gatewayChange()
                    $("#donation").val(linkText);
                } else {
                    setDonationAmount();
                    gatewayChange()
                    $("#donation").val(1);
                }
            });

            function setDonationAmount(amount = `{{ @gs('donation_setting')?->amount }}`) {
                $('.amount').val(amount);
                gatewayChange()
                
            }

        })(jQuery);
</script>
@endpush

@endif

@push('style')
<style>
    .item-details-video video {
        width: 100%;
        object-fit: cover;
        height: 100%;
        border-radius: 5px;
    }

    .form-select:focus {
        border: 1px solid hsl(var(--border));
    }
</style>
@endpush