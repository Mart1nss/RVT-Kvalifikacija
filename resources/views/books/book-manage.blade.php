<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Book Management</title>
  <link rel="stylesheet" href="{{ asset('css/allbooks-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/product-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modal-edit.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modal-confirmation-delete.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/pdf-item.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modal-book-mobile.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>


  @livewireStyles
</head>

<body>
  @include('components.alert')
  @include('navbar')

  <div class="main-container">
    @livewire('books.book-management')
  </div>

  <script>
    // Add mobile book modal functionality
    document.addEventListener('DOMContentLoaded', function() {
      // Click event for book cards (mobile view)
      document.querySelector('.item-container').addEventListener('click', function(e) {
        // Find closest pdf-item if clicked on a book card or its child
        const pdfItem = e.target.closest('.pdf-item');
        if (pdfItem && window.innerWidth <= 768) {
          const bookId = pdfItem.dataset.bookId;
          if (bookId) {
            // Dispatch event to open the modal
            window.dispatchEvent(new CustomEvent('open-modal', {
              detail: {
                bookId: parseInt(bookId)
              }
            }));
            e.preventDefault();
          }
        }
      });

      // Listen for edit/delete events from mobile modal
      window.addEventListener('openEditModal', function(e) {
        if (e.detail && e.detail.bookId) {
          // Find and trigger the edit button for this book
          const editBtn = document.querySelector(`.pdf-item[data-book-id="${e.detail.bookId}"] .edit-btn`);
          if (editBtn) {
            editBtn.click();
          }
        }
      });

      window.addEventListener('confirmDelete', function(e) {
        if (e.detail && e.detail.bookId) {
          // Find and trigger the delete button for this book
          const deleteBtn = document.querySelector(`.pdf-item[data-book-id="${e.detail.bookId}"] .delete-btn`);
          if (deleteBtn) {
            deleteBtn.click();
          }
        }
      });
    });
  </script>

  @livewireScripts
</body>

</html>
