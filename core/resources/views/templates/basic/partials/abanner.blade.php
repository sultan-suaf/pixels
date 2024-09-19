@php
    $content = getContent('banner.content', true);
    $images = App\Models\Image::approved()->inrandomOrder()->limit(6)->get('tags')->pluck('tags')->toArray();
    $tags = array_slice(array_unique(array_merge(...$images)), 0, 6);
@endphp
<section class="hero">
    <div class="container custom--container">
        <div class="row">
            <div class="col-12">
                <div class="hero__container">
                    <form action="{{ route('search') }}" method="get">
                        <input type="hidden" name="type" value="image">
                        <div class="hero__content mx-auto">
                            <h1 class="hero__content-title text-center text--white">
                                {{ __(@$content->data_values->title) }}
                            </h1>
                            <div class="search-bar">
                                <div class="search-bar__icon">
                                    <i class="las la-search"></i>
                                </div>
                                <input type="text" name="filter" class="form-control form--control search-bar__input"
                                    placeholder="@lang('Search anything')...">
                            </div>
                            @if (count($tags))
                                <ul class="list list--row flex-wrap justify-content-center" style="--gap: 5px;">
                                    @foreach ($tags as $tag)
                                        <li>
                                            <a href="{{ route('search', ['type' => 'image', 'tag' => $tag]) }}" class="search-tag">
                                                <span class="search-tag__icon">
                                                    <i class="las la-search"></i>
                                                </span>
                                                <span class="search-tag__text">{{ __(ucfirst($tag)) }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </form>
                    <div class="hero__image">
                        <img src="{{ getImage('assets/images/frontend/banner/' . @$content->data_values->right_image, '750x500') }}"
                            alt="@lang('Banner')" class="hero__image-is">
                    </div>
                    <div class="hero__image-left">
                        <img src="{{ getImage('assets/images/frontend/banner/' . @$content->data_values->left_image, '1100x1140') }}"
                            alt="@lang('Banner')" class="hero__image-is">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
