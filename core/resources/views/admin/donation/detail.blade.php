@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30 justify-content-center">
        <div class="col-xl-4 col-md-6 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Donation from') {{ __(@$donation->sender->name) }}</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Date')
                            <span class="fw-bold">{{ showDateTime(@$donation->created_at) }}</span>
                        </li>


                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Receiver Name')


                            <span class="fw-bold">
                                {{ @$donation->user->fullname }} </br>
                                <a href="{{ route('admin.users.detail', $donation->receiver_id) }}"><span>@</span>{{ @$donation->user->username }}</a>
                            </span>

                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Sender Name')
                            <span class="fw-bold">
                                {{ @$donation->sender->name }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Method')
                            <span class="fw-bold">{{ __(@$donation->payment_info) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Amount')
                            <span class="fw-bold">{{ showAmount($donation->amount) }} </span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('After Charge')
                            <span class="fw-bold">{{ showAmount($donation->amount + $donation->charge) }} </span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @php echo $donation->statusBadge @endphp
                        </li>
                        @if ($donation->admin_feedback)
                            <li class="list-group-item">
                                <strong>@lang('Admin Response')</strong>
                                <br>
                                <p>{{ __($donation->admin_feedback) }}</p>
                            </li>
                        @endif
                    </ul>
                    @if ($donation->status == Status::DONATION_PENDING)
                        <div class="mt-4">
                            <button class="btn btn-outline--success btn-sm ms-1 confirmationBtn"
                                data-action="{{ route('admin.deposit.approve', $donation->deposit->id) }}" data-question="@lang('Are you sure to approve this transaction?')"><i
                                    class="las la-check-double"></i>
                                @lang('Approve')
                            </button>
                            <button class="btn btn-outline--danger btn-sm ms-1 rejectBtn" data-id="{{ $donation->deposit->id }}"
                                data-amount="{{ showAmount($donation->amount) }}" data-username="{{ @$donation->user->username }}"><i
                                    class="las la-ban"></i> @lang('Reject')
                            </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>

    {{-- REJECT MODAL --}}
    <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Reject Confirmation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.deposit.reject') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <p>@lang('Are you sure to') <span class="fw-bold">@lang('reject')</span> <span class="fw-bold withdraw-amount text-success"></span>
                            @lang('deposit of') <span class="fw-bold withdraw-user"></span>?</p>

                        <div class="form-group">
                            <label class="mt-2">@lang('Reason for Rejection')</label>
                            <textarea name="message" maxlength="255" class="form-control" rows="5" required>{{ old('message') }}</textarea>
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

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.rejectBtn').on('click', function() {
                var modal = $('#rejectModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('.withdraw-amount').text($(this).data('amount'));
                modal.find('.withdraw-user').text($(this).data('username'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
