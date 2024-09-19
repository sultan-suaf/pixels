@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Slug')</th>
                                    <th>@lang('Icon')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($fileTypes as $fileType)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-end justify-content-md-start gap-2">
                                                <div class="avatar avatar--sm">
                                                    <img src="{{ getImage(getFilePath('fileType') . '/' . $fileType->image, getFileSize('fileType')) }}"
                                                        alt="@lang('Category')" />
                                                </div>
                                                <span>{{ __($fileType->name) }}</span>
                                            </div>
                                        </td>
                                        <td> {{ __($fileType->slug) }} </td>
                                        <td>@php echo $fileType->icon @endphp</td>
                                        <td>@php echo $fileType->statusBadge; @endphp</td>

                                        @php
                                            $image_with_path = [
                                                getImage(getFilePath('fileType') . '/' . $fileType->image, getFileSize('fileType')),
                                                getImage(
                                                    getFilePath('fileTypeCollection') . '/' . $fileType->collection_image,
                                                    getFileSize('fileTypeCollection'),
                                                ),
                                            ];
                                            $fileType->image_with_path = $image_with_path;
                                        @endphp
                                        <td>
                                            <div class="d-flex justify-content-end flex-wrap gap-1">
                                                <button class="btn btn-outline--primary cuModalBtn btn-sm editBtn" data-modal_title="@lang('Update Category')"
                                                    data-resource="{{ $fileType }}">
                                                    <i class="las la-pen"></i>@lang('Edit')
                                                </button>
                                                @if ($fileType->status == Status::ENABLE)
                                                    <button class="btn btn-outline--danger btn-sm confirmationBtn" data-question="@lang('Are you sure to disable this filetype?')"
                                                        data-action="{{ route('admin.filetype.status', $fileType->id) }}">
                                                        <i class="las la-eye-slash"></i>@lang('Disable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline--success confirmationBtn btn-sm" data-question="@lang('Are you sure to enable this filetype?')"
                                                        data-action="{{ route('admin.filetype.status', $fileType->id) }}">
                                                        <i class="las la-eye"></i>@lang('Enable')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">
                                            {{ __($emptyMessage) }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($fileTypes->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($fileTypes) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="cuModal" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.filetype.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Upload Banner Image')</label>
                                    <x-image-uploader class="w-100" type="fileType" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Upload collection Image')</label>
                                    <x-image-uploader class="w-100" name="collection_image" type="fileTypeCollection" id="collection_image" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Banner Video')</label>
                                    <div class="input-group">
                                        <input class="form-control" name="video" type="file" accept="video/mp4" id="video-upload" />
                                        <a class="input-group-text video_download" href="#" download=""><i class="las la-download"></i></a>
                                        <a class="input-group-text remove_video" href="#"><i class="las la-times"></i></a>
                                    </div>
                                    <small class="text-muted mt-3">
                                        @lang('Supported Files:')
                                        <b>@lang('.mp4'), @lang('.3gp')</b>
                                        @lang('& maximum file size is ')
                                        <b class="image--upload-size">@lang('15MB')</b>
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Icons')</label>
                                    <div class="input-group">
                                        <input class="form-control iconPicker icon" name="icon" type="text" value="" autocomplete="off"
                                            required />
                                        <span class="input-group-text input-group-addon" data-icon="las la-home" role="iconpicker"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input class="form-control" name="name" type="text" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Slug')</label>
                                    <input class="form-control" name="slug" type="text" required />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Supported File Extension')</label>
                                    <div id="supported-file-extension-wrapper">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">
                            @lang('Submit')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form />
    @php
        $imagePath = [getImage(null, getFileSize('fileType')), getImage(null, getFileSize('fileTypeCollection'))];
    @endphp

    <button class="btn btn-outline--primary h-45 cuModalBtn addBtn" data-image_path="{{ json_encode($imagePath) }}"
        data-modal_title="@lang('Add Filetype')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('style-lib')
    <link href="{{ asset('assets/admin/css/fontawesome-iconpicker.min.css') }}" rel="stylesheet" />
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/fontawesome-iconpicker.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            let cuModal = $("#cuModal");

            $("input[name=name]").on("keyup", function() {
                let subCategory = $(this).val();
                var slug = slugify(subCategory);
                $("input[name=slug]").val(slug);
            });

            function slugify(text) {
                return text.toString()
                    .toLowerCase()
                    .replace(/\s+/g, "-")
                    .replace(/[^\w\-]+/g, "")
                    .replace(/\-\-+/g, "-")
                    .replace(/^-+/, "")
                    .replace(/-+$/, "");
            }

            $(".editBtn").on("click", function() {

                let resource = $(this).data("resource");
                let html = `<option value="" disabled>@lang('Type Extension')</option>`;

                $.each(resource.supported_file_extension, function(i, element) {
                    html += `<option value="${element}" selected>${element}</option>`;
                });

                $(`#supported-file-extension-wrapper`).html(`<select name="file_extension[]" class="form-control" multiple>${html}</select>`);
                $("#supported-file-extension-wrapper select").select2({
                    tags: true,
                    tokenSeparators: [","],
                    dropdownParent: $('#cuModal')
                });

                $(".input-group-addon").html(resource.icon);

                let video = resource.video;

                if (video) {
                    let videoLink = `{{ asset(getFilePath('fileTypeVideo') . '/' . ':video') }}`.replace(":video", video);
                    let videoRemoveUrl = `{{ route('admin.filetype.video.remove', ':id') }}`.replace(":id", resource.id);

                    $(".video_download, .remove_video").show();
                    $(".video_download").attr("href", videoLink);
                    $(".remove_video").attr("href", videoRemoveUrl);
                } else {
                    $(".video_download, .remove_video").hide();
                }
            });

            $(".iconPicker")
                .iconpicker()
                .on("iconpickerSelected", function(e) {
                    $(this).closest(".form-group").find(".iconpicker-input").val(`<i class="${e.iconpickerValue}"></i>`);
                });

            $(".addBtn").on("click", function(e) {
                $(`#supported-file-extension-wrapper`).html(`<select class="form-control" name="file_extension[]" multiple></select>`);
                $("#supported-file-extension-wrapper select").select2({
                    tags: true,
                    tokenSeparators: [","],
                    dropdownParent: $('#cuModal')
                });
                $(".video_download, .remove_video").hide();
            });

            $("#video-upload").on("change", function() {
                const fileInput = document.getElementById("video-upload");
                const fileSize = fileInput.files[0].size;
                const maxSize = 15 * 1024 * 1024;

                if (fileSize > maxSize) {
                    $(`button[type="submit"]`).prop("disabled", true);
                    notify("error", "@lang('Maximum file upload is: 10MB')");
                } else {
                    $(`button[type="submit"]`).prop("disabled", false);
                }
            });
        })(jQuery);
    </script>
@endpush
