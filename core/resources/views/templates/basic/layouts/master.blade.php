@extends($activeTemplate . 'layouts.app')
@section('panel')
    @include($activeTemplate . 'partials.header')

    <div class="dashboard-section">
        <div class="section">
            <div class="container">
                <div class="row g-4 gy-lg-0">
                    <div class="col-lg-4 col-xl-3">
                        @include($activeTemplate . 'partials.user_nav')
                    </div>
                    <div class="col-lg-8 col-xl-9">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include($activeTemplate . 'partials.footer')
@endsection
