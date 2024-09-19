@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="custom--table-container table-responsive--md">
        <table class="custom--table table">
            <thead>
                <tr>
                    <th>@lang('User')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Time')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $k => $data)
                    <tr>
                        <td>
                            {{ __($data->referee->fullname) }} <br>
                            <a href="{{ route('member.images', $data->referee->username) }}">{{ $data->referee->username }}</a>
                        </td>
                        <td>
                            {{ getAmount($data->amount) }} {{ __(gs('cur_text')) }}
                        </td>

                        <td>
                            {{ showDateTime($data->created_at) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="sm-text text-center" colspan="3">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($logs->hasPages())
            <div class=" my-3">
                {{ paginateLinks($logs) }}
            </div>
        @endif


    </div>
@endsection
