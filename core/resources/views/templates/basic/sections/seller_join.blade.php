@php
    $content = getContent('seller_join.content', true);
@endphp

<section class="join section">
    <div class="container">
        <div class="join-content-wrapper">
            <div class="join-thumb">
                <img src="{{ getImage('assets/images/frontend/seller_join/' . @$content->data_values->image, '440x350') }}" alt="">
            </div>
            <div class="join-content">
                <h2 class="join-content__title" s-break="-3">{{ __(@$content->data_values->title) }}</h2>
                <p class="join-content__desc">{{ __(@$content->data_values->description) }}</p>
                <a href="{{ @$content->data_values->button_url }}" class="base-btn">{{ __(@$content->data_values->button_text) }}</a>
            </div>
        </div>
    </div>
</section>
