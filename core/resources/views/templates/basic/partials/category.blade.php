@php
    $categories = App\Models\Category::active()->orderBy('name')->get();
@endphp
<div class="category section--sm pb-0">
    <div class="container custom--container">
        <div class="row">
            <div class="col-12">
                <div class="category__slider">
                    @foreach ($categories as $category)
                        <div class="category__slider-item">
                            <a href="{{ route('search', ['type' => 'image', 'category' => $category->slug]) }}" class="category__link">
                                <span class="category__text">{{ __($category->name) }}</span>
                                <img src="{{ getImage(getFilePath('category') . '/' . $category->image, getFileSize('category')) }}"
                                    alt="{{ __($category->name) }}" class="category__img">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
