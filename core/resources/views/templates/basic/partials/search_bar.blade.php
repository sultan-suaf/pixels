@php
    $colors = App\Models\Color::orderBy('id', 'DESC')->get();
    $extensions = getFileExt();
    $images = App\Models\Image::approved()->inrandomOrder()->limit(6)->pluck('tags')->toArray();
    $tags = array_slice(array_unique(array_merge(...$images)), 0, 6);

@endphp

<div class="filter-sidebar">
    <div class="filter-sidebar__header">
        <div class="d-flex align-items-center">
            <button class="btn filter-close">
                <i class="las la-times"></i>
            </button>
            <span class="filter-sidebar__text">@lang('Filters')</span>
        </div>
        <button class="btn filter-clear">
            <span class="clear-all">@lang('Clear all')</span>
            <i class="las la-times"></i>
        </button>
    </div>
    <div class="filter-sidebar__body" data-simplebar>
        <ul class="list" style="--gap: 0.5rem">
            <li>
                <div class="filter-sidebar__title">
                    <span class="filter-sidebar__title-icon">
                        <i class="las la-sort"></i>
                    </span>
                    <span class="filter-sidebar__title-text">@lang('Sort by')</span>
                </div>
                <ul class="list list--row flex-wrap align-items-center">
                    <li>
                        <span class="filter-btn__is search-param" data-param="popular" data-param_value="1"> @lang('Popular') </span>
                    </li>
                    <li>
                        <span class="filter-btn__is sortBy @if (!request()->has('sort_by')) active @endif"> @lang('Recent') </span>
                    </li>
                </ul>
            </li>
            <li>
                <div class="filter-sidebar__title">
                    <span class="filter-sidebar__title-icon">
                        <i class="las la-crown"></i>
                    </span>
                    <span class="filter-sidebar__title-text">@lang('License')</span>
                </div>
                <ul class="list list--row flex-wrap align-items-center">
                    <li>
                        <span class="filter-btn__is search-param" data-param="is_free" data-param_value="1" data-search_type="single">
                            @lang('Free') </span>
                    </li>
                    <li>
                        <span class="filter-btn__is search-param" data-param="is_free" data-param_value="0" data-search_type="single">
                            @lang('Premium') </span>
                    </li>
                </ul>
            </li>
            @if ($colors->count())
                <li>
                    <div class="filter-sidebar__title">
                        <span class="filter-sidebar__title-icon">
                            <i class="las la-tint"></i>
                        </span>
                        <span class="filter-sidebar__title-text">@lang('Colors')</span>
                    </div>
                    <ul class="list list--row flex-wrap align-items-center">
                        <li>
                            <span class="color-clear clear-param" id="color-1" data-param="color"></span>
                        </li>
                        @foreach ($colors as $color)
                            <li>
                                <span class="color-selector search-param" id="color-{{ $color->id }}" data-param="color"
                                    data-param_value="{{ $color->color_code }}" data-search_type="single"
                                    style="background: #{{ $color->color_code }};border: 1px solid @if ($color->color_code != 'ffffff' && $color->color_code != 'fff') #{{ $color->color_code }}; @else #bac8d3 @endif"></span>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endif
            <li>
                <div class="filter-sidebar__title">
                    <span class="filter-sidebar__title-icon">
                        <i class="las la-calendar-week"></i>
                    </span>
                    <span class="filter-sidebar__title-text">@lang('Publish date')</span>
                </div>
                <ul class="list list--row flex-wrap align-items-center pb-3">
                    <li>
                        <span class="filter-btn__is search-param" data-param="period" data-search_type="single" data-param_value="3">
                            @lang('Last 3 months') </span>
                    </li>
                    <li>
                        <span class="filter-btn__is search-param" data-param="period" data-search_type="single" data-param_value="6">
                            @lang('Last 6 months') </span>
                    </li>
                    <li>
                        <span class="filter-btn__is search-param" data-param="period" data-search_type="single" data-param_value="12">
                            @lang('Last year') </span>
                    </li>
                </ul>
            </li>

            <li>
                <div class="filter-sidebar__title">
                    <span class="filter-sidebar__title-icon">
                        <i class="las la-tag"></i>
                    </span>
                    <span class="filter-sidebar__title-text">@lang('Tag')</span>
                </div>
                <ul class="list list--row flex-wrap align-items-center pb-3">
                    <li>
                        <span class="filter-btn__is search-param" data-param="tag" data-search_type="single"
                            data-param_value="all">@lang('All')</span>
                    </li>
                    @if (request()->tag && !in_array(request()->tag, $tags))
                        <li>
                            <span class="filter-btn__is search-param" data-param="tag" data-search_type="single"
                                data-param_value="{{ request()->tag }}">{{ __(request()->tag) }}</span>
                        </li>
                    @endif
                    @foreach ($tags as $tag)
                        <li>
                            <span class="filter-btn__is search-param" data-param="tag" data-search_type="single"
                                data-param_value="{{ $tag }}"> {{ __($tag) }} </span>
                        </li>
                    @endforeach

                </ul>
            </li>


            <li>
                <div class="filter-sidebar__title">
                    <span class="filter-sidebar__title-icon">
                        <i class="las la-image"></i>
                    </span>
                    <span class="filter-sidebar__title-text">@lang('Extensions')</span>
                </div>
                <ul class="list list--row flex-wrap align-items-center pb-3">
                    <li>
                        <span class="filter-btn__is search-param" data-param="extension" data-search_type="single"
                            data-param_value="all">@lang('All')</span>
                    </li>
                    @foreach ($extensions as $text)
                        <li>
                            <span class="filter-btn__is search-param" data-param="extension" data-search_type="single"
                                data-param_value="{{ $text }}"> {{ __(strtoupper($text)) }} </span>
                        </li>
                    @endforeach

                </ul>
            </li>
        </ul>
        @php echo getAds('300x250');@endphp
    </div>


