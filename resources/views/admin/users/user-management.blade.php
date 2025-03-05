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
    // Initialize event listeners when Livewire is ready
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

      // Toggle accordion sections
      accordionHeaders.forEach(header => {
        header.addEventListener('click', function() {
          const accordionId = this.getAttribute('data-accordion');
          const content = this.nextElementSibling;

          // Toggle active class on header
          this.classList.toggle('active');

          // Toggle open class on content
          if (content.classList.contains('open')) {
            content.classList.remove('open');
            content.style.maxHeight = '0px';
          } else {
            content.classList.add('open');
            content.style.maxHeight = content.scrollHeight + 'px';
          }
        });
      });

      // Close drawer when a filter is selected (optional)
      const drawerSelects = document.querySelectorAll('.drawer-select');
      drawerSelects.forEach(select => {
        select.addEventListener('change', function() {
          // Add a small delay to allow the Livewire update to complete
          setTimeout(closeDrawer, 300);
        });
      });

      // Handle Livewire updates - reattach event listeners
      document.addEventListener('livewire:update', function() {
        // Re-initialize accordion functionality after Livewire updates
        const updatedAccordionHeaders = document.querySelectorAll('.accordion-header');
        updatedAccordionHeaders.forEach(header => {
          const content = header.nextElementSibling;
          if (header.classList.contains('active') && content) {
            content.classList.add('open');
            content.style.maxHeight = content.scrollHeight + 'px';
          }
        });
      });
    });
  </script>
</body>

</html>
