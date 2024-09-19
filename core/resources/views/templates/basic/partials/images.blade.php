@if (count($images))
    <div class="section--sm section--bottom">
        <div class="container custom--container">
            <div class="row g-4">
                <div class="col-12">
                    @include($activeTemplate . 'partials.image_grid', ['images' => $images, 'class' => 'gallery'])
                </div>
                @if (count($images) >= 28)
                    <div class="col-12">
                        <div class="text-center">
                            <a href="{{ route('search', ['type' => 'image']) }}" class="base-btn">
                                @lang('View More')
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

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
