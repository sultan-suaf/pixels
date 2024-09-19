@extends($activeTemplate . 'layouts.app')
@section('panel')
    @if (request()->routeIs('search*'))
        @include($activeTemplate . 'partials.search_header')
    @else
        @include($activeTemplate . 'partials.header')
    @endif
    <main class="for-blur">
        @yield('content')
    </main>

    @stack('modal')
    @include($activeTemplate . 'partials.footer')
@endsection
