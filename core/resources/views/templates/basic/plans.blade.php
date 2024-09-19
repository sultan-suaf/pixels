@php
    $content = getContent('plan.content', true);
@endphp
@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="section price-section">
        <div class="section__head">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-xl-7 col-xxl-6">
                        <h2 class="mt-0 text-center">{{ __(@$content->data_values->title) }}</h2>
                        <p class="t-short-para mx-auto mb-0 text-center">
                            {{ __(@$content->data_values->subtitle) }}
                        </p>
                        @if ($plans->count())
                            <div class="form-check form-switch d-flex align-items-center justify-content-center flex-row">
                                <label class="form-check-label" for="period">@lang('Monthly')</label>
                                <input class="form-check-input mx-2" id="period" name="plan_period" type="checkbox" value="monthly">
                                <label class="form-check-label" for="period">@lang('Yearly')</label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row g-4 g-lg-3 g-xl-4 justify-content-center">
                @forelse ($plans as $plan)
                    <div class="col-md-6 col-lg-4">
                        <div class="price-card price-card--base plan-card" data-monthly_price="{{ showAmount($plan->monthly_price) }}"
                            data-yearly_price="{{ showAmount($plan->yearly_price) }}">
                            <div class="price-card__head">
                                <ul class="list">
                                    <li>
                                        <div class="list list--row align-items-end" style="--gap: 0.2rem">
                                            <h1 class="price-card__price plan-price m-0">{{ showAmount($plan->monthly_price) }}</h1>
                                            <span class="price-card__time plan-period"> /@lang('Month') </span>
                                        </div>
                                    </li>
                                </ul>
                                <div class="price-card__head-content">
                                    <h4 class="price-card__title mt-0 mb-1">{{ __($plan->name) }}</h4>
                                    <span class="price-card__subtitle"> {{ __($plan->title) }} </span>
                                </div>
                            </div>
                            <div class="price-card__body">
                                <ul class="list list--check">
                                    <li class="sm-text">{{ $plan->dailyLimitText }} @lang('daily downloads')</li>
                                    <li class="sm-text">{{ $plan->monthlyLimitText }} @lang('monthly downloads')</li>
                                </ul>
                            </div>
                            <div class="price-card__footer">
                                <button class="base-btn rounded-pill w-100 purchase-btn"
                                    data-current="{{ auth()->user()?->purchasedPlan?->plan_id == $plan->id }}"
                                    data-daily_limit="{{ $plan->dailyLimitText }}" data-id="{{ $plan->id }}"
                                    data-monthly_limit="{{ $plan->monthlyLimitText }}" data-plan_name="{{ __($plan->name) }}" type="button">
                                    @lang('Purchase Now')
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="d-flex justify-content-center align-items-center">
                        <img src="{{ getImage('assets/images/empty_message.png') }}" alt="@lang('Image')">
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('modal')
    <!--  Purchase Modal  -->
    <div class="modal custom--modal fade" id="purchaseModal" aria-hidden="true" aria-labelledby="title" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="title">@lang('Purchase Plan')</h5>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                </div>
                @auth
                    <form action="{{ route('user.plan.purchase') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <input name="period" type="hidden">
                            <input name="plan" type="hidden">
                            <div class="row gy-3">

                                <h6 class="text-danger already_purchased text-center">
                                    @lang('You already purchased the plan')
                                </h6>

                                <p class="plan-info text-center">@lang('By purchasing') <span class="fw-bold plan_name"></span> @lang(' plan, you will get ') <span
                                        class="daily_limit fw-bold"></span>@lang(' images download opurtunity per day and') <span class="monthly_limit fw-bold"></span> @lang(' images per month.')
                                </p>

                                <div class="form-group payment-info select2-parent">
                                    <label class="form-label required" for="payment_type">@lang('Payment Type')</label>
                                    <div class="form--select">
                                        <select class="form-select select2-basic" data-minimum-results-for-search="-1" id="payment_type" name="payment_type"
                                            required>
                                            <option value="">@lang('Select One')</option>
                                            <option value="direct">@lang('Direct Payment')</option>
                                            <option value="wallet">@lang('From Wallet')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn--base w-100 planSubmitConfirm" type="submit">@lang('Submit') <span class="plan_id"></span> </button>
                            <button class="btn btn--dark closeButton" data-bs-dismiss="modal" type="button">@lang('Close')</button>
                        </div>
                    </form>
                @else
                    <div class="modal-body">
                        <p>@lang('Please login first')</p>
                    </div>
                    <div class="modal-footer">
                        <a class="btn btn--base btn--sm" href="{{ route('user.login') }}">@lang('Login')</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('#period').on('change', function() {
                let planCards = $('.plan-card');
                let period = $(this).val();
                let monthlyPrice = 0;
                let yearlyPrice = 0;
                let afterText = null;

                if (period == 'monthly') {
                    $(this).val('yearly');
                    period = 'yearly';
                    afterText = '/Year';
                } else {
                    $(this).val('monthly');
                    period = 'monthly';
                    afterText = '/Month';
                }

                $.each(planCards, function(index, element) {
                    let price = $(element).data(period + '_price');
                    $(element).find('.plan-price').text(price);
                    $(element).find('.plan-period').text(afterText);
                });
            });

            $('.purchase-btn').on('click', function() {
                let plan = $(this).data();
                let plan_id = plan.id;
                let period = $('[name=plan_period]').val();
                let modal = $('#purchaseModal');

                modal.find('[name=plan]').val(plan.id);
                modal.find('[name=period]').val(period);

                modal.find('.plan_name').text(plan.plan_name);
                modal.find('.daily_limit').text(plan.daily_limit);
                modal.find('.monthly_limit').text(plan.monthly_limit);


                $('.already_purchased, .closeButton').addClass('d-none')
                $('.payment-info,.plan-info,.planSubmitConfirm').removeClass('d-none');

                let isCurrent = $(this).data('current');
                if (isCurrent) {
                    $('.already_purchased,.closeButton').removeClass('d-none')
                    $('.payment-info,.plan-info, .planSubmitConfirm').addClass('d-none');
                }
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
