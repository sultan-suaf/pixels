@extends($activeTemplate . 'layouts.frontend')
@section('content')

    @if (gs('banner') == Status::BANNER_TWO)
        @include($activeTemplate . 'sections.banner_two')
    @else
        @include($activeTemplate . 'sections.banner')
    @endif

    @include($activeTemplate . 'sections.category')
    @include($activeTemplate . 'sections.images', ['images' => $images])

    @if ($sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
