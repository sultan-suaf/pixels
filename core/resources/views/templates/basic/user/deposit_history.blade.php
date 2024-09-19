@extends($activeTemplate . 'layouts.master')
@section('content')
    <form action="" method="GET">
        <div class="row justify-content-end mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" name="search" class="form-control form--control" value="{{ request()->search }}" placeholder="@lang('Search by transactions')">
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
                    <th class="text-center sm-text">@lang('Initiated')</th>
                    <th class="text-center sm-text">@lang('Amount')</th>
                    <th class="text-center sm-text">@lang('Conversion')</th>
                    <th class="text-center sm-text">@lang('Status')</th>
                    <th class="sm-text">@lang('Details')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deposits as $deposit)
                    <tr>
                        <td class="sm-text">
                            <div>
                                <span class="fw-bold text--base">{{ __($deposit->gateway?->name) }}</span>
                                <br>
                                <small> {{ $deposit->trx }} </small>
                            </div>
                        </td>

                        <td class="text-center sm-text">
                            <div>
                                {{ showDateTime($deposit->created_at) }}<br>{{ diffForHumans($deposit->created_at) }}
                            </div>
                        </td>
                        <td class="text-center sm-text">
                            <div>
                                {{ showAmount($deposit->amount) }} + <span class="text--danger" title="@lang('charge')">
                                    {{ showAmount($deposit->charge) }} </span>
                                <br>
                                <strong title="@lang('Amount with charge')">
                                    {{ showAmount($deposit->amount + $deposit->charge) }}
                                </strong>
                            </div>
                        </td>
                        <td class="text-center sm-text">
                            <div>
                                1 {{ __(gs('cur_text')) }} = {{ showAmount($deposit->rate) }} {{ __($deposit->method_currency) }}
                                <br>
                                <strong>{{ showAmount($deposit->final_amount, currencyFormat: false) }} {{ __($deposit->method_currency) }}</strong>
                            </div>
                        </td>
                        <td class="text-center sm-text">
                            @php echo $deposit->statusBadge @endphp
                        </td>
                        @php
                            $details = [];
                            if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000) {
                                foreach (@$deposit->detail ?? [] as $key => $info) {
                                    $details[] = $info;
                                    if ($info->type == 'file') {
                                        $details[$key]->value = route(
                                            'user.download.attachment',
                                            encrypt(getFilePath('verify') . '/' . $info->value),
                                        );
                                    }
                                }
                            }
                        @endphp

                        <td>
                            @if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000)
                                <a href="javascript:void(0)" class="btn btn--base btn-sm detailBtn" data-info="{{ json_encode($details) }}"
                                    @if ($deposit->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $deposit->admin_feedback }}" @endif>
                                    <i class="fas fa-desktop"></i>
                                </a>
                            @else
                                <button type="button" class="btn btn--success btn-sm" data-bs-toggle="tooltip" title="@lang('Automatically processed')">
                                    <i class="fas fa-check"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center sm-text">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($deposits->hasPages())
        <div class="mt-3">
            {{ paginateLinks($deposits) }}
        </div>
    @endif

    <!-- Detail Modal -->
    <div class="modal fade custom--modal" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="deposit-card__list list userData">
                    </ul>
                    <div class="feedback"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark sm-text" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');

                var userData = $(this).data('info');
                var html = '';
                if (userData) {
                    userData.forEach(element => {
                        if (element.type != 'file') {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${element.name}</span>
                                <span">${element.value}</span>
                            </li>`;
                        } else {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${element.name}</span>
                                <span"><a href="${element.value}"><i class="fa-regular fa-file"></i> @lang('Attachment')</a></span>
                            </li>`;
                        }
                    });
                }

                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);


                modal.modal('show');
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

        })(jQuery);
    </script>
@endpush
