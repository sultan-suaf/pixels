@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="section member-page">
        <div class="container">
            <div class="row g-4">
                <div class="col-12">
                    <h2 class="mt-0 mb-2">@lang('Collections') ({{ shortNumber($collections->total()) }})</h2>
                </div>
                @if ($collections->count())
                    @include($activeTemplate . 'partials.collection_grid', ['collections' => $collections])
                @else
                    <div class="d-flex justify-content-center align-items-center">
                        <img src="{{ getImage('assets/images/empty_message.png') }}" alt="@lang('Image')">
                    </div>
                @endif
            </div>
            @if ($collections->hasPages())
                <div class="mt-5">
                    {{ paginateLinks($collections) }}
                </div>
            @endif
        </div>
    </div>
@endsection
