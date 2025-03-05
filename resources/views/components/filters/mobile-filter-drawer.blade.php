@props(['id' => 'filterDrawer', 'title' => 'Filter Options'])

<div class="overlay" id="filterOverlay"></div>
<div class="mobile-filter-drawer" id="{{ $id }}">
  <div class="drawer-header">
    <h3>{{ $title }}</h3>
    <button class="drawer-close-btn" id="drawerCloseBtn">
      <i class='bx bx-x'></i>
    </button>
  </div>
  <div class="drawer-content">
    {{ $slot }}
  </div>
  @if (isset($footer))
    <div class="drawer-footer">
      {{ $footer }}
    </div>
  @endif
</div>

@once
  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const mobileFilterBtn = document.getElementById('mobileFilterBtn');
        const filterDrawer = document.getElementById('{{ $id }}');
        const drawerCloseBtn = document.querySelector('#{{ $id }} .drawer-close-btn');
        const overlay = document.getElementById('filterOverlay');

        // Open drawer when filter button is clicked
        if (mobileFilterBtn) {
          mobileFilterBtn.addEventListener('click', function() {
            filterDrawer.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
          });
        }

        // Close drawer when close button or overlay is clicked
        if (drawerCloseBtn) {
          drawerCloseBtn.addEventListener('click', closeDrawer);
        }

        if (overlay) {
          overlay.addEventListener('click', closeDrawer);
        }

        function closeDrawer() {
          filterDrawer.classList.remove('open');
          overlay.classList.remove('active');
          document.body.style.overflow = ''; // Re-enable scrolling
        }

        // Close drawer when a filter is selected (optional)
        const drawerSelects = document.querySelectorAll('#{{ $id }} .drawer-select');
        drawerSelects.forEach(select => {
          select.addEventListener('change', function() {
            // Add a small delay to allow the Livewire update to complete
            setTimeout(closeDrawer, 300);
          });
        });
      });
    </script>
  @endpush
@endonce
