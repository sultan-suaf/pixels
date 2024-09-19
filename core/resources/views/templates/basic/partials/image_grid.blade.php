@php
    $defaultImageContent = getContent('default_images.content', true);
    $defaultImage = getImage('assets/images/frontend/default_images/' . @$defaultImageContent->data_values->loading_image);
@endphp

<ul class="list list--row flex-images {{ @$class ?? 'gallery' }} flex-wrap" id="flexBox">

    @foreach ($images as $image)
        @php
            $imageUrl = imageUrl(getFilePath('stockImage'), $image->thumb);
        @endphp
        <li class="item gallery__item" data-w="{{ $image->image_width }}" data-h="{{ $image->image_height }}" data-video={{ $image->video }}>
            <a class="gallery__link" href="{{ route('image.detail', [slug($image->title), $image->id]) }}"></a>
            <figure>
                <a class="gallery__content" href="{{ route('image.detail', [slug($image->title), $image->id]) }}">
                    <span class="gallery__title">
                        {{ __($image->title) }}
                    </span>
                    <span class="gallery__footer">
                        <span class="gallery__author">
                            <span class="gallery__user">
                                <img class="gallery__user-img lazy-loading-img"
                                    data-image_src="{{ getImage(getFilePath('userProfile') . '/' . $image->user->image, null, 'user') }}"
                                    src="{{ $defaultImage }}" alt="@lang('Contributor')">
                            </span>
                            <span class="gallery__user-name">{{ $image->user->fullname }}</span>
                        </span>
                        <span class="gallery__like">
                            <span class="gallery__like-icon">
                                <i class="fas fa-heart"></i>
                            </span>
                            <span class="gallery__like-num">{{ shortNumber($image->total_like) }}</span>
                        </span>
                    </span>
                </a>
            </figure>
            <div class="image--details-icon">
                @if ($image->premium)
                    <span class="gallery__premium">
                        <i class="fas fa-crown"></i>
                    </span>
                @endif
                @if ($image->video)
                    <span class="gallery__video_icon">
                        <i class="las la-video"></i>
                    </span>
                @endif
            </div>
            <div class="item-video item-link">
                <span class="d-none h-100 w-100">
                    <video class="w-100 h-100" src="" autoplay muted loop></video>
                </span>
                <img class="gallery__img lazy-loading-img" data-image_src="{{ $imageUrl }}" src="{{ $defaultImage }}" alt="@lang('Image')">
            </div>
            @php
                $user = auth()->user();
                $like = $image->likes->where('user_id', @$user->id)->count();
                $collectionImage = $user ? $user->collectionImages->where('image_id', $image->id)->first() : null;
            @endphp

            <div class="gallery__share">
                <div class="list gallery__list">
                    <div>
                        <button class="gallery__btn @if ($like) unlike-btn @else like-btn @endif" data-has_icon="1"
                            data-bs-toggle="tooltip" data-bs-placement="left" data-bs-custom-class="custom--tooltip" data-image="{{ $image->id }}"
                            title="@if ($like) @lang('Unlike') @else @lang('like') @endif">
                            @if ($like)
                                <i class="las la-heart active"></i>
                            @else
                                <i class="lar la-heart"></i>
                            @endif
                        </button>
                    </div>
                    <div>
                        <button class="gallery__btn collect-btn" data-bs-toggle="tooltip" data-bs-placement="left"
                            data-bs-custom-class="custom--tooltip" data-image_id="{{ $image->id }}"
                            title="@if ($collectionImage) @lang('Collected') @else
                @lang('Collect') @endif">
                            <i class="las la-folder-plus"></i>
                        </button>
                    </div>
                    <div>
                        <button class="gallery__btn share-btn" data-bs-toggle="tooltip" data-bs-placement="left"
                            data-bs-custom-class="custom--tooltip" data-route="{{ route('image.detail', [slug($image->title), $image->id]) }}"
                            data-url_len_code="{{ urlencode(route('image.detail', [slug($image->title), $image->id])) }}"
                            data-image_title="{{ $image->title }}" title="Share">
                            <i class="las la-share"></i>
                        </button>
                    </div>

                </div>

            </div>
        </li>
    @endforeach
</ul>
@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/jquery.flex-images.min.js') }}"></script>
@endpush

@push('script')
    <script>
        "use strict";

        $('#flexBox').flexImages({
            rowHeight: 240
        });

        $('.gallery__item').on('mouseenter', function() {
            let video = $(this).data('video');
            if (video) {
                let videoUrl = (`{{ asset(getFilePath('stockVideo') . '/' . ':video') }}`).replace(":video", video);
                $(this).find('.item-video span').removeClass('d-none');
                $(this).find('.item-video span video').attr('src', videoUrl);
            }
        })

        $('.gallery__item').on('mouseleave', function() {
            $(this).find('.item-video span').addClass('d-none');
            $(this).find('.item-video span video').attr('src', '');
        })
    </script>
@endpush
@push('style')
    <style>
        .item {
            position: relative;
            border-radius: 4px;
            overflow: hidden;
        }

        .item-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .item-video video {
            width: inherit;
            height: inherit;
            object-fit: cover;
        }

        .gallery__video_icon {
            z-index: 1;
            width: 30px;
            height: 30px;
            display: grid;
            place-items: center;
            border-radius: 3px;
            font-size: 16px;
            background: hsl(var(--black)/0.5);
            backdrop-filter: blur(2px);
            color: hsl(var(--white));
        }

        .gallery__video_icon i {
            font-size: 18px;
        }

        .image--details-icon {
            position: absolute;
            left: 15px;
            top: 15px;
        }
    </style>
@endpush
