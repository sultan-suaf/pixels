@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-header">
            <h5 class="card-title">@lang('KYC Form')</h5>
        </div>
        <div class="card-body p-0">
            @if ($user->kyc_data)
                <ul class="list-group list-group-flush">
                    @foreach ($user->kyc_data as $val)
                        @continue(!$val->value)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ __($val->name) }}
                            <span>
                                @if ($val->type == 'checkbox')
                                    {{ implode(',', $val->value) }}
                                @elseif($val->type == 'file')
                                    <a href="{{ route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $val->value)) }}"><i class="fa fa-file"></i> @lang('Attachment') </a>
                                @else
                                    {{ __($val->value) }}
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <h5 class="text-center">@lang('KYC data not found')</h5>
            @endif
        </div>
    </div>
@endsection
