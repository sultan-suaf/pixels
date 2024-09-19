@extends('reviewer.layouts.master')

@section('content')

@php
    $sidenav = file_get_contents(resource_path('views/reviewer/partials/sidenav.json'));
@endphp

    <div class="page-wrapper default-version">
        @include('reviewer.partials.sidenav')
        @include('reviewer.partials.topnav')

        <div class="body-wrapper">
            <div class="bodywrapper__inner">
                @include('reviewer.partials.breadcrumb')
                @yield('panel')
            </div>
        </div>
    </div>
@endsection
