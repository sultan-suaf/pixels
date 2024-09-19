@php
    $user = auth()->user()->load('collections');
    $collections = $user ? $user->collections->sortBy('title') : [];
    $allCollectionImages = App\Models\CollectionImage::where('image_id', $image->id)->get();
@endphp

<div class="sl-collection">
    <div class="sl-collection__img">
        <img src="{{ imageUrl(getFilePath('stockImage'), $image->thumb) }}" alt="@lang('Image')" class="sl-collection__img-is">
    </div>
    <div class="sl-collection__content">
        <h5 class="modal-title mb-3">@lang('Add to collection')</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

        <form action="{{ route('user.collection.image.add') }}" method="post">
            @csrf
            <input type="hidden" name="image" value="{{ $image->id }}">
            <div class="sl-collection-box" data-simplebar>
                <ul class="list sl-collection-list list-group-flush collections" style="--gap: 0;">
                    @foreach ($collections as $collection)
                        @php
                            $collected = $collection->collectionImage->where('image_id', $image->id)->first();
                        @endphp
                        <li class="list-group-item d-flex justify-content-between flex-wrap @if ($collected) active remove-collection @else select-collection @endif"
                            data-collection="{{ $collection->id }}">
                            <span class="left-side">{{ __($collection->title) }}</span>
                            <span class="right-side">
                                @if ($collected)
                                    <input type="hidden" name="collection[]" value="{{ $collection->id }}">
                                    <span class="hover-effect">@lang('remove')</span>
                                    <span class="show"><i class="las la-check"></i></span>
                                @else
                                    <span class="hover-effect">@lang('select')</span>
                                    <span class="show"></span>
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="sl-collection__footer">
                <div class="input-group input--group mt-3">
                    <input type="text" class="form-control form--control" name="title" placeholder="@lang('Create a new collection')">
                    <span class="input-group-text input-group-primary">
                        <button type="button" class="btn add-collection">@lang('Add collection')</button>
                    </span>
                </div>
                <button type="submit" class="base-btn w-100 mt-3">@lang('Submit')</button>
            </div>

        </form>
    </div>
</div>
