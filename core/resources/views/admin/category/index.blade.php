@php
    $categoryImage = getImage(getFilePath('category'), getFileSize('category'));
@endphp
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
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-end justify-content-md-start gap-2">
                                                <div class="avatar avatar--sm">
                                                    <img src="{{ getImage(getFilePath('category') . '/' . $category->image, getFileSize('category')) }}"
                                                        alt="@lang('Category')">
                                                </div>
                                                {{ __($category->name) }}
                                            </div>
                                        </td>
                                        <td>
                                            {{ $category->slug }}
                                        </td>

                                        <td>
                                            @php echo $category->statusBadge; @endphp
                                        </td>
                                        @php
                                            $category->image_with_path = getImage(
                                                getFilePath('category') . '/' . $category->image,
                                                getFileSize('category'),
                                            );
                                        @endphp
                                        <td>
                                            <div class="d-flex justify-content-end flex-wrap gap-1">
                                                <button class="btn btn-outline--primary cuModalBtn editBtn btn-sm" data-modal_title="@lang('Update Category')"
                                                    data-resource="{{ $category }}">
                                                    <i class="las la-pen"></i>@lang('Edit')
                                                </button>
                                                @if ($category->status == Status::ENABLE)
                                                    <button class="btn btn-outline--danger btn-sm confirmationBtn" data-question="@lang('Are you sure to disable this category?')"
                                                        data-action="{{ route('admin.category.status', $category->id) }}">
                                                        <i class="las la-eye-slash"></i>@lang('Disable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline--success confirmationBtn btn-sm" data-question="@lang('Are you sure to enable this category?')"
                                                        data-action="{{ route('admin.category.status', $category->id) }}">
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
                @if ($categories->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($categories) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="cuModal" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.category.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <div class="form-group">
                            <label>@lang('Upload Image')</label>
                            <x-image-uploader class="w-100" type="category" />
                        </div>

                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input class="form-control" name="name" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Slug')</label>
                            <input class="form-control" name="slug" type="text" required>
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

@push('breadcrumb-plugins')
    <x-search-form />
    <button class="btn btn-outline--primary h-45 cuModalBtn" data-image="{{ getImage(getFilePath('category') . '/', getFileSize('category')) }}"
        data-modal_title="@lang('Add Category')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $("input[name=name]").on('keyup', function() {
                let name = $(this).val();
                var slug = slugify(name);
                $("input[name=slug]").val(slug)
            })

            $('.editBtn').on('click', function() {
                $("input[name=image]").removeAttr('required');
            })



            function slugify(text) {
                return text.toString().toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w\-]+/g, '')
                    .replace(/\-\-+/g, '-')
                    .replace(/^-+/, '')
                    .replace(/-+$/, '');
            }

        })(jQuery);
    </script>
@endpush
