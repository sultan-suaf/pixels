@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="search-page">

        <div class="search-page__filter">
            <div class="filter-sidebar--backdrop"></div>
            @include($activeTemplate . 'partials.search_bar')
        </div>
        <div class="search-page__body">

            @if (request()->type != 'collection' && $categories->count() > 1)
                <div class="search-category">
                    <div class="search-category__list">
                        <button class="search-category__btn search-param" data-param="category" data-param_value=""
                            data-search_type="single">@lang('All Category')</button>
                    </div>
                    @foreach ($categories as $category)
                        <div class="search-category__list">
                            <button class="search-category__btn search-param" data-param="category" data-param_value="{{ $category->slug }}"
                                data-search_type="single">{{ __($category->name) }}</button>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Tab Menu  -->
            @if (request()->filter)
                <h1 class="text-muted my-4 text-center">@lang('Showing results for') <span class="fw-bold text--dark">{{ request()->filter }}</span></h1>
            @endif

            <div class="tab-menu">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-menu__content">
                                <a class="tab-menu__link search-images @if (request()->type == 'image') active @endif" href="javascript:void(0)"> <i
                                        class="las la-image"></i> @lang('Images') ({{ $imageCount }}) </a>

                                <a class="tab-menu__link search-collections @if (request()->type == 'collection') active @endif" href="javascript:void(0)"> <i
                                        class="las la-folder-plus"></i> @lang('Collections') ({{ $collectionCount }}) </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tab Menu End -->

            @if (request()->type == 'image' && $images->count())
                @include($activeTemplate . 'partials.image_grid', ['images' => $images, 'class' => 'gallery'])
                @if ($images->hasPages())
                    <div class="search-page__pagination py-3 text-center">
                        {{ $images->appends(request()->all())->links($activeTemplate . 'partials.paginate') }}
                    </div>
                @endif
            @elseif(request()->type == 'collection' && $collections->count())
                <div class="pb-2">
                    <div class="row g-4 justify-content-center">
                        @include($activeTemplate . 'partials.collection_grid', ['collections' => $collections])
                    </div>
                </div>

                @if ($collections->hasPages())
                    <div class="search-page__pagination py-3 text-center">
                        {{ $collections->appends(request()->all())->links($activeTemplate . 'partials.paginate') }}
                    </div>
                @endif
            @else
                <div class="d-flex justify-content-center align-items-center my-4">
                    <img src="{{ getImage('assets/images/empty_message.png') }}" alt="@lang('Image')">
                </div>
            @endif


            @php echo getAds('728x90', 2);@endphp

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
