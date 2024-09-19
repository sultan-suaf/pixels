@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="section--xl">
        <div class="container custom--container">
            <div class="row g-4">
                <div class="col-12">
                    <h2 class="mt-0 mb-2">{{ __($pageTitle) }} ( {{ shortNumber($collectionImages->total()) }} )</h2>
                    @if ($collection->description)
                        <p>{{ __($collection->description) }}</p>
                    @endif
                </div>
                <div class="col-12">
                    @if ($collectionImages->count())
                        @include($activeTemplate . 'partials.image_grid', ['images' => $collectionImages, 'class' => 'gallery'])
                    @else
                        <div class="text-center">
                            <img src="{{ getImage('assets/images/empty_message.png') }}" alt="@lang('image')">
                        </div>
                    @endif
                </div>
            </div>

            @if ($collectionImages->hasPages())
                <div class="d-flex justify-content-end my-3">
                    {{ paginateLinks($collectionImages) }}
                </div>
            @endif
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
            loggedIn: "{{ route('user.login') }}",
            updateLike: "{{ route('user.image.like.update') }}"

        };
        let likeParams = {
            loggedStatus: @json(Auth::check()),
            csrfToken: "{{ csrf_token() }}"
        }
    </script>
    <script src="{{ asset($activeTemplateTrue . 'js/like.js') }}"></script>
@endpush
