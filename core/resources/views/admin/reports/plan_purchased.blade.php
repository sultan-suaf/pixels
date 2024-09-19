@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Plan')</th>
                                    <th>@lang('Download Limit')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Expired At')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchasedPlans as $purchasedPlan)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $purchasedPlan->user->fullname }}</span>
                                            <br>
                                            <span class="fw-bold">
                                                <a
                                                    href="{{ route('admin.users.detail', $purchasedPlan->user_id) }}"><span>@</span>{{ $purchasedPlan->user->username }}</a>
                                            </span>
                                        </td>
                                        <td>
                                            <a
                                                href="{{ route('admin.plan.all', ['search' => $purchasedPlan->plan->name]) }}">{{ __(@$purchasedPlan->plan->name) }}</a>
                                        </td>
                                        <td>
                                            <span class="d-block"> {{ $purchasedPlan->daily_limit == -1 ? 'unlimited' : $purchasedPlan->daily_limit }} /
                                                @lang('day')</span>
                                            <span>{{ $purchasedPlan->monthly_limit == -1 ? 'unlimited' : $purchasedPlan->monthly_limit }} /
                                                @lang('month')</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ showAmount($purchasedPlan->amount) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ showDateTime($purchasedPlan->expired_at, 'd M, Y') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($purchasedPlans->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($purchasedPlans) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    <x-confirmation-modal />
@endsection
@push('breadcrumb-plugins')
    <x-search-form />
@endpush
