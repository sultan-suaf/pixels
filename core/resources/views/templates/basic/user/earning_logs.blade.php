@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="custom--table-container table-responsive--md">
        <table class="table custom--table">
            <thead>
                <tr>
                    <th class="sm-text">@lang('Date')</th>
                    <th class="sm-text">@lang('Image Title')</th>
                    <th class="sm-text">@lang('Amount')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $key=>$log)
                    <tr>
                        <td class="sm-text">
                            {{ showDateTime($log->earning_date, 'd M, Y') }}
                        </td>
                        <td class="sm-text">
                            {{ __($log->imageFile->image->title) }} |
                            {{ __($log->imageFile->resolution) }}
                        </td>

                        <td class="sm-text">
                            {{ showAmount($log->amount) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center sm-text">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($logs->hasPages())
            <div class="mt-4">
                {{ paginateLinks($logs) }}
            </div>
        @endif
    </div>
@endsection
