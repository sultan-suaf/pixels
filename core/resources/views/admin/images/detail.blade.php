@extends('admin.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <form action="{{ route('admin.images.update', $image->id) }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <img src="{{ imageUrl(getFilePath('stockImage'), $image->image_name) }}" alt="@lang('Image')">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    @if (@$image->user)
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('Uploaded By')</label>
                                                <input class="form-control" type="text" value="{{ __($image->user->fullname) }}" disabled>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('File Type')</label>
                                            <div class="form--select">
                                                <select class="form-control select2" data-minimum-results-for-search="-1" name="file_type" required>
                                                    <option value="">@lang('Select One')</option>
                                                    @foreach ($fileTypes as $fileType)
                                                        <option value="{{ $fileType->id }}" @selected($fileType->id == old('file_type', @$image->file_type_id))
                                                            data-extesnion='@json($fileType->supported_file_extension)'>
                                                            {{ __($fileType->name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Category')</label>
                                            <select class="form-control select2" data-minimum-results-for-search="-1" name="category" required>
                                                <option value="" disabled>@lang('Select One')</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}" @selected($category->id == $image->category_id)>{{ __($category->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    @if ($extensions)
                                        <div class="col-md-12">
                                            <div class="form-group position-relative" id="extension">
                                                <label>@lang('Extensions')</label>
                                                <select class="form-control select2-tokenize" name="extensions[]" multiple="multiple" required>
                                                    @foreach ($extensions as $option)
                                                        <option value="{{ $option }}" @selected($image->extensions && in_array($option, $image->extensions))>{{ __(strtoupper($option)) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Title')</label>
                                            <input class="form-control" name="title" type="text" value="{{ $image->title }}" required>
                                        </div>
                                    </div>

                                    @if ($colors)
                                        <div class="col-md-6">
                                            <div class="form-group position-relative" id="color">
                                                <label>@lang('Colors')</label>
                                                <select class="form-control select2-tokenize-color" name="colors[]" multiple="multiple" required>
                                                    @foreach ($colors as $color)
                                                        <option value="{{ $color->color_code }}" @selected($image->colors && in_array($color->color_code, $image->colors))>{{ __($color->name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif



                                    <div class="col-md-12">
                                        <div class="form-group" id="tag">
                                            <label>@lang('Tags')</label>
                                            <select class="form-control select2-auto-tokenize" name="tags[]" multiple="multiple" required>
                                                @if (@$image->tags)
                                                    @foreach ($image->tags as $option)
                                                        <option value="{{ $option }}" selected>{{ __($option) }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Total Views')</label>
                                    <input class="form-control" type="text" value="{{ $image->total_view }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Total Likes')</label>
                                    <input class="form-control" type="text" value="{{ $image->total_like }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Total Downloads')</label>
                                    <input class="form-control" type="number" value="{{ $image->totalDownloads }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Attribution')</label>
                                    <input name="attribution" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger"
                                        data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')"
                                        type="checkbox" @if ($image->attribution) checked @endif>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Status')</label>
                                    <select class="form-control status select2" data-minimum-results-for-search="-1" name="status" required>
                                        <option value="" selected>@lang('Select One')</option>
                                        @if ($image->status == 0)
                                            <option value="0" @selected($image->status == 0)>@lang('Pending')</option>
                                        @endif
                                        <option value="3" @selected($image->status == 3)>@lang('Rejected')</option>
                                        <option value="1" @selected($image->status == 1)>@lang('Approved')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <ul class="list-group my-3">
                                    <li class="list-group-item bg--primary border--primary text-center">
                                        <span class="fw-bold text--white">@lang('Files')</span>
                                    </li>
                                    @foreach ($image->files as $value)
                                        <li class="list-group-item d-flex justify-content-between flex-wrap">
                                            <div class="col-12 extraPriceElement">
                                                <div class="row align-items-center">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <div class="d-flex justify-content-between">
                                                                <label>@lang('Resolution')</label>
                                                            </div>
                                                            <div class="input-group">
                                                                <input class="form-control" name="resolution[]" type="text"
                                                                    value="{{ $value->resolution }}" required>
                                                                <input name="file_id[]" type="hidden" value="{{ $value->id }}">
                                                                <a class="input-group-text"
                                                                    href="{{ route('admin.images.file.download', $value->id) }}">
                                                                    <i class="las la-download"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>@lang('Status')</label>
                                                            <select class="form-control select2" data-minimum-results-for-search="-1"
                                                                name="statusFile[{{ $loop->index }}]" required>
                                                                <option value="">@lang('Select One')</option>
                                                                <option value="1" @selected($value->status == Status::ENABLE)>@lang('Enable')</option>
                                                                <option value="0" @selected($value->status == Status::DISABLE)>@lang('Disable')</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>@lang('Premium/Free')</label>
                                                            <select class="form-control is_free_select select2" data-minimum-results-for-search="-1"
                                                                name="is_free[{{ $loop->index }}]" required>
                                                                <option value="">@lang('Select One')</option>
                                                                <option value="0" @selected($value->is_free == Status::PREMIUM)>@lang('Premium')</option>
                                                                <option value="1" @selected($value->is_free == Status::FREE)>@lang('Free')</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 price {{ $value->is_free == Status::FREE ? 'd-none' : '' }}">
                                                        <div class="form-group">
                                                            <label>@lang('Price')</label>
                                                            <div class="input-group">
                                                                <input class="form-control" name="price[]" type="number"
                                                                    value="{{ @$value->price ? showAmount(@$value->price, currencyFormat: false) : '' }}"
                                                                    step="any" @if (!$value->is_free) required @endif>
                                                                <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Description')</label>
                                    <textarea class="form-control" name="description" rows="5" required>{{ $image->description }}</textarea>
                                </div>
                            </div>

                            <div class="row reason">
                                <div class="border-bottom my-3 text-center">
                                    <h5 class="py-2">@lang('Rejection Reason')</h5>
                                    @if ($image->admin_id || $image->reviewer_id)
                                        <h6 class="mb-2">@lang('Previously Reviewed By') {{ $image->admin_id ? $image->admin->name : $image->reviewer->name }}</h6>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="form- group">
                                        <label>@lang('Predefined Reason')</label>
                                        <select class="form-control predefined-reason">
                                            <option value="" disabled selected>@lang('Select One')</option>
                                            @foreach ($reasons as $reason)
                                                <option value="{{ $reason->description }}">{{ __($reason->title) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>@lang('Reason')</label>
                                        <textarea class="form-control" name="reason" rows="6" @if ($image->status == 3) required @endif>{{ $image->reason }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('style')
    <style>
        #tag,
        #extension {
            position: relative;
        }

        .reason-title {
            background-color: #dddddd21;
        }
    </style>
@endpush

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.images.all') }}" />
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            if ($('.status option:selected').val() != 3) {
                $('.reason').hide();
            }

            $('.select2-auto-tokenize').select2({
                dropdownParent: $('#tag'),
                tags: true,
                tokenSeparators: [',']
            });

            $('.select2-tokenize').select2({
                dropdownParent: $('#extension'),
                tags: false,
                tokenSeparators: [',']
            });
            $('.select2-tokenize-color').select2({
                dropdownParent: $('#color'),
                tags: false,
                tokenSeparators: [',']
            });

            $('[name=is_free]').on('change', function() {
                if (!$(this).is(':checked')) {
                    $('.price').removeClass('d-none');
                    $('.price label').addClass('required');
                    $('[name=price]').attr('required', true);
                } else {
                    $('.price').addClass('d-none');
                    $('.price label').removeClass('required');
                    $('[name=price]').attr('required', false);
                }
            })

            $('.status').on('change', function() {
                if ($(this).val() == 3) {
                    $('[name=reason]').attr('required', true);
                    $('.reason').show('slow');
                } else {
                    $('[name=reason]').attr('required', false);
                    $('.reason').hide('slow');
                }
            });

            $('.predefined-reason').on('change', function() {
                $('[name=reason]').val($(this).val());
            });



            $(document).on('change', '.is_free_select', function() {
                if ($(this).val() == 1) {
                    $(this).closest('.extraPriceElement').find('.price').addClass('d-none');
                } else {
                    $(this).closest('.extraPriceElement').find('.price').removeClass('d-none');
                }
            })


            $(`select[name=file_type]`).on('change', function() {
                $('.select2-tokenize').val(null).trigger(`change`);
                const fileExtensions = $(this).find(`option:selected`).data('extesnion');
                let html = "";

                $.each((fileExtensions ?? []), function(i, fileExtension) {
                    html += `<option value="${fileExtension}">${fileExtension}</option>`;
                });
                $(`select[name="extensions[]"]`).html(html);
            });

        })(jQuery);
    </script>
@endpush
@push('style')
    <style>
        .select2-container {
            z-index: unset !important;
        }
    </style>
@endpush
