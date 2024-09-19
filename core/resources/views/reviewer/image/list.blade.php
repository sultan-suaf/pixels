@extends('reviewer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Total Files')</th>
                                    @if (!request()->routeIs('reviewer.images.pending'))
                                        <th>@lang('Reviewed By')</th>
                                    @endif
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($images as $image)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb"><img src="{{ imageUrl(getFilePath('stockImage'), $image->thumb) }}" alt="image"></div>
                                                <span class="name">
                                                    <a href="{{ route('reviewer.images.detail', $image->id) }}">{{ $image->title }}</a>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="fw-bold">
                                            {{ __($image->category->name) }}
                                        </td>
                                        <td>
                                            {{ __($image->user->username) }}
                                        </td>
                                        <td>
                                            {{ __($image->total_files) }}
                                        </td>
                                        @if (!request()->routeIs('reviewer.images.pending'))
                                            <td>
                                                @if ($image->admin_id)
                                                    {{ __($image->admin->name) }}
                                                @elseif($image->reviewer_id)
                                                    {{ __($image->reviewer->name) }}
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            <a href="{{ route('reviewer.images.detail', $image->id) }}" class="btn btn-outline--primary btn-sm">
                                                <i class="las la-desktop"></i>@lang('Detail')
                                            </a>
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
                @if ($images->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($images) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search..." />
@endpush
