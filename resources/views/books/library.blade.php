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
  @livewireStyles
</head>

<body>
  @include('components.alert')
  @include('navbar')

  <div class="main-container">
    @livewire('books.library')
  </div>

  <script src="{{ asset('js/library-pdf.js') }}" type="module"></script>

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
  </script>

  @livewireScripts
</body>

</html>
