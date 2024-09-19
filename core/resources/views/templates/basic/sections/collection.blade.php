@php
    $content = getContent('collection.content', true);
    $collections = App\Models\Collection::public()
        ->where('is_featured', Status::ENABLE)
        ->whereHas('images')
        ->limit('8')
        ->orderBy('title')
        ->with('images', 'user')
        ->get();
@endphp
@if ($collections->count())
    <div class="section">
        <div class="section__head-xl">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-xl-6">
                        <h2 class="section__title text-center mb-0">{{ __($content->data_values->title) }}</h2>
                        <p class="mb-0 sm-text text-center">
                            {{ __($content->data_values->subtitle) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row g-4 justify-content-center">
                @include($activeTemplate . 'partials.collection_grid', ['collections' => $collections])
            </div>
        </div>
    </div>
@endif
