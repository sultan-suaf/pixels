@php
    $content = getContent('banner.content', true);
    $images = App\Models\Image::approved()->inrandomOrder()->limit(6)->get('tags')->pluck('tags')->toArray();
    $tags = array_slice(array_unique(array_merge(...$images)), 0, 6);

@endphp
<section class="hero">
    <div class="custom--container container">
        <div class="row">
            <div class="col-12">
                <div class="hero__container">
                    <form action="{{ route('search') }}" method="get">
                        <input name="type" type="hidden" value="image">
                        <div class="hero__content mx-auto">
                            <h1 class="hero__content-title text--white text-center">
                                {{ __(@$content->data_values->title) }}
                            </h1>
                            <div class="search-bar">
                                <div class="search-bar__icon">
                                    <i class="las la-search"></i>
                                </div>
                                <input class="form-control form--control search-bar__input" name="filter" type="text"
                                    placeholder="@lang('Search anything')...">
                            </div>
                            @if (count($tags))
                                <ul class="list list--row justify-content-center flex-wrap" style="--gap: 5px;">
                                    @foreach ($tags as $tag)
                                        <li>
                                            <a class="search-tag" href="{{ route('search', ['type' => 'image', 'tag' => $tag]) }}">
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
                        <img class="hero__image-is"
                            src="{{ getImage('assets/images/frontend/banner/' . @$content->data_values->right_image, '750x500') }}"
                            alt="@lang('Banner')">
                    </div>
                    <div class="hero__image-left">
                        <img class="hero__image-is"
                            src="{{ getImage('assets/images/frontend/banner/' . @$content->data_values->left_image, '1100x1140') }}"
                            alt="@lang('Banner')">
                    </div>
                </div>
            </div>
        </div>
        @php echo getAds('728x90', 2);@endphp
    </div>
</section>
