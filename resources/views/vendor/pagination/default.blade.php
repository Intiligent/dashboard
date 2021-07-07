<div class="el-text-center el-pagination is-background">
@if ($paginator->hasPages())
    @if ($paginator->onFirstPage())
        <button type="button" disabled="disabled" class="btn-prev"><i class="el-icon-arrow-left32"></i></button>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn-prev"><i class="el-icon-arrow-left32"></i></a>
    @endif
    <ul class="el-pager">
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="el-icon more btn-quicknext el-icon-more link disabled"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="number active">{{ $page }}</li>
                    @else
                        <li class="number link"><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach
    </ul>
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="btn-next" rel="next">
            <i class="el-icon-arrow-right32"></i>
        </a>
    @else
        <button type="button" class="btn-next"><i class="el-icon-arrow-right32"></i></button>
    @endif
@endif
</div>