</div>

@push('script')
    <script>
        (function($) {
            "use strict";

            //clear all parameter wihout specefic field
            $('.clear-all').on('click', function() {
                let url = new URL($(location).attr("href"));
                let params = new URLSearchParams(url.search);
                let searchParams = [];
                for (const key of params.keys()) {
                    if (key != 'type' && key != 'category') {
                        searchParams.push(key);
                    }
                }

                searchParams.forEach(element => {
                    params.delete(element);
                });

                const newUrl = new URL(`${url.origin}${url.pathname}?${params}`);
                window.location.href = newUrl;
            });

            // page on load active searched field
            $.each($('.search-param'), function(index, element) {
                let url = new URL($(location).attr("href"));
                let params = new URLSearchParams(url.search);

                params.forEach((value, key) => {
                    if ($(element).data('param') == key && $(element).data('param_value') == value) {
                        $(element).addClass('active');
                    }
                });
            });


            // on click search field
            $(document).on('click', '.search-param', function() {
                let searchItem = $(this);
                let link = new URL($(location).attr('href'));
                let param = $(this).data('param');
                let paramValue = $(this).data('param_value');
                let searchType = $(this).data('search_type') ?? null;
                link = removeParam(link, 'page');
                if (searchType == 'single') {
                    let sameTypeSearchField = $(`[data-param='${param}']`).not(this);

                    $.each(sameTypeSearchField, function(index, element) {
                        let params = new URLSearchParams(link.search);
                        let param = $(element).data('param');
                        let paramValue = $(element).data('param_value');

                        params.forEach((value, key) => {
                            if (param == key && paramValue == value) {
                                link = removeParam(link, param, paramValue, searchType);
                            }
                        });
                        $(element).removeClass('active');
                    });
                }

                if (searchItem.hasClass('active')) {
                    searchItem.removeClass('active');
                    link = removeParam(link, param, paramValue, searchType);
                } else {
                    searchItem.addClass('active');
                    link = appendParam(link, param, paramValue);
                }
                window.location.href = link;
            })

            // append parameter to the current route
            function appendParam(currentUrl, param = null, paramValue = null) {
                let url = new URL(currentUrl);
                const addParam = {
                    [param]: paramValue
                }
                const newParams = new URLSearchParams([
                    ...Array.from(url.searchParams.entries()),
                    ...Object.entries(addParam)
                ]);
                const newUrl = new URL(`${url.origin}${url.pathname}?${newParams}`);
                return newUrl;
            }

            //remove parameter from the current route
            function removeParam(currentUrl, param = null, paramValue = null, searchType = 'single') {
                let url = new URL(currentUrl);
                let params = new URLSearchParams(url.search);
                if (searchType == 'multiple') {
                    const multipleParams = params.getAll(param).filter(param => param != paramValue);
                    params.delete(param);
                    for (const value of multipleParams) {
                        params.append(param, value);
                    }

                } else {
                    params.delete(param);
                }
                const newUrl = new URL(`${url.origin}${url.pathname}?${params}`);
                return newUrl;
            }

            //clear individual parameter
            $('.clear-param').on('click', function() {
                let url = new URL($(location).attr("href"));
                let param = $(this).data('param');
                url = removeParam(url, param);
                $(`span[data-param='${param}']`).removeClass('active');
                window.location.href = url;
            });

            //search from search input
            let searchInputField = $(document).find('.search-input');
            $(searchInputField).keypress(function(event) {
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if (keycode == '13') {
                    let url = new URL($(location).attr('href'));
                    let queryParam = $(this).val();
                    url = removeParam(url, 'filter');
                    url = appendParam(url, 'filter', queryParam);
                    window.location.href = url;
                }
            });

            $('.sortBy').on('click', function() {
                let link = new URL($(location).attr('href'));
                link = removeParam(link, 'page');

                if ($(this).hasClass('active')) {
                    link = appendParam(link, 'sort_by', 'asc');
                } else {
                    link = removeParam(link, 'sort_by');
                }
                window.location.href = link;
            });

            $(document).on('click', '.search-images', function() {
                if (!$(this).hasClass('active')) {
                    let link = new URL($(location).attr('href'));
                    link = removeParam(link, 'page');
                    link = removeParam(link, 'type');
                    link = appendParam(link, 'type', 'image');
                    window.location.href = link;
                }
            })
            $(document).on('click', '.search-collections', function() {
                if (!$(this).hasClass('active')) {
                    let link = new URL($(location).attr('href'));
                    link = removeParam(link, 'page');
                    link = removeParam(link, 'type');
                    link = appendParam(link, 'type', 'collection');
                    window.location.href = link;
                }
            })
        })(jQuery);
    </script>
@endpush
