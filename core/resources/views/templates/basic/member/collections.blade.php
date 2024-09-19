@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @include($activeTemplate . 'partials.member_nav')
    <!-- User Gallery  -->
    <div class="user-gallery section--sm d-flex align-items-center">
        <div class="container custom--container">
            <div class="row g-4 justify-content-center">
                @if ($collections->count())
                    @include($activeTemplate . 'partials.collection_grid', ['collections' => $collections])
                @else
                    <div class="text-center">
                        <img src="{{ getImage('assets/images/empty_message.png') }}" alt="@lang('Image')">
                    </div>
                @endif
                @if ($collections->hasPages())
                    <div class="search-page__pagination text-center py-3">
                        {{ $collections->appends(request()->all())->links($activeTemplate . 'partials.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- User Gallery End -->
@endsection
