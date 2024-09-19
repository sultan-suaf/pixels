@php
    $content = getContent('contributor.content', true);
    $members = App\Models\User::whereHas('images')
        ->where('is_featured', Status::ENABLE)
        ->withCount('images')
        ->orderBy('images_count', 'desc')
        ->limit(8)
        ->get();
@endphp

@if ($members->count())
    <div class="section--sm section--bottom">
        <div class="section__head-xl">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-xl-6">
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
            <div class="row g-4 g-md-3">
                @foreach ($members as $member)
                    <div class="col-lg-3 col-md-4 col-sm-6 col-xsm-6">
                        <div class="contributor">
                            <div class="contributor__head"
                                style="background-image: url({{ getImage(getFilePath('userProfile') . '/' . $member->cover_photo, null, 'cover-photo') }});">
                                <img alt="@lang('Member')" class="contributor__img"
                                    src="{{ getImage(getFilePath('userProfile') . '/' . $member->image, null, 'user') }}">
                            </div>
                            <div class="contributor__body">
                                <a href="{{ route('member.images', $member->username) }}">
                                    <h6 class="contributor__name text--base">{{ __($member->fullname) }}</h6>
                                </a>
                                <span class="contributor__contributons">{{ shortNumber($member->images_count) }} @lang('Resources')</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
