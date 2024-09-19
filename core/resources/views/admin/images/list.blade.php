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
                                    <th>@lang('Image')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Likes')</th>
                                    <th>@lang('Views')</th>
                                    <th>@lang('Downloads')</th>
                                    <th>@lang('Featured')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($images as $image)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.images.details', $image->id) }}">
                                                <div class="user gap-2">
                                                    <div class="thumb">
                                                        <img src="{{ imageUrl(getFilePath('stockImage') , $image->image_name) }}" alt="@lang('image')">
                                                    </div>
                                                    <div>
                                                        {{ __($image->title) }}
                                                    </div>
                                                </div>
                                            </a>
                                        </td>

                                        <td>
                                            <span class="fw-bold">
                                                {{ __(@$image->category->name) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ __(@$image->user->fullname) }}</span>
                                            <small>
                                                <a href="{{ route('admin.users.detail', $image->user->id) }}"><span>@</span>{{ $image->user->username }}</a>
                                            </small>
                                        </td>
                                        <td>
                                            {{ shortNumber($image->total_like) }}
                                        </td>
                                        <td>
                                            {{ shortNumber($image->total_view) }}
                                        </td>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.images.download.log', $image->id) }}">
                                                {{ shortNumber($image->total_downloads) }}
                                            </a>
                                        </td>
                                        <td>
                                            @if ($image->is_featured)
                                                <span class="badge badge--success">@lang('Featured')</span>
                                            @else
                                                <span class="badge badge--dark">@lang('Unfeatured')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                echo $image->statusBadge;
                                            @endphp
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-1 flex-wrap">
                                                <a href="{{ route('admin.images.details', $image->id) }}" class="btn btn-sm btn-outline--primary">
                                                    <i class="la la-desktop"></i>@lang('Details')
                                                </a>

                                                @if ($image->is_featured)
                                                    <button data-action="{{ route('admin.images.feature.update', $image->id) }}" data-question="@lang('Are you sure to unfeature this image?')" class="btn btn-sm btn-outline--dark confirmationBtn" @if($image->status != Status::IMAGE_APPROVED) disabled @endif>
                                                        <i class="la la-times"></i>@lang('Unfeature')
                                                    </button>
                                                @else
                                                    <button data-action="{{ route('admin.images.feature.update', $image->id) }}" data-question="@lang('Are you sure to featured this image?')" class="btn btn-sm btn-outline--success confirmationBtn" @if($image->status != Status::IMAGE_APPROVED) disabled @endif>
                                                        <i class="la la-ribbon"></i>@lang('Feature')
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
                @if ($images->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($images) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by User/Image/Category" />
@endpush

@push('style')
    <style>
        .breadcrumb-plugins .input-group {
            width: 330px !important;
        }
    </style>
@endpush
