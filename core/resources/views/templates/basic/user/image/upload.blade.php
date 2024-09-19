@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row flex-column-reverse flex-md-row">
        <div class="col-12">
            <div class="card custom--card">
                <div class="card-body">
                    <h2 class="text-center">{{ __(@gs('instruction')->heading) }}</h2>
                    <p>{{ __(@gs('instruction')->instruction) }}</p>
                    <p class="text-center">@lang('Please download this file and include it in your zip')</p>
                    <div class="mt-3 text-center">
                        <a class="base-btn" href="{{ route('txt.download') }}">@lang('Download Now')</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="row upload-wrapper no-gutters justify-content-center">
                <div class="col-12 mt-4">
                    <div class="card custom--card form-card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __($pageTitle) }}</h5>
                        </div>
                        <form class="resourceUploadForm" enctype="multipart/form-data" method="post">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="photo-upload-area">
                                            <div class="file-upload">
                                                <div class="image-upload-wrap @if (@$image) d-none @endif">
                                                    <input class="file-upload-input validate" id="image-lavel" name="photo" type='file'
                                                        @if (!@$image) required @endif accept=".png,.jpeg,.jpg"
                                                        onchange="readURL(this);">
                                                    <div class="drag-text">
                                                        <label class="title" for="image-lavel">@lang('Drag and drop a file or select add Image')</label>
                                                    </div>
                                                </div>
                                                <div class="file-upload-content @if (@$image) d-block @endif">
                                                    <img class="file-upload-image"
                                                        src="{{ @$image ? imageUrl(getFilePath('stockImage'), $image->image_name) : '' }}"
                                                        alt="@lang('your image')">
                                                </div>
                                                <button class="base-btn w-100 upload-btn mt-3" type="button">@lang('Add Image')</button>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <label class="form-label">@lang('Video')</label>
                                            <div class="input-group">
                                                <input class="form-control form--control" id="video-upload" name="video" type="file"
                                                    accept="video/mp4">
                                                <a class="input-group-text" href="{{ route('user.image.download.video', @$image->id) }}">
                                                    <i class="las la-download"></i>
                                                </a>
                                            </div>
                                            <small class="text-muted mt-3"> @lang('Supported Files:')
                                                <b>@lang('.mp4'), @lang('.3gp')</b>
                                                @lang('& maximum file size is ') <b class="image--upload-size">@lang('15MB')</b>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">@lang('Title')</label>
                                            <input class="form-control form--control" name="title" type="text"
                                                value="{{ old('title', @$image->title) }}" required>
                                        </div>
                                        <div class="mb-3 select2-parent">
                                            <label class="form-label">@lang('File Type')</label>

                                            <select class="form-select select2-basic" name="file_type" required>
                                                <option value="">@lang('Select One')</option>
                                                @foreach ($fileTypes as $fileType)
                                                    <option value="{{ $fileType->id }}" @selected($fileType->id == old('file_type', @$image->file_type_id))
                                                        data-extesnion='@json($fileType->supported_file_extension)'>
                                                        {{ __($fileType->name) }}
                                                    </option>
                                                @endforeach
                                            </select>

                                        </div>
                                        <div class="mb-3  select2-parent">
                                            <label class="form-label required">@lang('Category')</label>

                                            <select class="form-select select2-basic" name="category" required>
                                                <option value="">@lang('Select One')</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}" @selected($category->id == old('category', @$image->category_id))>
                                                        {{ __($category->name) }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                        <div class="mb-3 position-relative select2-parent">
                                            <label class="form-label">@lang('Colors')</label>
                                            <select class="form-select form--control select2-auto-tokenize" name="colors[]" multiple="multiple" required>
                                                @foreach ($colors as $color)
                                                    <option value="{{ $color->color_code }}" @selected(@$image && in_array($color->color_code, $image->colors))>
                                                        {{ __($color->name) }}
                                                    </option>
                                                @endforeach
                                                @if (old('colors'))
                                                    @foreach (old('colors') as $oldColor)
                                                        <option value="{{ $oldColor }}" selected>
                                                            {{ __(ucfirst($oldColor)) }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="mb-3 position-relative select2-parent">
                                            <label class="form-label">@lang('Extensions')</label>
                                            <select class="form-select form--control select2-auto-tokenize" name="extensions[]" multiple="multiple"
                                                required>
                                                @foreach (old('extensions', @$image->extensions) ?? [] as $oldExtension)
                                                    <option value="{{ $oldExtension }}" selected>
                                                        {{ __(ucfirst($oldExtension)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">@lang('Tags (maximum 10 tags)')</label>
                                            <select class="form-select form--control select2-auto-tokenize" name="tags[]" multiple="multiple" required>
                                                @foreach (old('tags', $image->tags ?? []) as $oldTag)
                                                    <option value="{{ $oldTag }}" selected>
                                                        {{ __(ucfirst($oldTag)) }} </option>
                                                @endforeach
                                                @foreach ($tags ?? [] as $tag)
                                                    <option value="{{ $tag }}"> {{ __(ucfirst($tag)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <div class="card p-3">
                                        <div class="priceNewElement">
                                            <div class="removed_file"></div>
                                            @forelse (@$image->files ?? []  as $key => $file)
                                                <div class="extraPriceElement border-bottom py-3">
                                                    <div class="d-flex justify-content-between align-items-center py-3">
                                                        <h5 class="pb-sm-0 my-0">@lang('Pricing Set') - <span class="pricing-set-number"></span></h5>
                                                        <div class="removeButton">
                                                            <button class="btn btn-danger btn-sm removePrice d-flex align-items-center"
                                                                data-id="{{ $file->id }}" type="button">
                                                                <i class="las la-minus-circle"></i>
                                                                <span class="d-none d-sm-block ps-1">@lang('Remove')</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">
                                                                @lang('Resolution')
                                                                <i class="las la-exclamation-circle text--base" data-bs-custom-class="custom--tooltip"
                                                                    data-bs-toggle="tooltip" title="@lang('Seperate width and height by x, e.g. 600x1200 px')"></i>
                                                            </label>
                                                            <input class="form-control form--control resolutionInput"
                                                                name="old_file[{{ $file->id }}][resolution]" type="text"
                                                                value="{{ old('old_file.' . $file->id . '.resolution', @$file->resolution) }}"
                                                                placeholder="@lang('e.g. 600x1200 px')" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">@lang('Zip File')</label>
                                                            <div class="input-group">
                                                                <input class="form-control form--control fileInput"
                                                                    name="old_file[{{ $file->id }}][file]" type="file"
                                                                    @if (!@$image) required @endif accept=".zip,.7z,.rar,.tar,.wim">
                                                                <a class="input-group-text" href="{{ route('user.image.download.file', $file->id) }}">
                                                                    <i class="las la-download"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mb-3 select2-parent">
                                                            <label class="form-label">@lang('Status')</label>

                                                            <select class="form-select select2-basic" name="old_file[{{ $file->id }}][status]"
                                                                required>

                                                                <option value="1" @selected(old('old_file.' . $file->id . '.status', @$file->status) == 1)>
                                                                    @lang('Enable')</option>
                                                                <option value="0" @selected(old('old_file.' . $file->id . '.status', @$file->status) == 0)>
                                                                    @lang('Disable')</option>
                                                            </select>

                                                        </div>
                                                        <div class="col-md-6 mb-3 select2-parent">
                                                            <label class="form-label">@lang('Premium / Free')</label>

                                                            <select class="form-select is_free_select select2-basic"
                                                                name="old_file[{{ $file->id }}][is_free]" required>

                                                                <option value="0" @selected(old('old_file.' . $file->id . '.is_free', @$file->is_free) == Status::PREMIUM)>
                                                                    @lang('Premium')
                                                                </option>
                                                                <option value="1" @selected(old('old_file.' . $file->id . '.is_free', @$file->is_free) == Status::FREE)>
                                                                    @lang('Free')
                                                                </option>

                                                            </select>

                                                        </div>

                                                        <div class="price-div col-md-12 @if (old('old_file.' . $file->id . '.is_free', @$file->is_free) == Status::FREE) d-none @endif mb-3">
                                                            <label class="form-label">@lang('Price')
                                                                (@lang('You will get') <span class="commission-text fw-bold">
                                                                    {{ showAmount(gs('per_download'), currencyFormat: false) . '%' }}
                                                                </span>
                                                                @lang(' in each download'))
                                                            </label>
                                                            <div class="input-group input--group">
                                                                <input class="form-control form--control image-price priceInput"
                                                                    name="old_file[{{ $file->id }}][price]" type="number"
                                                                    value="{{ old('old_file.' . $file->id . '.price', showAmount(@$file->price, currencyFormat: false)) }}"
                                                                    max="{{ gs('image_maximum_price') }}" min="0" step="any">
                                                                <span class="input-group-text">
                                                                    {{ __(gs('cur_text')) }}
                                                                </span>
                                                            </div>
                                                            <small class="d-block maximum-price">@lang('Maximum price') <span
                                                                    class="fw-bold">{{ showAmount(gs('image_maximum_price'), currencyFormat: false) }}
                                                                </span></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="extraPriceElement border-bottom">
                                                    <div class="d-flex justify-content-between align-items-center py-3">
                                                        <h5 class="pb-sm-0 my-0">@lang('Pricing Set') - <span class="pricing-set-number"></span></h5>
                                                        <div class="removeButton">
                                                            <button class="btn btn-danger btn-sm removePrice d-flex align-items-center" type="button">
                                                                <i class="las la-minus-circle"></i>
                                                                <span class="d-none d-sm-block ps-1">@lang('Remove')</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">
                                                                @lang('Resolution')
                                                                <i class="las la-exclamation-circle text--base" data-bs-custom-class="custom--tooltip"
                                                                    data-bs-toggle="tooltip" title="@lang('Seperate width and height by x, e.g. 600x1200 px')"></i>
                                                            </label>
                                                            <input class="form-control form--control resolutionInput" name="resolution[]"
                                                                type="text" value="{{ old('resolution', @$image->resolution) }}"
                                                                placeholder="@lang('e.g. 600x1200 px')" required>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">@lang('Zip File')</label>
                                                            <input class="form-control form--control fileInput" name="file[]" type="file"
                                                                @if (!@$image) required @endif accept=".zip,.7z,.rar,.tar,.wim">
                                                        </div>
                                                        <div class="col-md-6 mb-3 select2-parent">
                                                            <label class="form-label">@lang('Status')</label>

                                                            <select class="form-select select2-basic" name="status[]" required>

                                                                <option value="1">@lang('Enable')</option>
                                                                <option value="0">@lang('Disable')</option>
                                                            </select>

                                                        </div>
                                                        <div class="col-md-6 mb-3 select2-parent">
                                                            <label class="form-label">@lang('Premium / Free')</label>

                                                            <select class="form-select is_free_select select2-basic" name="is_free[]" required>
                                                                <option value="0" @selected(old('is_free', @$image->is_free) == 0)>
                                                                    @lang('Premium')
                                                                </option>
                                                                <option value="1" @selected(old('is_free', @$image->is_free) == 1)>
                                                                    @lang('Free')
                                                                </option>

                                                            </select>

                                                        </div>
                                                        <div class="price-div col-12 mb-3">
                                                            <label class="form-label">@lang('Price')
                                                                (@lang('You will get') <span class="commission-text fw-bold">
                                                                    {{ showAmount(gs('per_download'), currencyFormat: false) . '%' }}
                                                                </span>
                                                                @lang(' in each download'))
                                                            </label>
                                                            <div class="input-group input--group">
                                                                <input class="form-control form--control image-price priceInput" name="price[]"
                                                                    type="number"
                                                                    value="{{ old('price', showAmount(@$image->price, currencyFormat: false)) }}"
                                                                    max="{{ gs('image_maximum_price') }}" min="0" step="any">
                                                                <span class="input-group-text">
                                                                    {{ __(gs('cur_text')) }}
                                                                </span>
                                                            </div>
                                                            <small class="d-block maximum-price">@lang('Maximum price') <span
                                                                    class="fw-bold">{{ showAmount(gs('image_maximum_price'), currencyFormat: false) }}</span></small>
                                                        </div>

                                                    </div>
                                                </div>
                                            @endforelse
                                        </div>
                                        <div class="py-3 text-end">
                                            <button class="btn btn--base btn-sm addNewPrice" type="button"> <i class="las la-plus"></i>
                                                @lang('Add More') </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">@lang('Description')</label>
                                    <textarea class="form-control form--control" id="description" name="description" required rows="6">{{ old('description', @$image->description) }}</textarea>

                                </div>

                                <div class="col-12">
                                    <button class="base-btn w-100" type="submit">@lang('Submit')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="progess-percent">
        <div class="progress-center">
            <div class="progress">
                <div class="progress-bar progress-bar-striped bg--base progress-bar-animated" role="progressbar" aria-valuemax="100" aria-valuemin="0"
                    aria-valuenow="75" style="width: 0%"><span class="progress-value">0%</span></div>
            </div>
        </div>
    </div>

@endsection

@push('style-lib')
    <link href="{{ asset('assets/global/css/select2.min.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush


@push('script')
    <script>
        'use strict';



        $('.upload-btn').on('click', function() {
            $('.file-upload-input').trigger('click');
        })


        // image upload js
        function readURL(input) {
            if (!['image/jpeg', 'image/png', 'image/jpg'].includes(input.files[0].type)) {
                notify('error', 'File type doesn\'t match');
                return false;
            }

            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {

                    $('.image-upload-wrap').hide();

                    $('.file-upload-image').attr('src', e.target.result);
                    $('.file-upload-content').show();

                    $('.image-title').html(input.files[0].name);
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                removeUpload();
            }
        }

        function removeUpload() {
            $('.file-upload-input').replaceWith($('.file-upload-input').clone());
            $('.file-upload-content').hide();
            $('.image-upload-wrap').show();
        }

        $('.image-upload-wrap').bind('dragover', function() {
            $('.image-upload-wrap').addClass('image-dropping');
        });

        $('.image-upload-wrap').bind('dragleave', function() {
            $('.image-upload-wrap').removeClass('image-dropping');
        });

        $('.remove-image').on('click', function() {
            removeUpload();
        });

        $('[name=resolution]').keypress(function(e) {
            let value = $(this).val();
            if (e.keyCode === 13) {
                if (value.indexOf('x') == -1) {
                    e.preventDefault();
                    $(this).val(value + " x ");
                }
            }
        });

        $(document).on('change', '.is_free_select', function() {
            if ($(this).val() == 1) {
                $(this).closest('.extraPriceElement').find('.price-div').addClass('d-none');
                $(this).closest('.extraPriceElement').find('.price-div input').val('0.00');
            } else {
                $(this).closest('.extraPriceElement').find('.price-div').removeClass('d-none');
            }
        })


        $(document).on('focusout', '.image-price', function() {

            let amount = parseFloat($(this).val());
            let maximumPrice = @json(gs('image_maximum_price')) * 1;
            if (amount > maximumPrice) {
                notify('error', `@lang('Price exceeds the maximum amount')`);
                $(this).val('');
                return false;
            }

            if (amount > 0) {
                let percentage = @json(gs('per_download'));
                let commission = amount * parseFloat(percentage) / 100;
                $(this).closest('.price-div').find('.commission-text').text(
                    `${commission} {{ __(gs('cur_text')) }}`);
            } else {
                $(this).closest('.price-div').find('.commission-text').text(
                    `{{ showAmount(gs('per_download'), currencyFormat: false) . '%' }}`);
            }
        });

        function validateFormData(isUpdate) {

            let error = false;
            if (!$('[name=photo]').val() && !isUpdate) {
                error = true;
                notify('error', `@lang('The photo field is required')`);
            }

            if ($('[name=photo]').val()) {
                var file = $('[name=photo]').val();
                var extension = file.substr((file.lastIndexOf('.') + 1));
                if (!['jpg', 'png', 'jpeg'].includes(extension)) {
                    error = true;
                    notify('error', `@lang('The photo must be in jpg, png or jpeg')`);
                }
            }

            if (!$('input[name=title]').val()) {
                error = true;
                notify('error', `@lang('The title field is required')`);
            }
            if (!$('select[name=category]').val()) {
                error = true;
                notify('error', `@lang('The category field is required')`);
            }
            if ($('select[name=is_free]').val() == '') {
                error = true;
                notify('error', `@lang('The is free field is required')`);
            }

            if ($('.priceInput').val() == 0 && !$('.priceInput').val()) {
                error = true;
                notify('error', `@lang('The price field is required if the resource is premium')`);
            }

            if (!$('.resolutionInput').val()) {
                error = true;
                notify('error', `@lang('The resolution field is required')`);
            }
            if (!$('select[name="colors[]"]').val().length) {
                error = true;
                notify('error', `@lang('The colors field is required')`);
            }
            if (!$('select[name="extensions[]"]').val().length) {
                error = true;
                notify('error', `@lang('The extensions field is required')`);
            }
            if (!$('select[name="tags[]"]').val().length) {
                error = true;
                notify('error', `@lang('The tags field is required')`);
            }
            if (!$('textarea[name=description]').val()) {
                error = true;
                notify('error', `@lang('The description field is required')`);
            }

            if (!$('.fileInput').val() && !isUpdate) {
                error = true;
                notify('error', `@lang('The file is required')`);
            }

            if ($('input[name=file]').val()) {
                var file = $('[name=file]').val();
                var extension = file.substr((file.lastIndexOf('.') + 1));
                if (!['zip', '7z', 'rar', 'tar', 'wim'].includes(extension)) {
                    error = true;
                    notify('error', `@lang('The file must be in zip, 7z, tar or wim')`);
                }
            }

            return error;
        }


        $('.resourceUploadForm').on('submit', function(e) {
            e.preventDefault();

            let isUpdate = {{ @$image->id ?? 0 }};
            let url = `{{ @$image->id ? route('user.image.update', $image->id) : route('user.image.store') }}`;
            let data = new FormData(this);

            if (validateFormData(isUpdate)) {
                return;
            }

            $.ajax({
                type: "post",
                url: url,
                data: data,
                contentType: false,
                processData: false,
                cache: false,
                beforeSubmit: function() {
                    $('.progress-bar').css({
                        'width': '0%'
                    });
                },
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(event) {
                        if (event.lengthComputable) {
                            var percentComplete = (event.loaded / event.total) * 100;
                            $('.progress-bar').css({
                                'width': percentComplete + '%'
                            });
                            $('.progress-value').text(percentComplete + '%');
                            $('.progess-percent').addClass('active');
                            $('body').addClass('overflow-hidden');
                        }
                    }, false);

                    return xhr;
                },
                success: function(response) {
                    if (response.status) {
                        if (!isUpdate) {
                            removeUpload();
                            $('.resourceUploadForm')[0].reset();
                            $('.select2-auto-tokenize').select2({
                                dropdownParent: $('.form-card'),
                                tags: true,
                                tokenSeparators: [',']
                            });

                            $('.select2-auto-tokenize').select2({
                                dropdownParent: $('.form-card'),
                                tags: false,
                                tokenSeparators: [',']
                            });
                        }
                        notify('success', response.success);
                    } else {
                        notify('error', response.error);
                    }

                    $('.progess-percent').removeClass('active');
                    $('body').removeClass('overflow-hidden');
                }
            });
        });




        $(document).on('click', '.addNewPrice', function() {
            let totalElement = $(document).find('.extraPriceElement').length;
            if (totalElement >= 5) {
                notify('error', `@lang('Maximum element is 5')`);
                return;
            }

            var html = `
                <div class="extraPriceElement border-bottom">
                    <div class="py-3 d-flex justify-content-between align-items-center">
                        <h5 class="my-0 pb-sm-0 pb-3">@lang('Pricing Set') - <span class="pricing-set-number"></span></h5>
                        <div class="removeButton">
                                <button  type ="button" class="btn btn-danger btn-sm removePrice d-flex align-items-center" >
                                    <i class="las la-minus-circle"></i>
                                    <span class="d-none d-sm-block ps-1">@lang('Remove')</span>
                                     </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                @lang('Resolution')
                                <i class="las la-exclamation-circle text--base" data-bs-custom-class="custom--tooltip" data-bs-toggle="tooltip" title="@lang('Seperate width and height by x, e.g. 600x1200 px')"></i>
                            </label>
                            <input class="form-control form--control resolutionInput" name="resolution[]" placeholder="@lang('e.g. 600x1200 px')" required type="text" value="{{ old('resolution') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">@lang('Zip File')</label>
                            <input @if (!@$image) required @endif accept=".zip,.7z,.rar,.tar,.wim" class="form-control form--control fileInput" name="file[]" type="file">
                        </div>
                        <div class="col-md-6 mb-3 select2-parent">
                                <label class="form-label">@lang('Status')</label>
                               
                                    <select class="form-select select2-basic" data-minimum-results-for-search="-1" name="status[]" required>
                                    
                                        <option value="1">@lang('Enable')</option>
                                        <option value="0" >@lang('Disable')</option>
                                    </select>
                              
                            </div>
                        <div class="col-md-6 mb-3 select2-parent">
                            <label class="form-label required">@lang('Premium / Free')</label>
                            
                                <select class="form-select is_free_select select2-basic" data-minimum-results-for-search="-1" name="is_free[]" required>
                                    <option @selected(old('is_free', @$image->is_free) == 0) value="0">
                                        @lang('Premium')
                                    </option>
                                    <option @selected(old('is_free', @$image->is_free) == 1) value="1">
                                        @lang('Free')
                                    </option>
                                
                                </select>
                          
                        </div>
                        <div class="price-div col-12 mb-3">
                                <label class="form-label">@lang('Price')
                                    (@lang('You will get') <span class="commission-text fw-bold">
                                        {{ showAmount(gs('per_download'), currencyFormat: false) . '%' }} </span>
                                    @lang(' in each download'))
                                </label>
                                <div class="input-group input--group">
                                    <input class="form-control form--control image-price priceInput"
                                        max="{{ gs('image_maximum_price') }}" min="0"
                                        name="price[]" step="any" type="number"
                                        value="{{ old('price') }}">
                                    <span class="input-group-text">
                                        {{ __(gs('cur_text')) }}
                                    </span>
                                </div>
                                <small class="d-block maximum-price">@lang('Maximum price') <span class="fw-bold">{{ showAmount(gs('image_maximum_price')) }} </span></small>
                            </div>

                            
                        </div>
                    </div>
                    
                </div>
                `;
            $('.priceNewElement').append(html);
            resetRemoveButtionVisibility();
            $('[data-bs-toggle="tooltip"]').tooltip();

            $('.select2-basic').select2();
        });

        $(document).on('click', '.removePrice', function() {
            if ($(document).find('.removePrice').length > 1) $(this).closest('.extraPriceElement').remove();
            resetRemoveButtionVisibility();
            if ($(this).data('id')) $('.removed_file').append(
                `<input type="hidden" name="removed_file[]" value="${$(this).data('id')}">`);
        });

        function resetRemoveButtionVisibility() {
            let buttons = $(document).find('.removeButton');
            if (buttons.length == 1) buttons.fadeOut();
            else buttons.fadeIn();
            $.each($('.pricing-set-number'), function(i, e) {
                $(e).text(i + 1);
            });
        }

        resetRemoveButtionVisibility();

        $('#video-upload').on("change", function() {

            const fileInput = $(this);
            const fileSize = fileInput[0].files[0].size;
            const maxSize = 15 * 1024 * 1024;

            if (fileSize > maxSize) {
                $(`button[type="submit"]`).prop('disabled', true);
                notify('error', "@lang('Maximum file upload is: 10MB')")
            } else {
                $(`button[type="submit"]`).prop('disabled', false);
            }
        });

        $(`select[name=file_type]`).on('change', function() {
            const fileExtensions = $(this).find(`option:selected`).data('extesnion');
            let html = "";

            $.each((fileExtensions ?? []), function(i, fileExtension) {
                html += `<option value="${fileExtension}">${fileExtension}</option>`;
            });

            $(`select[name="extensions[]"]`).html(html);

        });
    </script>
@endpush

@push('style')
    <style>
        /* start progress bar */
        .progess-percent {
            position: fixed;
            width: 100%;
            height: 100%;
            left: 0;
            top: 0;
            opacity: 0;
            z-index: -1;
        }

        .progess-percent.active {
            background-color: #000000a8;
            z-index: 1111;
            opacity: 1;
        }

        .progress {
            width: 300px;
            border-radius: 5px;
            height: 24px;
        }

        .progress-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .progress-bar {
            height: 100% !important;
            font-size: 16px;
            line-height: 25px;
        }

        .maximum-price {
            font-size: 13px;
            font-weight: 500;
            color: hsl(var(--heading));
        }

        .photo-upload-area {
            background-color: #ffffff;
        }

        .photo-upload-area .image-upload {
            width: 100%;
        }

        .file-upload {
            background-color: #ffffff;
            margin: 0 auto;
        }

        .file-upload .file-upload-btn {
            width: 100%;
            margin: 0;
            color: #fff;
            background: #0062FF;
            border: none;
            padding: 10px;
            border-radius: 4px;
            border-bottom: 4px solid #004ecc;
            transition: all .2s ease;
            outline: none;
            text-transform: uppercase;
            font-weight: 700;
        }

        .file-upload .file-upload-btn:hover {
            background: #0058e6;
            color: #ffffff;
            transition: all .2s ease;
            cursor: pointer;
        }

        .file-upload .file-upload-btn.active {
            border: 0;
            transition: all .2s ease;
        }

        .file-upload .file-upload-input {
            position: absolute;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            outline: none;
            opacity: 0;
            cursor: pointer;
        }

        .file-upload .image-upload-wrap {
            position: relative;
            border: 2px dashed #dddddd;
            border-radius: 5px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            -ms-border-radius: 5px;
            -o-border-radius: 5px;
            height: 356px;
        }

        .file-upload .image-dropping:hover,
        .file-upload .image-upload-wrap:hover {
            background-color: #e4e4e4;
        }

        .file-upload .image-title-wrap {
            padding: 0 15px 15px 15px;
            color: #222;
        }

        .file-upload .drag-text {
            text-align: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .file-upload .drag-text .title {
            font-size: 22px;
            color: #6d6d6d;
            text-shadow: 0 5px 5px rgba(0, 0, 0, 0.15);
            cursor: pointer;
        }

        .file-upload .file-upload-image {
            width: 100%;
            height: 100%;
            border-radius: 5px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            -ms-border-radius: 5px;
            -o-border-radius: 5px;
        }

        .file-upload .file-upload-content {
            height: 356px;
        }

        @media only screen and (max-width: 768px) {
            .file-upload .image-upload-wrap {
                height: 300px;
            }

            .file-upload .file-upload-content {
                height: 300px;
            }

            .file-upload .drag-text .title {
                font-size: 20px;
            }
        }
    </style>
@endpush
