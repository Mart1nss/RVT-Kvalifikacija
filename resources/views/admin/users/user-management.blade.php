<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('css/user-manage/usermanage-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modal-confirmation-delete.css') }}">
  <link rel="stylesheet" href="{{ asset('css/main-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/mobile-filter-drawer.css') }}">
  @livewireStyles
</head>

<body>
  @include('components.alert')
  @include('navbar')

  <div class="main-container">
    @livewire('users.user-management')
  </div>

  @livewireScripts
  @stack('scripts')

  <script>
    document.addEventListener('livewire:initialized', () => {
      Livewire.on('alert', (data) => {
        if (window.showAlert) {
          window.showAlert(data[0].message, data[0].type);
        }
      });

      Livewire.on('show-alert', (data) => {
        if (window.showAlert) {
          window.showAlert(data.message, data.type);
        }
      });
    });

    // Mobile filter drawer functionality
    document.addEventListener('DOMContentLoaded', function() {
      const mobileFilterBtn = document.getElementById('mobileFilterBtn');
      const filterDrawer = document.getElementById('filterDrawer');
      const drawerCloseBtn = document.getElementById('drawerCloseBtn');
      const overlay = document.getElementById('filterOverlay');
      const accordionHeaders = document.querySelectorAll('.accordion-header');

      // Open drawer when filter button is clicked
      if (mobileFilterBtn) {
        mobileFilterBtn.addEventListener('click', function() {
          filterDrawer.classList.add('open');
          overlay.classList.add('active');
          document.body.style.overflow = 'hidden'; // Prevent scrolling when drawer is open
        });
      }

      if (drawerCloseBtn) {
        drawerCloseBtn.addEventListener('click', closeDrawer);
      }

      if (overlay) {
        overlay.addEventListener('click', closeDrawer);
      }

      function closeDrawer() {
        filterDrawer.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
      }

      // Toggle accordion sections
      accordionHeaders.forEach(header => {
        header.addEventListener('click', function() {
          const accordionId = this.getAttribute('data-accordion');
          const content = this.nextElementSibling;

          this.classList.toggle('active');

          if (content.classList.contains('open')) {
            content.classList.remove('open');
            content.style.maxHeight = '0px';
          } else {
            content.classList.add('open');
            content.style.maxHeight = content.scrollHeight + 'px';
          }
        });
      });

      // Close drawer when a filter is selected
      const drawerSelects = document.querySelectorAll('.drawer-select');
      drawerSelects.forEach(select => {
        select.addEventListener('change', function() {
          setTimeout(closeDrawer, 300);
        });
      });

      document.addEventListener('livewire:update', function() {
        const updatedAccordionHeaders = document.querySelectorAll('.accordion-header');
        updatedAccordionHeaders.forEach(header => {
          const content = header.nextElementSibling;
          if (header.classList.contains('active') && content) {
            content.classList.add('open');
            content.style.maxHeight = content.scrollHeight + 'px';
          }
        });
      });

      // Reset filter UI elements after clearing
      Livewire.on('filtersCleared', () => {
        // reset search input
        const searchInput = document.querySelector('input[wire\\:model\\.live\\.debounce\\.300ms="searchQuery"]');
        if (searchInput) {
          searchInput.value = '';
          searchInput.dispatchEvent(new Event('input'));
        }

        // reset a select element
        const resetSelect = (selector, defaultValue) => {
          const selectElement = document.querySelector(selector);
          if (selectElement) {
            selectElement.value = defaultValue;
            selectElement.dispatchEvent(new Event('change'));
          }
        };

        // Reset desktop dropdowns
        resetSelect('.search-filter-container select[wire\\:model\\.live="sortOption"]', 'newest');
        resetSelect('.search-filter-container select[wire\\:model\\.live="filterUserType"]', '');
        resetSelect('.search-filter-container select[wire\\:model\\.live="filterBanStatus"]', '');

        // Reset mobile dropdowns
        resetSelect('#filterDrawer select[wire\\:model\\.live="sortOption"]', 'newest');
        resetSelect('#filterDrawer select[wire\\:model\\.live="filterUserType"]', '');
        resetSelect('#filterDrawer select[wire\\:model\\.live="filterBanStatus"]', '');
      });
    });
  </script>
</body>

</html>
