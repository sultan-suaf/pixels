@php
    $content = getContent('service.content', true);
    $elements = getContent('service.element', false, 4, true);
@endphp
<div class="container custom--container">
    <div class="service-section section--sm section--bottom">
        <div class="section__head">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-xl-5 col-xxl-6">
                        <h2 class="section__title text-center">
                            {{ __(@$content->data_values->title) }}
                        </h2>
                        <p class="mb-0 text-center sm-text section__para mx-auto">
                            {{ __(@$content->data_values->subtitle) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row g-4 justify-content-center">
                @foreach ($elements as $element)
                    <div class="col-sm-6 col-lg-3">
                        <div class="service-card text-center text-lg-start">
                            <div class="service-card__icon service-card__icon-{{ $loop->index + 1 }} mx-auto ms-lg-0">
                                @php echo $element->data_values->icon @endphp
                            </div>
                            <div class="service-card__content">
                                <h5 class="service-card__title">
                                    {{ __($element->data_values->title) }}
                                </h5>
                                <p class="service-card__text">
                                    {{ __($element->data_values->description) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
