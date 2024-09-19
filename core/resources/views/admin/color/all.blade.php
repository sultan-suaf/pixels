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
                                    <th>@lang('S.N')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Color Code')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($colors as $color)
                                    <tr>
                                        <td>
                                            {{ $colors->firstItem() + $loop->index }}
                                        </td>

                                        <td>
                                            <span class="d-flex align-items-center justify-content-center gap-2">
                                                <span class="color-indicator"
                                                    style="@if ($color->color_code != 'ffffff' && $color->color_code != 'fff') border-color:#{{ $color->color_code }}; @endif background: #{{ $color->color_code }};"></span>
                                                {{ __($color->name) }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ $color->color_code }}
                                        </td>

                                        <td>
                                            <div class="d-flex justify-content-end flex-wrap gap-2">
                                                <button class="btn btn-outline--primary cuModalBtn editBtn btn-sm" data-modal_title="@lang('Update Color')"
                                                    data-resource="{{ $color }}">
                                                    <i class="las la-pen"></i>@lang('Edit')
                                                </button>
                                                <button class="btn btn-outline--danger btn-sm confirmationBtn"
                                                    data-action="{{ route('admin.color.delete', $color->id) }}" data-question="@lang('Are you sure to delete this color?')">
                                                    <i class="las la-trash-alt"></i>@lang('Delete')
                                                </button>
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
                @if ($colors->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($colors) }}
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
                <form action="{{ route('admin.color.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input class="form-control" name="name" type="text" required>
                        </div>
                        <div class="form-group">
                            <label> @lang('Color Code')</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 p-0">
                                    <input class="form-control customColorPicker" type='text' />
                                </span>
                                <input class="form-control colorCode" name="color_code" type="text" />
                            </div>
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
    <button class="btn btn-outline--primary h-45 cuModalBtn" data-modal_title="@lang('Add Color')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/spectrum.js') }}"></script>
@endpush

@push('style-lib')
    <link href="{{ asset('assets/admin/css/spectrum.css') }}" rel="stylesheet">
@endpush

@push('script')
    <script>
        "use strict";
        var changeFun = function(color) {
            $(this).parent().siblings('.colorCode').val(color.toHexString().replace(/^#?/, ''));
        };
        $('.customColorPicker').spectrum({
            color: $(this).data('color'),
            change: changeFun
        });

        $('.colorCode').on('input', function() {
            var clr = $(this).val();
            $(this).parents('.input-group').find('.customColorPicker').spectrum({
                color: clr,
                change: changeFun
            });
        });

        $('.editBtn').on('click', function() {
            let colorCode = '#' + $(this).data('resource').color_code;
            $('.customColorPicker').spectrum({
                color: colorCode,
                change: changeFun
            });
        })

        $('.colorPicker').spectrum({
            color: $(this).data('color'),
            change: function(color) {
                $(this).parent().siblings('.colorCode').val(color.toHexString().replace(/^#?/, ''));
            }
        });

        $('.colorCode').on('input', function() {
            var clr = $(this).val();
            $(this).parents('.input-group').find('.colorPicker').spectrum({
                color: clr,
            });
        });
    </script>
@endpush

@push('style')
    <style>
        .color-indicator {
            display: inline-block;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            border: 1px solid #cfd9e0;
            background: #fff;
        }
    </style>
@endpush
