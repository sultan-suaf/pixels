@php
    $headerTwoContent = getContent('banner_two.content', true);
    $images = App\Models\Image::approved()->inrandomOrder()->limit(6)->get('tags')->pluck('tags')->toArray();
    $tags = array_slice(array_unique(array_merge(...$images)), 0, 6);
    $fileTypes = App\Models\FileType::active()
        ->withWhereHas('categories', function ($category) {
            $category->where('categories.status', Status::ENABLE);
        })
        ->approvedImageCount()
        ->get();

    $fileTypeImage = @$fileTypes->count() ? @$fileTypes->first()->image : '';
    $fileTypeVideo = @$fileTypes->count() ? @$fileTypes->first()->video : '';
@endphp

<section class="banner-two-section bg-img"
    style="background-image:url({{ getImage(getFilePath('fileType') . '/' . @$fileTypeImage, getFileSize('fileType')) }})">
    <div class="banner-two-video">
        <video src="{{ asset(getFilePath('fileTypeVideo') . '/' . $fileTypeVideo) }}" autoplay muted loop></video>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="banner-content__two">
                    <div class="text-center">
                        <h1 class="banner-content__two-title"> {{ __(@$headerTwoContent->data_values->heading) }} </h1>
                        <p class="banner-content__two-desc"> {{ __(@$headerTwoContent->data_values->subheading) }} </p>
                    </div>
                    <div class="search-box">
                        <form action="{{ route('search') }}" method="get">
                            <input name="file_type" type="hidden" value="0">
                            <input name="type" type="hidden" value="image">
                            <div class="input-group">
                                <div class="category-wrapper">
                                    <button class="btn input-group-text" type="button">
                                        <div class="d-flex">
                                            <span class="icon"><i class="las la-list-ul me-1"></i></span>
                                            <span class="search-service-btn">@lang('Assets')</span>
                                        </div>
                                        <span class="search-box__btn-icon"> <i class="las la-caret-down"></i> </span>
                                    </button>
                                    <div class="banner-search-category-box">
                                        <ul class="banner-search-category__list">
                                            <li class="banner-search-category__item typeSelect active" data-image="image">
                                                <span class="banner-search-category__icon"> <i class="las la-list-ul me-1"></i> </span>
                                                <span class="file_type_name" data-filetype="0">@lang('Assets')</span>
                                            </li>
                                            <li class="banner-search-category__item typeSelect" data-image="collection">
                                                <span class="banner-search-category__icon"> <i class="las la-folder-open me-1"></i> </span>
                                                <span class="file_type_name" data-filetype="0">@lang('Collection')</span>
                                            </li>
                                            @foreach ($fileTypes as $fileType)
                                                <li class="banner-search-category__item selectTypeItem">
                                                    <span class="banner-search-category__icon">
                                                        @php echo $fileType->icon @endphp
                                                    </span>
                                                    <span class="file_type_name"
                                                        data-filetype="{{ $fileType->slug }}">{{ __($fileType->name) }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <div class="banner-search-category-box__item">
                                            <div class="form--radio">
                                                <input class="form-check-input" id="all" name="is_free" type="radio" value="" checked>
                                                <label class="form-check-label" for="all">
                                                    @lang('All')
                                                </label>
                                            </div>
                                            <div class="form--radio">
                                                <input class="form-check-input" id="premium" name="is_free" type="radio" value="1">
                                                <label class="form-check-label" for="premium">
                                                    @lang('Premium')
                                                </label>
                                            </div>
                                            <div class="form--radio">
                                                <input class="form-check-input" id="free" name="is_free" type="radio" value="0">
                                                <label class="form-check-label" for="free">
                                                    @lang('Free')
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input class="form-control search-input" name="filter" type="search" aria-label="Amount (to the nearest dollar)"
                                    placeholder="Search all Assets">
                                <button class="input-group-text search-button" type="submit"> <i class="fa fa-search me-2"></i> <span
                                        class="d-none d-md-block">@lang('Search')</span></button>
                            </div>
                        </form>
                    </div>
                    @if (count($tags))
                        <ul class="list list--row justify-content-center mt-4 flex-wrap" style="--gap: 5px;">
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
            </div>
        </div>
        <div class="banner-two-category">
            @foreach ($fileTypes as $fileType)
                <div class="banner-two-category__item" data-video={{ $fileType->video }}>
                    <a class="banner-two-category__link" href="#">
                        <span class="banner-two-category__title"> {{ __($fileType->name) }} </span>
                        <img src="{{ getImage(getFilePath('fileType') . '/' . $fileType->image, getFileSize('fileType')) }}" alt="file type">
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

@push('script')
    <script>
        "use strict";

        var clickOnBtn = false;



        $('.category-wrapper .btn').on('click', function(event) {
            event.stopPropagation();
            $('.category-wrapper .banner-search-category-box').toggleClass('category_show');
            clickOnBtn = !clickOnBtn;

            let hasIcon = $('.search-box__btn-icon i').hasClass('las la-caret-down');
            if (hasIcon) {
                $('.search-box__btn-icon i').attr('class', 'las la-caret-up');
            } else {
                $('.search-box__btn-icon i').attr('class', 'las la-caret-down');
            }
        });


        $(document).on("click", function(event) {
            let parentElement = $(event.target).closest('.banner-search-category-box').hasClass('category_show');
            let targetElement = $('.banner-search-category-box').hasClass('category_show');
            if (parentElement != targetElement) {
                $('.category-wrapper .banner-search-category-box').removeClass('category_show');
                $('.search-box__btn-icon i').attr('class', 'las la-caret-down');
            }
        });

        $('.selectTypeItem').on('click', function() {

            var textContent = $(this).text().trim();

            $('.banner-search-category__list').find('.selectTypeItem').removeClass('active');
            $(this).toggleClass('active');

            $('input[type=search]').attr('placeholder', '@lang('Search all') ' + textContent);
            let fileType = $(this).find('.file_type_name').data('filetype');
            $('input[name=file_type]').val(fileType);
        });

        $('.typeSelect').on('click', function() {

            var iconHtml = $(this).find('.banner-search-category__icon').html().trim();
            var textContent = $(this).text().trim();
            $('.banner-search-category__list').find('.typeSelect').removeClass('active');
            $(this).toggleClass('active');

            $('.category-wrapper').find('.icon').html(iconHtml);
            $('.category-wrapper').find('.search-service-btn').html(textContent);
            var image = $(this).data('image');
            $('input[name=type]').val(image);
        })

        $('.banner-two-category__item').on('mouseenter', function() {
            var hoverImg = $(this).find('img').attr('src');
            $('.banner-two-section').css('background-image', 'url(' + hoverImg + ')');
            $('.banner-two-video video').attr('src', '')

            let video = $(this).data('video');

            if (video) {
                let videoLink = (`{{ asset(getFilePath('fileTypeVideo') . '/' . ':video') }}`).replace(":video", video);
                $('.banner-two-video video').attr('src', videoLink)
            }
        });


        $(window).on("scroll", function() {
            if ($(window).scrollTop() >= 300) {
                $("header").addClass("fixed-header");
            } else {
                $("header").removeClass("fixed-header");
            }
        });
    </script>
@endpush

@push('style')
    <style>
        .header-two.fixed-header {
            position: fixed;
            background-color: hsl(var(--black));
            transition: 0.3s linear;
            top: -1px;
            animation: slide-down 0.8s;
            width: 100%;
            border-bottom: 1px solid hsl(var(--white)/.2);
        }

        .bg-img {
            background-size: cover !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            width: 100%;
            height: 100%;
        }

        .banner-two-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .banner-two-video video {
            width: inherit;
            height: inherit;
            object-fit: cover;
        }

        .banner-two-section {
            position: relative;
            padding: 180px 0 140px;
            z-index: 1;
            transition: .4s;
        }

        @media (max-width:1199px) {
            .banner-two-section {
                padding: 130px 0 90px;
            }
        }

        .banner-content__two-title {
            color: hsl(var(--white));
            font-size: 50px;
        }

        .banner-two-section::after {
            position: absolute;
            content: '';
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-color: hsl(var(--black)/.5);
        }

        .header-two {
            position: absolute;
            background-color: transparent;
            box-shadow: none;
        }

        .header-two .signup-btn {
            border: 1px solid hsl(var(--white));
            border-radius: 4px;
            color: hsl(var(--white));
        }

        .header-two .primary-menu__link {
            margin-left: 0;
            margin-right: 0;
        }

        .header-two .primary-menu>li.has-sub .primary-menu__link::after {
            right: 0;
        }

        @media (max-width:991px) {
            .header-two .mega-menu__item>a {
                color: hsl(var(--white));
                font-size: 14px;
            }

            .header-two .mega-menu__item>a:hover {
                color: hsl(var(--white));
            }

            .header-two .mega-menu-wrapper__title::after {
                position: absolute;
                content: '';
                bottom: 0;
                left: 0;
                width: 70px;
                height: 1px;
                background-color: hsl(var(--white));
            }

            .header-two .mega-menu-wrapper__title {
                color: hsl(var(--white));
            }
        }

        .banner-content__two-desc {
            color: hsl(var(--white)/.8);
            font-size: 18px;
        }

        .search-box__category {
            width: max-content;
        }

        .search-box .btn {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            height: 50px;
            width: 160px;
            border: 0 !important;
            background-color: hsl(var(--white));
            border-radius: 4px 0 0 4px !important;
        }

        @media (max-width:991px) {
            .header-two .primary-menu__sub-link {
                color: hsl(var(--white));
            }
        }

        .header-two {
            background-color: hsl(var(--black));
        }

        .header-two .nav-container {
            position: relative;
            background: hsl(var(--black));
            border-radius: 0px;
        }

        .header-two .primary-menu>li {
            border-bottom: 1px solid hsl(var(--white)/0.2);
        }

        .header-two .signup-btn {
            margin-right: 0;
            margin-left: 0;
            padding: 5px 20px;
            margin-top: 6px;
        }

        .header-two .primary-menu>li:last-child,
        .language_switcher {
            border: 0 !important;
        }

        .header-two .primary-menu__link {
            color: hsl(var(--white));
        }

        .search-box__btn-icon i {
            font-size: 14px;
        }

        .search-box .btn.input-group-text span {
            font-weight: 400;
        }

        .search-box .form-control {
            outline: none;
            border: none;
            border-left: 1px solid #cccccc !important;
        }

        .search-box .form-control:focus {
            border-color: transparent;
            box-shadow: none !important;
        }

        .category-wrapper {
            position: relative;
            z-index: 2;
            border-right: 1px solid hsl(var(--black)/.01);
        }

        .search-box .search-input::placeholder {
            color: #cccccc;
            font-size: 15px;
        }

        .category-wrapper .banner-search-category-box {
            position: absolute;
            width: 100%;
            margin-top: 4px;
            background-color: hsl(var(--white));
            border-radius: 4px;
            box-shadow: rgba(0, 0, 0, 0.04) 0px 3px 5px;
            transform: scaleY(0);
            transform-origin: top center;
            transition: .3s ease-in-out;
            top: 100%;
            visibility: hidden;
            opacity: 0;
        }

        .category-wrapper .banner-search-category-box.category_show {
            visibility: visible;
            opacity: 1;
            transform: scaleY(1);
        }

        .category-wrapper .banner-search-category__list {
            border-radius: 0px;
            border: 0;
            padding: 0 !important;
            list-style: none;
            margin-bottom: 0;
        }

        .banner-search-category__item {
            padding: 4px 8px;
            transition: .2s;
            cursor: pointer;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .banner-search-category__item:hover {
            background-color: hsl(var(--base)/.03);
        }

        .banner-search-category__item.active {
            color: hsl(var(--base));
            position: relative;
            background-color: hsl(var(--base)/.03);
        }

        .banner-search-category__item.active::after {
            position: absolute;
            content: "\f00c";
            font-family: "Line Awesome Free";
            font-weight: 700;
            color: hsl(var(--base));
            font-size: 16px;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .banner-search-category-box__item {
            border-top: 1px solid hsl(var(--black)/.1);
            padding: 4px 8px;
        }

        .banner-search-category-box__item .form--radio {
            padding-bottom: 5px;
        }

        .banner-search-category-box__item .form--radio:last-child {
            padding-bottom: 0;
        }

        .form-check.form--check .form-check-input {
            background-image: none !important;
            width: 16px;
            height: 16px;
            border-radius: 2px;
            background-color: transparent;
            border: 1px solid hsl(var(--black)/.2);
        }

        @media (max-width:767px) {
            .banner-content__two-title {
                font-size: 35px;
            }
        }

        /* Custom Radio Design */
        .form--radio {
            display: flex;
            align-items: center;
        }

        .form--radio .form-check-input {
            box-shadow: none;
            border: 1px solid hsl(var(--black)/0.2);
            position: relative;
            background-color: transparent;
            cursor: pointer;
            width: 16px;
            height: 16px;
            border-radius: 2px;
            margin-top: 0;
        }

        .form--radio .form-check-input:active {
            filter: brightness(100%);
        }

        .form--radio .form-check-input:checked {
            background-color: hsl(var(--base));
            border-color: hsl(var(--base));
        }

        .form--radio .form-check-input:checked[type=radio] {
            background-image: none;
        }

        .form--radio .form-check-input:checked::before {
            position: absolute;
            content: "\f00c";
            font-family: 'Line Awesome Free';
            font-weight: 700;
            color: hsl(var(--white));
            z-index: 999;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 10px;
        }

        .form--radio .form-check-label {
            font-weight: 400;
            width: calc(100% - 16px);
            padding-left: 12px;
            cursor: pointer;
            font-size: 14px;
        }

        .search-box .search-button {
            background: hsl(var(--base));
            color: hsl(var(--white));
            padding: 10px 20px;
            border: 1px solid transparent;
            border-radius: 0 4px 4px 0px !important;
        }

        .banner-two-category {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 70px;
            flex-wrap: wrap;
        }

        .banner-two-category__link img {
            width: 100%;
            object-fit: cover;
            max-height: 120px;
            height: 100%;
            transition: .3s;
        }

        .banner-two-category__item {
            position: relative;
            z-index: 1;
            border-radius: 4px;
            overflow: hidden;
            width: 200px;
            height: 120px;
        }

        .banner-two-category__item:hover .banner-two-category__link img {
            transform: scale(1.2);
        }

        .banner-two-category__link {
            display: inline-block;
            width: 100%;
            height: 120px;
            border-radius: 5px;
            position: relative;
            isolation: isolate;
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            overflow: hidden;
            border: 1px solid hsl(var(--white)/.5);
        }

        .banner-two-category__title {
            margin-bottom: 0;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: hsl(var(--white));
            cursor: pointer;
            z-index: 1;
            font-size: 24px;
        }

        .category-wrapper .search-service-btn {
            font-size: 14px;
            padding-left: 3px;
        }

        .search-box .btn.input-group-text span i {
            margin-right: 5px;
        }

        @media (max-width:991px) {
            .banner-two-category__link {
                height: 90px;
            }

            .banner-two-category__link img {
                height: 90px;
            }

            .banner-two-category__item {
                width: 150px;
                height: 90px;
            }

            .banner-two-category__title {
                font-size: 18px;
            }

            .banner-two-category {
                margin-top: 40px;
            }
        }

        @media (max-width:424px) {
            .banner-two-category__link {
                height: 70px;
            }

            .banner-two-category__link img {
                height: 70px;
            }

            .banner-two-category__item {
                width: 120px;
                height: 70px;
            }

            .banner-two-category__title {
                font-size: 16px;
            }
        }

        .banner-two-category__link::after {
            content: "";
            position: absolute;
            inset: 0;
            background: hsl(var(--black)/0.65);
        }

        .header-two .menu-toggle::after {
            background-color: hsl(var(--white));
        }

        .header-two .menu-toggle::before {
            background-color: hsl(var(--white));
        }

        .header-two .menu-toggle {
            color: hsl(var(--white));
        }

        @media(max-width:767px) {
            .search-box .btn {
                width: 65px;
            }

            .category-wrapper .banner-search-category-box {
                min-width: 180px;
            }

            .search-service-btn {
                display: none;
            }
        }

        .header-two .nav-container .language_switcher__caption .text {

            color: #fff !important;
        }
    </style>
@endpush
