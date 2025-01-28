<link rel="stylesheet" href="{{ asset('css/pagination.css') }}">


@if ($paginator->hasPages())
  <nav class="pagination-nav">
    <div class="pagination-links" style="background-color: rgba(63, 63, 63, 0.1);">
      {{-- Previous Page Link --}}
      @if (!$paginator->onFirstPage())
        <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn">
          <i class='bx bxs-left-arrow-alt'></i>
        </a>
      @endif

      {{-- Pagination Elements --}}
      @foreach ($elements as $element)
        {{-- Array Of Links --}}
        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <span class="pagination-btn active">{{ $page }}</span>
            @else
              <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
            @endif
          @endforeach
        @endif
      @endforeach

      {{-- Next Page Link --}}
      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn">
          <i class='bx bxs-right-arrow-alt'></i>
        </a>
      @endif
    </div>
  </nav>
@endif
