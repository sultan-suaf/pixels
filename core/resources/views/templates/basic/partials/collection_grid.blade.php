@foreach ($collections as $collection)
    @php
        $images = $collection->images->pluck('tags')->toArray();
        $tags = array_slice(array_unique(array_merge(...$images)), 0, 4);
    @endphp
    <div class="col-sm-6 col-lg-3">
        <div class="collection">
            <div class="collection__group">
                <ul class="collection__list collection__list-tripple">
                    @foreach ($collection->images->sortByDesc('id')->take(3) as $image)
                        <li class="bg--light">
                            <a href="{{ route('collection.detail', [slug($collection->title), $collection->id]) }}" class="collection__link">
                                <img src="{{ imageUrl(getFilePath('stockImage'), $image->thumb) }}" alt="@lang('image')" class="collection__img">
                            </a>
                        </li>
                    @endforeach
                    @if (count($collection->images) < 3)
                        @for ($i = 0; $i < 3 - count($collection->images); $i++)
                            <li class="bg--light">
                                <a href="{{ route('collection.detail', [slug($collection->title), $collection->id]) }}" class="collection__link"></a>
                            </li>
                        @endfor
                    @endif
                </ul>
            </div>
            <a href="{{ route('collection.detail', [slug($collection->title), $collection->id]) }}">
                <h4 class="collection__title">{{ __($collection->title) }}</h4>
            </a>
            <p class="collection__subtitle">{{ shortNumber($collection->images->count()) }} @lang('Photos') - @lang('Created by') <a
                    href="{{ route('member.images', $collection->user->username) }}">{{ __($collection->user->fullname) }}</a></p>
            @if (count($tags))
                <ul class="list list--row flex-wrap" style="--gap: 5px">
                    @foreach ($tags as $tag)
                        <li>
                            <a class="btn btn--tag" href="{{ route('search', ['type' => 'image', 'tag' => $tag]) }}">{{ __(ucfirst($tag)) }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endforeach
