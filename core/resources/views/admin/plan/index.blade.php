@extends('admin.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('S.N')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Download Limit')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $plan)
                                    <tr>
                                        <td>
                                            {{ $plans->firstItem() + $loop->index }}
                                        </td>
                                        <td>
                                            <span class="fw-bold">
                                                {{ __($plan->name) }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="d-block">{{ showAmount($plan->monthly_price) }} / @lang('month')</span>
                                            <span>{{ showAmount($plan->yearly_price) }} / @lang('year')</span>
                                        </td>
                                        <td>
                                            <span class="d-block"> {{ $plan->daily_limit == -1 ? 'unlimited' : $plan->daily_limit }} /
                                                @lang('day')</span>
                                            <span>{{ $plan->monthly_limit == -1 ? 'unlimited' : $plan->monthly_limit }} / @lang('month')</span>
                                        </td>
                                        <td>
                                            @php echo $plan->statusBadge; @endphp
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end flex-wrap gap-2">
                                                <button class="btn btn-outline--primary cuModalBtn btn-sm" data-modal_title="@lang('Update Plan')"
                                                    data-resource="{{ $plan }}"><i class="las la-pen"></i>@lang('Edit')
                                                </button>
                                                @if ($plan->status == 1)
                                                    <button class="btn btn-outline--danger btn-sm confirmationBtn" data-question="@lang('Are you sure to disable this plan?')"
                                                        data-action="{{ route('admin.plan.status', $plan->id) }}">
                                                        <i class="las la-eye-slash"></i>@lang('Disable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline--success confirmationBtn btn-sm" data-question="@lang('Are you sure to enable this plan?')"
                                                        data-action="{{ route('admin.plan.status', $plan->id) }}">
                                                        <i class="las la-eye"></i>@lang('Enable')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($plans->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($plans) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="cuModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.plan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Title')</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Monthly Price')</label>
                            <div class="input-group">
                                <input type="number" step="any" class="form-control" name="monthly_price" required>
                                <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Yearly Price')</label>
                            <div class="input-group">
                                <input type="number" step="any" class="form-control" name="yearly_price" required>
                                <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Daily Download Limit')</label>
                            <input type="number" class="form-control" name="daily_limit" required>
                            <small class="text--info"><i class="las la-info-circle"></i> @lang('-1 for unlimited download limit')</small>
                        </div>
                        <div class="form-group">
                            <label>@lang('Monthly Download Limit')</label>
                            <input type="number" class="form-control" name="monthly_limit" required>
                            <small class="text--info"><i class="las la-info-circle"></i> @lang('-1 for unlimited download limit')</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection


@push('breadcrumb-plugins')
    <x-search-form />
    <button class="btn btn-outline--primary h-45 cuModalBtn" data-modal_title="@lang('Add Plan')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush
