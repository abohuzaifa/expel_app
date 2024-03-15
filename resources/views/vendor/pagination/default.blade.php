<!-- resources/views/vendor/pagination/default.blade.php -->

@if ($paginator->hasPages())
    <ul class="pagination" style="font-size: 18px; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="disabled" aria-disabled="true" aria-label="@lang('pagination.previous')" style="margin-right: 10px;">
                <span aria-hidden="true">{{trans('lang.prev')}}</span>
            </li>
        @else
            <li style="margin-right: 10px;">
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">{{trans('lang.prev')}}</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="disabled" aria-disabled="true" style="margin: 0 10px;"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($paginator->currentPage() == $page)
                        <li class="active" aria-current="page" style="margin: 0 10px;"><span>{{ $page }}</span></li>
                    @else
                        <li style="margin: 0 10px;"><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li style="margin-left: 10px;">
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">{{trans('lang.next')}}</a>
            </li>
        @else
            <li class="disabled" aria-disabled="true" aria-label="@lang('pagination.next')" style="margin-left: 10px;">
                <span aria-hidden="true">{{trans('lang.prev')}}</span>
            </li>
        @endif
    </ul>
@endif
