@php
    $content = getContent('premium.content', true);
@endphp

@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="section member-page">
        <div class="container custom--container">
            <div class="row g-4 justify-content-center">
                <div class="col-12">
                    <h2 class="mt-0 mb-2">{{ __($pageTitle) }}</h2>
                    <p class="mb-0">
                        {{ __($content->data_values->title) }}
                    </p>
                </div>
                @if ($images->count())
                    <div class="col-12">
                        @include($activeTemplate . 'partials.image_grid', ['images' => $images, 'class' => 'gallery'])
                    </div>
                @else
                    <div class="col-12 text-center">
                        <img src="{{ getImage('assets/images/empty_message.png') }}" alt="@lang('Image')">
                    </div>
                @endif

                @if (@$images->hasPages())
                    <div class="col-12">
                        <div class="search-page__pagination text-center py-3">
                            {{ $images->links($activeTemplate . 'partials.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('modal')
    @include($activeTemplate . 'partials.login_modal')
    @include($activeTemplate . 'partials.collection_modal')
    @include($activeTemplate . 'partials.share_modal')
@endpush

@push('script')
    <script>
        "use strict";

        let likeRoutes = {
            updateLike: "{{ route('user.image.like.update') }}"

        };
        let likeParams = {
            loggedStatus: @json(Auth::check()),
            csrfToken: "{{ csrf_token() }}"
        }
    </script>
    <script src="{{ asset($activeTemplateTrue . 'js/like.js') }}"></script>
@endpush
