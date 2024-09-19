@extends($activeTemplate . 'layouts.master')
@section('content')
    @php
        $kyc = getContent('kyc.content', true);
    @endphp
    <div class="row">
        <div class="container">
            <div class="notice"></div>
            @php
                $kyc = getContent('kyc.content', true);
            @endphp
            @if (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason)
                <div class="col-12">
                    <div class="alert alert--danger" role="alert">
                        <div class="alert__icon"><i class="fas fa-file-signature"></i></div>
                        <p class="alert__message">
                            <span class="fw-bold">@lang('KYC Documents Rejected')</span><br>
                            <small><i>{{ __(@$kyc->data_values->reject) }}
                                    <a href="javascript::void(0)" class="link-color" data-bs-toggle="modal"
                                        data-bs-target="#kycRejectionReason">@lang('View Reason')</a> @lang('to show the reason').

                                    <a href="{{ route('user.kyc.form') }}" class="link-color">@lang('Click Here')</a>
                                    @lang('to Re-submit Documents'). <br>
                                    <a href="{{ route('user.kyc.data') }}" class="link-color">@lang('See KYC Data')</a>
                                </i>
                            </small>
                        </p>
                    </div>
                </div>
            @elseif ($user->kv == Status::KYC_UNVERIFIED)
                <div class="col-12">
                    <div class="alert alert--info" role="alert">
                        <div class="alert__icon"><i class="fas fa-file-signature"></i></div>
                        <p class="alert__message">
                            <span class="fw-bold">@lang('KYC Verification Required')</span><br>
                            <small>
                                <i>{{ __(@$kyc->data_values->required) }}
                                    <a href="{{ route('user.kyc.form') }}" class="link-color">@lang('Click here')</a>
                                    @lang('to submit KYC information').</i>
                            </small>
                        </p>
                    </div>
                </div>
            @elseif($user->kv == Status::KYC_PENDING)
                <div class="col-12">
                    <div class="alert alert--warning" role="alert">
                        <div class="alert__icon"><i class="fas fa-user-check"></i></div>
                        <p class="alert__message">
                            <span class="fw-bold">@lang('KYC Verification Pending')</span><br>
                            <small><i>{{ __(@$kyc->data_values->pending) }} <a href="{{ route('user.kyc.data') }}"
                                        class="link-color">@lang('Click here')</a>
                                    @lang('to see your submitted information')</i>
                            </small>
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>


    <div class="row g-4 g-lg-3 g-xxl-4">
        <div class="col-sm-6 col-md-4">
            <div class="dashboard-widget">
                <div class="dashboard-widget__icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="dashboard-widget__content">
                    <span class="dashboard-widget__title">
                        @lang('Balance')
                    </span>
                    <h4 class="dashboard-widget__amount">
                        {{ showAmount($user->balance) }}
                    </h4>
                </div>
                <span class="dashboard-widget__overlay-icon">
                    <i class="fas fa-wallet"></i>
                </span>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="dashboard-widget">
                <div class="dashboard-widget__icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="dashboard-widget__content">
                    <span class="dashboard-widget__title">
                        @lang('Deposit')
                    </span>
                    <h4 class="dashboard-widget__amount">
                        {{ showAmount($user->deposits->where('status', Status::PAYMENT_SUCCESS)->sum('amount')) }}
                    </h4>
                </div>
                <span class="dashboard-widget__overlay-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </span>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="dashboard-widget">
                <div class="dashboard-widget__icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="dashboard-widget__content">
                    <span class="dashboard-widget__title">
                        @lang('Withdraw')
                    </span>
                    <h4 class="dashboard-widget__amount">
                        {{ showAmount($user->withdrawals->where('status', Status::PAYMENT_SUCCESS)->sum('amount')) }}
                    </h4>
                </div>
                <span class="dashboard-widget__overlay-icon">
                    <i class="fas fa-credit-card"></i>
                </span>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="dashboard-widget">
                <div class="dashboard-widget__icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="dashboard-widget__content">
                    <span class="dashboard-widget__title">
                        @lang('Referral Bonus')
                    </span>
                    <h4 class="dashboard-widget__amount">
                        {{ showAmount($user->referralLogs->sum('amount')) }}
                    </h4>
                </div>
                <span class="dashboard-widget__overlay-icon">
                    <i class="fas fa-credit-card"></i>
                </span>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="dashboard-widget">
                <div class="dashboard-widget__icon">
                    <i class="fas fa-money-bill-wave-alt"></i>
                </div>
                <div class="dashboard-widget__content">
                    <span class="dashboard-widget__title">
                        @lang('Earnings')
                    </span>
                    <h4 class="dashboard-widget__amount">
                        {{ showAmount($user->earningLogs->sum('amount')) }}
                    </h4>
                </div>
                <span class="dashboard-widget__overlay-icon">
                    <i class="fas fa-money-bill-wave-alt"></i>
                </span>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="dashboard-widget">
                <div class="dashboard-widget__icon">
                    <i class="fas fa-images"></i>
                </div>
                <div class="dashboard-widget__content">
                    <span class="dashboard-widget__title">
                        @lang('Resources')
                    </span>
                    <h4 class="dashboard-widget__amount">
                        {{ shortNumber($user->allImages->count()) }}
                    </h4>
                </div>
                <span class="dashboard-widget__overlay-icon">
                    <i class="fas fa-images"></i>
                </span>
            </div>
        </div>
        @if ($user->purchasedPlan)
            <h5 class="mb-0">@lang('Purchased Plan Details')</h5>
            <div class="col-sm-6 col-md-4">
                <div class="dashboard-widget">
                    <div class="dashboard-widget__icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="dashboard-widget__content">
                        <span class="dashboard-widget__title">
                            {{ __(@$user->purchasedPlan->plan->name) }} @lang('Plan')
                        </span>
                        <p class="dashboard-widget__amount">
                            @lang('Expired at : ') {{ showDateTime($user->purchasedPlan->expired_at, 'd M, Y') }}
                        </p>
                    </div>
                    <span class="dashboard-widget__overlay-icon">
                        <i class="fas fa-box"></i>
                    </span>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="dashboard-widget">
                    <div class="dashboard-widget__icon">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="dashboard-widget__content">
                        <span class="dashboard-widget__title">
                            @lang('Daily Download Limit')
                        </span>
                        <h4 class="dashboard-widget__amount">
                            <span data-bs-toggle="tooltip"
                                data-bs-title="@lang('Today\'s download')">{{ @$user->downloads()->whereDate('created_at', now())->count() }}</span>
                            / <span data-bs-toggle="tooltip" data-bs-title="@lang('Daily download limit')">{{ $user->purchasedPlan->daily_limit }}</span>
                        </h4>
                    </div>
                    <span class="dashboard-widget__overlay-icon">
                        <i class="fas fa-download"></i>
                    </span>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="dashboard-widget">
                    <div class="dashboard-widget__icon">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="dashboard-widget__content">
                        <span class="dashboard-widget__title">
                            @lang('Monthly Download Limit')
                        </span>
                        <h4 class="dashboard-widget__amount">
                            <span data-bs-toggle="tooltip" data-bs-title="@lang('This month\'s download')">
                                {{ shortNumber(@$user->downloads()->whereDate('created_at', '>=', now())->count()) }}</span>
                            / <span data-bs-toggle="tooltip"
                                data-bs-title="@lang('Monthly download limit')">{{ shortNumber($user->purchasedPlan->monthly_limit) }}</span>
                        </h4>
                    </div>
                    <span class="dashboard-widget__overlay-icon">
                        <i class="fas fa-download"></i>
                    </span>
                </div>
            </div>
        @endif
        <h5 class="mb-0">@lang('Earning Last 30 Days')</h5>
        <div class="col-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    <div id="earningLogChart"> </div>
                </div>
            </div>
        </div>
    </div>



    @if (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason)
        <div class="modal fade" id="kycRejectionReason">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('KYC Document Rejection Reason')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ auth()->user()->kyc_rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>

    <script>
        "use strict";

        // apex-line chart
        var options = {
            chart: {
                height: 450,
                type: "area",
                toolbar: {
                    show: false
                },
                dropShadow: {
                    enabled: true,
                    enabledSeries: [0],
                    top: -2,
                    left: 0,
                    blur: 10,
                    opacity: 0.08
                },
                animations: {
                    enabled: true,
                    easing: 'linear',
                    dynamicAnimation: {
                        speed: 1000
                    }
                }

            },
            dataLabels: {
                enabled: false
            },
            colors: ['#{{ gs('base_color') }}'],
            series: [{
                name: "Earnings",
                data: [
                    @foreach ($report['date'] as $earningDate)
                        {{ @$earningMonth->where('date', $earningDate)->first()->totalAmount ?? 0 }},
                    @endforeach
                ]
            }],
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.9,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: [
                    @foreach ($report['date'] as $earningDate)
                        "{{ $earningDate }}",
                    @endforeach
                ]
            },
            grid: {
                padding: {
                    left: 5,
                    right: 5
                },
                xaxis: {
                    lines: {
                        show: false
                    }
                },
                yaxis: {
                    lines: {
                        show: false
                    }
                },
            },
            fill: {
                colors: ['#{{ gs('base_color') }}']
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "{{ __(gs('cur_sym')) }}" + val + " "
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#earningLogChart"), options);
        chart.render();
    </script>
@endpush
