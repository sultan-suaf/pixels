@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Collection Title')</th>
                                    <th>@lang('Total Image')</th>
                                    <th>@lang('Visibility')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($collections as $collection)
                                    <tr>
                                        <td>
                                            <span class="fw-bold d-block">{{ __(@$collection->user->fullname) }}</span>
                                            <a href="{{ route('admin.users.detail', $collection->user_id) }}"><span>@</span>{{ @$collection->user->username }}</a>
                                        </td>
                                        <td>
                                            <span class="fw-bold">
                                                {{ __($collection->title) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.images.all') }}?search={{ $collection->title }}">
                                                {{ $collection->collection_image_count }}
                                            </a>
                                        </td>
                                        <td>
                                            @if ($collection->is_public)
                                                @lang('Public')
                                            @else
                                                @lang('Private')
                                            @endif
                                        </td>
                                        <td>
                                            @if ($collection->is_featured)
                                                <button data-action="{{ route('admin.report.user.image.collections.featured', $collection->id) }}" data-question="@lang('Are you sure to unfeature this collection?')" class="btn btn-sm btn-outline--dark confirmationBtn">
                                                    <i class="la la-times"></i>@lang('Unfeature')
                                                </button>
                                            @else
                                                <button data-action="{{ route('admin.report.user.image.collections.featured', $collection->id) }}" data-question="@lang('Are you sure to featured this collection?')" class="btn btn-sm btn-outline--success confirmationBtn">
                                                    <i class="la la-ribbon"></i>@lang('Feature')
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($collections->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($collections) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    <x-confirmation-modal />
@endsection
@push('breadcrumb-plugins')
    <x-search-form />
@endpush
