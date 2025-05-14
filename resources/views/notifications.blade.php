<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Notifications</title>
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  @livewireStyles
</head>

<body>

  @include('navbar')
  @include('components.alert')

  <div class="main-container">
    <div class="text-container" style="background: transparent; padding: 0; margin-bottom: 20px;">
      <h1 class="text-container-title">Notifications</h1>
    </div>

    <div class="item-container">
      <h2 class="item-container-title" style="margin-bottom: 16px; font-weight: 800;">Send Notification</h2>
      <div class="filter-div">
        <!-- Livewire Admin Notification Form Component -->
        @livewire('notifications.admin-notification-form')
      </div>
    </div>

    <!-- Livewire Notification List Component -->
    @livewire('notifications.notification-list')
  </div>

  @livewireScripts

  <script>
    // Listen for Livewire events to show alerts and refresh components
    document.addEventListener('livewire:initialized', function() {
      Livewire.on('notificationSent', function() {
        // Show success alert
        window.showAlert('Notification sent successfully!', 'success');

        // Refresh the navbar notification component
        Livewire.dispatch('refreshNotifications');
      });

      Livewire.on('notificationDeleted', function() {
        // Show success alert
        window.showAlert('Notification deleted successfully!', 'success');

        // Refresh the navbar notification component
        Livewire.dispatch('refreshNotifications');
      });
    });
  </script>
</body>

</html>
