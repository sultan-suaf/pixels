@extends($activeTemplate.'layouts.master')
@section('content')


<form>
    <div class="row justify-content-end mb-3">
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" name="search" class="form-control form--control" value="{{ request()->search }}"
                    placeholder="@lang('Search by transactions')">
                <button class="input-group-text btn--base border-0" type="submit">
                    <i class="las la-search"></i>
                </button>
            </div>
        </div>
    </div>
</form>
<div class="custom--table-container table-responsive--md">
    <table class="table custom--table">
        <thead>
            <tr>
                <th class="sm-text">@lang('Gateway | Trx')</th>
                <th class="sm-text">@lang('Initiated')</th>
                <th class="sm-text">@lang('Amount')</th>
                <th class="sm-text">@lang('Conversion')</th>
                <th class="sm-text">@lang('Status')</th>
                <th>@lang('Action')</th>
            </tr>
        </thead>
        <tbody>

            @forelse($withdraws as $withdraw)
            <tr>
                <td>
                    <div>
                        <span class="sm-text fw-bold text--base"> {{ __(@$withdraw->method->name) }}</span>
                        <br>
                        <small>{{ $withdraw->trx }}</small>
                    </div>
                </td>
                <td class="sm-text">
                    <div>
                        {{ showDateTime($withdraw->created_at) }} <br> {{ diffForHumans($withdraw->created_at) }}
                    </div>
                </td>
                <td class="sm-text">
                    <div>
                        {{ showAmount($withdraw->amount) }} - <span class="text--danger" title="@lang('charge')">{{
                            showAmount($withdraw->charge) }} </span>
                        <br>
                        <strong title="@lang('Amount after charge')">
                            {{ showAmount($withdraw->amount - $withdraw->charge) }}
                        </strong>
                    </div>
                </td>
                <td class="sm-text">
                    <div>
                        1 {{ __(gs('cur_text')) }} = {{ showAmount($withdraw->rate, currencyFormat:false) }} {{
                        __($withdraw->currency) }}
                        <br>
                        <strong>{{ showAmount($withdraw->final_amount, currencyFormat:false) }} {{
                            __($withdraw->currency) }}</strong>
                    </div>
                </td>
                <td class="sm-text">
                    @php echo $withdraw->statusBadge @endphp
                </td>
                <td>
                    <button class="btn btn-sm btn--base detailBtn"
                        data-user_data="{{ json_encode($withdraw->withdraw_information) }}" @if ($withdraw->status ==
                        Status::PAYMENT_REJECT) data-admin_feedback="{{ $withdraw->admin_feedback }}" @endif>
                        <i class="la la-desktop"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td class="text-center sm-text" colspan="6">{{ __($emptyMessage) }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if ($withdraws->hasPages())
    <div class="mt-3">
        {{ paginateLinks($withdraws) }}
    </div>
    @endif
</div>


{{-- APPROVE MODAL --}}
<div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Details')</h5>
                <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </span>
            </div>
            <div class="modal-body">
                <ul class="list-group userData">

                </ul>
                <div class="feedback"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    (function ($) {
            "use strict";
            $('.detailBtn').on('click', function () {
                var modal = $('#detailModal');
                var userData = $(this).data('user_data');
                var html = ``;
                userData.forEach(element => {
                    if(element.type != 'file'){
                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span">${element.value}</span>
                        </li>`;
                    }else{
                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span"><a href="${element.value}"><i class="fa-regular fa-file"></i> @lang('Attachment')</a></span>
                        </li>`;
                    }
                });
                modal.find('.userData').html(html);

                if($(this).data('admin_feedback') != undefined){
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                }else{
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);

                modal.modal('show');
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        })(jQuery);

</script>
@endpush