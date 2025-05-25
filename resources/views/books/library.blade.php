<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Library</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('css/allbooks-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/pdf-item.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modal-book-mobile.css') }}">
  @livewireStyles
</head>

<body>
  @include('components.alert')
  @include('navbar')

  <div class="main-container">
    @livewire('books.library')
  </div>

  <script>
    document.addEventListener('livewire:initialized', () => {
      // Listen for Livewire alert events
      Livewire.on('alert', ({
        type,
        message
      }) => {
        // Dispatch to the existing alert system
        window.dispatchEvent(new CustomEvent('show-alert', {
          detail: {
            message,
            type
          }
        }));
      });
    });

    // Add mobile book modal functionality
    document.addEventListener('DOMContentLoaded', function() {
      // Click event for book cards (mobile view)
      document.querySelector('.item-container').addEventListener('click', function(e) {
        // Find closest pdf-item if clicked on a book card or its child
        const pdfItem = e.target.closest('.pdf-item');
        if (pdfItem && window.innerWidth <= 768) {
          const bookId = pdfItem.dataset.bookId;
          if (bookId) {
            window.dispatchEvent(new CustomEvent('open-modal', {
              detail: {
                bookId: parseInt(bookId)
              }
            }));
            e.preventDefault();
          }
        }
      });
    });
  </script>

  @livewireScripts
</body>

</html>
