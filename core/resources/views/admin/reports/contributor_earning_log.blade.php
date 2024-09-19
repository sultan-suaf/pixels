@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Contributor')</th>
                                    <th>@lang('Image Title')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>
                                            <span class="fw-bold d-block">{{ __(@$log->contributor->fullname) }}</span>
                                            <a href="{{ route('admin.users.detail', @$log->contributor_id) }}"><span>@</span>{{ @$log->contributor->username }}</a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.images.details', $log->imageFile->image_id) }}">
                                                {{ __(@$log->imageFile->image->title) }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ showDateTime($log->earning_date, 'd M,Y') }}
                                        </td>
                                        <td>
                                            <span class="fw-bold">
                                                {{ __(showAmount($log->amount)) }}
                                            </span>
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
                @if ($logs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($logs) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection
