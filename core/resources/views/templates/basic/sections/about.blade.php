@php
    $content = getContent('about.content', true);
    $elements = getContent('about.element', false, 4, true);
@endphp

<div class="custom--container container">
    <div class="about-section section bg--light">
        <div class="section__head-xl">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-xl-6">
                        <h2 class="section__title text-center">
                            {{ __(@$content->data_values->title) }}
                        </h2>
                        <p class="sm-text mb-0 text-center">
                            {{ __(@$content->data_values->subtitle) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row justify-content-center justify-content-md-around g-4">
                <div class="col-md-8 col-lg-6 col-xl-5 col-xxl-4">
                    <ul class="list" style="--gap: 1.5rem">
                        @foreach ($elements as $element)
                            <li>
                                <div class="about-card">
                                    <div class="about-card__icon">
                                        @php echo $element->data_values->icon @endphp
                                    </div>
                                    <div class="about-card__content">
                                        <h4 class="about-card__title">{{ __($element->data_values->title) }}</h4>
                                        <p class="about-card__text">
                                            {{ __($element->data_values->description) }}
                                        </p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-8 col-lg-6 col-xl-6">
                    <img class="img-fluid" src="{{ getImage('assets/images/frontend/about/' . @$content->data_values->image, '800x570') }}"
                        alt="@lang('About')">
                </div>
            </div>
        </div>
    </div>
</div>
