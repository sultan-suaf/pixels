@extends('reviewer.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        <div class="col-md-12">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-5">
                            <div class="d-flex align-items-center justify-content-center">
                                <img class="w-100" src="{{ getImage(getFilePath('stockImage') . '/' . $image->image_name) }}" alt="@lang('Image')">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <ul class="list-group mb-3">
                                <li class="list-group-item bg--primary border--primary text-center">
                                    <span class="fw-bold text--white">@lang('Details')</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between flex-wrap">
                                    <span class="fw-bold">@lang('Title')</span>
                                    <span>{{ __($image->title) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between flex-wrap">
                                    <span class="fw-bold">@lang('Category')</span>
                                    <span>{{ __($image->category->name) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between flex-wrap">
                                    <span class="fw-bold">@lang('Uploader')</span>
                                    <span>{{ __($image->user->fullname) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between flex-wrap">
                                    <span class="fw-bold">@lang('Uploaded at')</span>
                                    <span>{{ showDateTime($image->created_at, 'd M, Y h:i A') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between flex-wrap">
                                    <span class="fw-bold">@lang('Extensions')</span>
                                    <span>
                                        @if ($image->extensions)
                                            @php
                                                echo strtoupper(implode(', ', $image->extensions));
                                            @endphp
                                        @else
                                            @lang('Not defined')
                                        @endif
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between flex-wrap">
                                    <span class="fw-bold">@lang('Attribution')</span>
                                    <span>
                                        {{ $image->attribution ? __('Required') : __('Not Required') }}
                                    </span>
                                </li>
                                @if ($image->status)
                                    <li class="list-group-item d-flex justify-content-between flex-wrap">
                                        <span class="fw-bold">@lang('Status')</span>
                                        <span>
                                            @php echo $image->statusBadge @endphp
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between flex-wrap">
                                        <span class="fw-bold">@lang('Reviewed By')</span>
                                        <span>
                                            @if ($image->admin_id)
                                                {{ __($image->admin->name) }}
                                            @endif

                                            @if ($image->reviewer_id)
                                                {{ __($image->reviewer->name) }}
                                            @endif
                                        </span>
                                    </li>
                                @endif
                            </ul>
                            <ul class="list-group mb-3">
                                <li class="list-group-item bg--primary border--primary text-center">
                                    <span class="fw-bold text--white">@lang('Files')</span>
                                </li>
                                @foreach ($image->files as $key => $file)
                                    <li class="list-group-item d-flex justify-content-between flex-wrap">
                                        <span class="fw-bold">
                                            {{ $file->resolution }} | @if ($file->is_free)
                                                @lang('Free')
                                            @else
                                               {{ showAmount($file->price) }}
                                            @endif
                                        </span>
                                        <span>
                                            <a href="{{ route('reviewer.images.file.download', $file->id) }}">@lang('Download File')</a>
                                        </span>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="form-group">
                                <label>@lang('Description')</label>
                                <textarea class="form-control" rows="6" disabled>{{ __($image->description) }}</textarea>
                            </div>
                            @if ($image->tags)
                                <div class="form-group">
                                    <label>@lang('Tags')</label>
                                    <select class="form-select form--control select2-auto-tokenize" multiple="multiple" disabled>
                                        @foreach ($image->tags as $tag)
                                            <option value="{{ $tag }}" selected>{{ __(ucfirst($tag)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if ($image->status == 3)
                                <div class="form-group">
                                    <label>@lang('Rejection Reason')</label>
                                    <textarea class="form-control" rows="6" disabled>{{ __($image->reason) }}</textarea>
                                </div>
                            @endif

                            @php
                                $class = 'col-sm-12';
                                if (!$image->status) {
                                    $class = 'col-sm-6';
                                }
                            @endphp

                            <div class="row gy-4 d-flex justify-content-between flex-wrap">
                                @if ($image->status != 1)
                                    <div class="{{ $class }}">
                                        <button class="btn btn--primary h-45 w-100 confirmationBtn" data-question="@lang('Are you sure, you want to approve this image?')" data-action="{{ route('reviewer.images.update', ['id' => $image->id, 'status' => 1]) }}"><i class="las la-check-circle"></i>@lang('Approve')</button>
                                    </div>
                                @endif
                                @if ($image->status != 3)
                                    <div class="{{ $class }}">
                                        <button class="btn btn--danger h-45 w-100 rejectBtn"><i class="las la-ban"></i>@lang('Reject')</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reviewModal" role="dialog" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Reject Image')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="post" action="{{ route('reviewer.images.update', $image->id) }}">
                    @csrf
                    <input name="status" type="hidden">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Predefined Reason')</label>
                            <select class="form-control predefinedReason select2" data-minimum-results-for-search="-1">
                                <option value="" disabled selected>@lang('Select One')</option>
                                @foreach ($reasons as $reason)
                                    <option value="{{ $reason->description }}">{{ __($reason->title) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Reason')</label>
                            <textarea class="form-control" name="reason" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            let modal = $('#reviewModal');
            $('.banBtn').on('click', function() {
                modal.find('.modal-title').text($(this).data('modal_title'));
                modal.find('[name=status]').val(2);
                modal.modal('show');
            });

            $('.rejectBtn').on('click', function() {
                modal.find('.modal-title').text($(this).data('modal_title'));
                modal.find('[name=status]').val(3);
                modal.modal('show');
            });

            $('.predefinedReason').on('change', function() {
                $('[name=reason]').val($(this).val());
            })
        })(jQuery);
    </script>
@endpush
