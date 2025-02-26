<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/usermanage-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modal-confirmation-delete.css') }}">
  <link rel="stylesheet" href="{{ asset('css/main-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
  @livewireStyles
</head>

<body>
  @include('components.alert')
  @include('navbar')

  <div class="main-container">
    @livewire('users.user-management')
  </div>

  @livewireScripts
  
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
  </script>
</body>
</html> 