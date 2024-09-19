@if (!$paginator->onFirstPage())
    <a href="{{ $paginator->previousPageUrl() }}" class="base-btn-outline">
        @lang('PREVIOUS PAGE')
    </a>
@endif
@if (!$paginator->onLastPage())
    <a href="{{ $paginator->nextPageUrl() }}" class="base-btn">
        @lang('NEXT PAGE')
    </a>
@endif
<div class="pagination-menu">
    <div class="pagination-menu__context">
        <span>@lang('Page')</span>
    </div>
    <div class="pagination-menu__input">
        <form method="GET" action="" class="paginator-input">
            @foreach (request()->except('page') as $key => $request)
                <input type="hidden" name="{{ $key }}" value="{{ $request }}">
            @endforeach
            <input type="number" name="page" value="{{ $paginator->currentPage() }}">
        </form>
    </div>
    <div class="pagination-menu__context">
        <span>@lang('of') {{ @(count($paginator->toArray()['links']) - 2) ?? 0 }}</span>
    </div>
    <div class="pagination-menu__button">
        @if (!$paginator->onFirstPage())
            <a href="{{ $paginator->previousPageUrl() }}" class="pagination-menu__btn pagination-menu__btn-prev">
                <i class="las la-angle-left"></i>
            </a>
        @endif
        @if (!$paginator->onLastPage())
            <a href="{{ $paginator->nextPageUrl() }}" class="pagination-menu__btn pagination-menu__btn-next">
                <i class="las la-angle-right"></i>
            </a>
        @endif
    </div>
</div>
