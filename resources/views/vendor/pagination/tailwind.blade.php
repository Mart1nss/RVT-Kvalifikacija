<link rel="stylesheet" href="{{ asset('css/pagination.css') }}">


@if ($paginator->hasPages())
  <nav class="pagination-nav">
    <div class="pagination-links">
      {{-- Previous Page Link --}}
      @if (!$paginator->onFirstPage())
        <button wire:click="previousPage('page')" wire:loading.attr="disabled" class="pagination-btn">
          <i class='bx bxs-left-arrow-alt'></i>
        </button>
      @endif

      {{-- Pagination Elements --}}
      @foreach ($elements as $element)
        {{-- Array Of Links --}}
        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <span class="pagination-btn active">{{ $page }}</span>
            @else
              <button wire:click="gotoPage({{ $page }}, 'page')" wire:loading.attr="disabled" class="pagination-btn">
                {{ $page }}
              </button>
            @endif
          @endforeach
        @endif
      @endforeach

      {{-- Next Page Link --}}
      @if ($paginator->hasMorePages())
        <button wire:click="nextPage('page')" wire:loading.attr="disabled" class="pagination-btn">
          <i class='bx bxs-right-arrow-alt'></i>
        </button>
      @endif
    </div>
  </nav>
@endif
