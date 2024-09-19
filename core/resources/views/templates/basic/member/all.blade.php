@php
    $content = getContent('member.content', true);
@endphp
@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="section">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <div class="col-12">
                    <h2 class="mt-0 mb-2">
                        {{ __($heading) }} ({{ $members->total() }})
                    </h2>
                    <p class="mb-0">
                        {{ __($content->data_values->title) }}
                    </p>
                </div>
                @forelse ($members as $member)
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="contributor">
                            <div class="contributor__head"
                                style="background-image: url({{ getImage(getFilePath('userProfile') . '/' . $member->cover_photo, null, 'cover-photo') }});">
                                <img src="{{ getImage(getFilePath('userProfile') . '/' . $member->image, null, 'user') }}" alt="@lang('Member')"
                                    class="contributor__img">
                            </div>
                            <div class="contributor__body">
                                <a href="{{ route('member.images', $member->username) }}">
                                    <h6 class="contributor__name text--base">{{ __($member->fullname) }}</h6>
                                </a>
                                <span class="contributor__contributons">{{ shortNumber($member->images_count) }} @lang('Resources')</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="d-flex justify-content-center align-items-center">
                        <img src="{{ getImage('assets/images/empty_message.png') }}" alt="@lang('Image')">
                    </div>
                @endforelse
            </div>
            @if ($members->hasPages())
                <div class="mt-5">
                    {{ paginateLinks($members) }}
                </div>
            @endif
        </div>
    </div>
@endsection
