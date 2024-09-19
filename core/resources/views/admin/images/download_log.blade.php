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
                                    @if (request()->routeIs('admin.report.download.log'))
                                        <th>@lang('Image')</th>
                                    @endif
                                    <th>@lang('Download By')</th>
                                    <th>@lang('Contributor')</th>
                                    <th>@lang('Downloader Ip')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        @if (request()->routeIs('admin.report.download.log'))
                                            <td>
                                                <a href="{{ route('admin.images.details', $log->imageFile->image->id) }}">
                                                    <div class="user gap-2">
                                                        <div class="thumb">
                                                            <img src="{{ getImage(getFilePath('stockImage') . '/' . @$log->imageFile->image->image_name) }}" alt="@lang('image')">
                                                        </div>
                                                        <div>
                                                            {{ __(@$log->imageFile->image->title) }}
                                                        </div>
                                                    </div>
                                                </a>
                                            </td>
                                        @endif
                                        <td>
                                            @if ($log->user_id)
                                                <span class="d-block"> {{ __($log->user->fullname) }}</span>
                                                <small>
                                                    <a href="{{ route('admin.users.detail', $log->user->id) }}"><span>@</span>{{ $log->user->username }}</a>
                                                </small>
                                            @else
                                                @lang('Unknown User')
                                            @endif
                                        </td>

                                        <td>
                                            <span class="d-block">{{ __(@$log->contributor->fullname) }}</span>
                                            <small>
                                                <a href="{{ route('admin.users.detail', @$log->contributor->id) }}"><span>@</span>{{ @$log->contributor->username }}</a>
                                            </small>
                                        </td>
                                        <td>
                                            <a href="https://www.ip2location.com/{{ $log->ip }}" target="_blank">{{ $log->ip }}</a>
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
                @if ($logs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($logs) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
