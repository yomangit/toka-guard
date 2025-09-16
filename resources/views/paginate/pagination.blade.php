@if ($paginator->hasPages())
    <div class="flex flex-col items-center justify-between gap-2 sm:flex-row sm:px-6 py-2">

        {{-- Info --}}
        <div class="text-xs text-gray-600">
            Showing
            <span class="font-semibold">{{ $paginator->firstItem() }}</span>
            to
            <span class="font-semibold">{{ $paginator->lastItem() }}</span>
            of
            <span class="font-semibold">{{ $paginator->total() }}</span>
            results
        </div>

        {{-- Pagination --}}
        <div class="join">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <button class="join-item btn btn-xs btn-disabled">«</button>
            @else
                <button wire:click="previousPage" class="join-item btn btn-xs">«</button>
            @endif

            {{-- Page Numbers --}}
            @foreach ($elements as $element)
                {{-- Separator --}}
                @if (is_string($element))
                    <button class="join-item btn btn-xs btn-disabled">{{ $element }}</button>
                @endif

                {{-- Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <button class="join-item btn btn-xs btn-active">{{ $page }}</button>
                        @else
                            <button wire:click="gotoPage({{ $page }})" class="join-item btn btn-xs">{{ $page }}</button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" class="join-item btn btn-xs">»</button>
            @else
                <button class="join-item btn btn-xs btn-disabled">»</button>
            @endif
        </div>
    </div>
@endif
