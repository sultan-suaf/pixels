@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row g-3">
        @forelse ($images as $image)
            <div class="col-md-6 col-xl-4">
                <div class="card custom--card image-information-card">
                    <div class="card-body">
                        <div class="image-information">
                            <div class="action-btns">
                                <div class="btn-group">
                                    <a class="btn btn-sm btn--primary" data-bs-toggle="tooltip" href="{{ route('user.image.edit', $image->id) }}"
                                        title="Edit">
                                        <i class="las la-pen"></i>
                                    </a>
                                </div>
                            </div>
                            <a class="t-link image-information__img" href="{{ route('image.detail', [slug($image->title), $image->id]) }}">
                                <img class="image-information__img-is" src="{{ imageUrl(getFilePath('stockImage'), @$image->thumb) }}" alt="image">
                                @if (!$image->is_free)
                                    <div class="image--details-icon">
                                        <span class="gallery__premium">
                                            <i class="fas fa-crown"></i>
                                        </span>
                                    </div>
                                @endif
                            </a>

                            <div class="image-information__content">
                                <h5 class="image-information__title"><a class="text--base"
                                        href="{{ route('image.detail', [slug($image->title), $image->id]) }}">{{ __($image->title) }}</a></h5>
                                <ul class="list" style="--gap: 0;">
                                    <li>
                                        <div class="image-information__item">
                                            <div class="image-information__item-left">
                                                @lang('Category :')
                                            </div>
                                            <div class="image-information__item-right">
                                                {{ __($image->category->name) }}
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="image-information__item">
                                            <div class="image-information__item-left">
                                                @lang('Total Likes :')
                                            </div>
                                            <div class="image-information__item-right">
                                                {{ shortNumber($image->total_like) }}
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="image-information__item">
                                            <div class="image-information__item-left">
                                                @lang('Total Views :')
                                            </div>
                                            <div class="image-information__item-right">
                                                {{ shortNumber($image->total_view) }}
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="image-information__item">
                                            <div class="image-information__item-left">
                                                @lang('Total Downloads :')
                                            </div>
                                            <div class="image-information__item-right">
                                                {{ shortNumber($image->total_downloads) }}
                                            </div>
                                        </div>
                                    </li>


                                    @if (request()->routeIs('user.image.all'))
                                        <li>
                                            <div class="image-information__item">
                                                <div class="image-information__item-left">
                                                    @lang(' Status :')
                                                </div>
                                                <div class="image-information__item-right">
                                                    @php echo $image->statusBadge; @endphp
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center">
                <img src="{{ getImage('assets/images/empty_message.png') }}" alt="@lang('Image')">
            </div>
        @endforelse

        @if ($images->hasPages())
            <div class="mt-4">
                {{ paginateLinks($images) }}
            </div>
        @endif
    </div>

    <x-confirmation-modal />
@endsection
