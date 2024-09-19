@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @include($activeTemplate . 'partials.member_nav')

    <!-- User Gallery  -->
    <div class="user-gallery section--sm d-flex align-items-center">
        <div class="container custom--container">

            <div class="row">
                <div class="col-12">
                    @if ($images->count())
                        @include($activeTemplate . 'partials.image_grid', ['images' => $images, 'class' => 'gallery'])
                    @else
                        <div class="text-center">
                            <img src="{{ getImage('assets/images/empty_message.png') }}" alt="@lang('Image')">
                        </div>
                    @endif
                </div>



                @if ($images->hasPages())
                    <div class="search-page__pagination text-center py-3">
                        {{ $images->appends(request()->all())->links($activeTemplate . 'partials.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('modal')
    @include($activeTemplate . 'partials.collection_modal')
    @include($activeTemplate . 'partials.share_modal')
@endpush

@push('script')
    <script>
        "use strict";

        let likeRoutes = {
            updateLike: "{{ route('user.image.like.update') }}"

        }
        let likeParams = {
            loggedStatus: @json(Auth::check()),
            csrfToken: "{{ csrf_token() }}"
        }
    </script>
    <script src="{{ asset($activeTemplateTrue . 'js/like.js') }}"></script>
@endpush
