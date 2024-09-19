@php
    $content = getContent('footer.content', true);
    $socialIcons = getContent('social_icon.element', false, 4, true);
    $policies = getContent('policy_pages.element', false, 5, true);
    $categories = App\Models\Category::active()
        ->limit(5)
        ->get();
@endphp
@include($activeTemplate . 'partials.cookie')

@if (!request()->routeIs(['search', 'user*']))
    <section>
        <div class="custom--container container">
            @php echo getAds('728x90', 2);@endphp
        </div>
    </section>
@endif

<footer class="footer">
    <div class="section">
        <div class="container">
            <div class="footer-top d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="footer-top__left">
                    <p class="desc text--white mb-0">{{ __(@$content->data_values->heading) }}</p>
                </div>
                @if (gs('multi_language'))
                @php
                    $language = App\Models\Language::all();
                @endphp
                    <div class="footer-top__right">
                        <div class="select-lang lang--dark">
                            <select class="langSel form-select">
                                @foreach ($language as $lang)
                                    <option value="{{ $lang->code }}" @selected(session()->get('lang') == $lang->code)>@lang($lang->name)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="container">
            <div class="row g-4 justify-content-xl-between">
                <div class="col-md-6 col-lg-3">
                    <h4 class="text--white mt-0">{{ __(@$content->data_values->title) }}</h4>
                    <p class="text--white">
                        {{ __(@$content->data_values->description) }}
                    </p>
                    <ul class="list list--row social-list">
                        @foreach ($socialIcons as $social)
                            <li>
                                <a class="t-link social-list__icon" href="{{ $social->data_values->url }}" target="_blank">
                                    @php echo $social->data_values->icon @endphp
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-6 col-lg-3 col-xl-2">
                    <h4 class="text--white mt-0">@lang('Explore')</h4>
                    <ul class="list" style="--gap: 0.5rem">
                        <li>
                            <a class="t-link t-link--base text--white d-inline-block sm-text" href="{{ route('members') }}">
                                @lang('Members')
                            </a>
                        </li>
                        <li>
                            <a class="t-link t-link--base text--white d-inline-block sm-text" href="{{ route('collections') }}">
                                @lang('Collections')
                            </a>
                        </li>
                        <li>
                            <a class="t-link t-link--base text--white d-inline-block sm-text" href="{{ route('images', ['scope' => 'premium']) }}">
                                @lang('Premium')
                            </a>
                        </li>
                        <li>
                            <a class="t-link t-link--base text--white d-inline-block sm-text" href="{{ route('images', ['scope' => 'featured']) }}">
                                @lang('Featured')
                            </a>
                        </li>
                        <li>
                            <a class="t-link t-link--base text--white d-inline-block sm-text" href="{{ route('images', ['scope' => 'popular']) }}">
                                @lang('Popular')
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6 col-lg-3 col-xl-2">
                    <h4 class="text--white mt-0">@lang('Categories')</h4>
                    <ul class="list" style="--gap: 0.5rem">
                        @foreach ($categories as $category)
                            <li>
                                <a class="t-link t-link--base text--white d-inline-block sm-text" href="{{ route('search', ['type' => 'image', 'category' => $category->slug]) }}">
                                    {{ __($category->name) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-6 col-lg-3 col-xl-2">
                    <h4 class="text--white mt-0">@lang('Useful Links')</h4>
                    <ul class="list" style="--gap: 0.5rem">
                        @foreach ($policies as $policy)
                            <li>
                                <a class="t-link t-link--base text--white d-inline-block sm-text" href="{{ route('policy.pages',  $policy->slug) }}">
                                    {{ __($policy->data_values->title) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer__copyright py-3">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p class="sm-text text--white mb-0 text-center">
                        @lang('Copyright') &copy; {{ date('Y') }}. @lang('All Rights Reserved By')
                        <a class="t-link" href="{{ route('home') }}">{{ __(gs('site_name')) }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
