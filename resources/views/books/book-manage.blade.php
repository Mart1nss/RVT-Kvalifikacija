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
    // Helper function to safely show alerts - only show if message is not empty
    function safeShowAlert(message, type = 'success') {
      if (!message) return;

      if (typeof window.showAlert === 'function') {
        window.showAlert(message, type);
      } else {
        // Fallback alert implementation
        const alertContainer = document.getElementById('alertContainer');
        if (alertContainer) {
          const alertDiv = document.createElement('div');
          alertDiv.className = `alert alert-${type || 'success'}`;
          alertDiv.textContent = message;

          // Clear existing alerts
          alertContainer.innerHTML = '';
          alertContainer.appendChild(alertDiv);

          // Show and hide
          alertContainer.style.display = 'block';
          setTimeout(() => {
            alertContainer.style.display = 'none';
          }, 3000);
        }
      }
    }

    // Create a clean alert handler that avoids duplicate alerts
    let lastAlertMessage = '';
    let lastAlertTime = 0;

    function handleAlert(data) {
      // Skip if this is a duplicate alert within 1 second
      const now = Date.now();
      let message = '';
      let type = 'success';

      // Extract message and type from various data formats
      if (Array.isArray(data) && data.length > 0) {
        if (data[0] && typeof data[0] === 'object') {
          message = data[0].message || '';
          type = data[0].type || 'success';
        }
      } else if (data && typeof data === 'object') {
        message = data.message || '';
        type = data.type || 'success';
      }

      // Skip empty messages
      if (!message) return;

      // Prevent duplicates
      if (message === lastAlertMessage && now - lastAlertTime < 1000) return;

      // Update tracking
      lastAlertMessage = message;
      lastAlertTime = now;

      // Show alert
      safeShowAlert(message, type);
    }

    document.addEventListener('livewire:initialized', () => {
      // Single handler for Livewire alerts
      Livewire.on('alert', handleAlert);
    });

    // Listen for DOM-based events too
    window.addEventListener('show-alert', (event) => {
      if (event.detail) handleAlert(event.detail);
    });

    // Clean hook for Livewire processing
    if (window.Livewire) {
      window.Livewire.hook('message.processed', (message) => {
        try {
          if (message.response?.effects?.dispatches) {
            const alerts = message.response.effects.dispatches.filter(d => d.event === 'alert');
            if (alerts.length > 0) {
              // Only process the first alert
              handleAlert(alerts[0].payload);
            }
          }
        } catch (e) {
          // Silent error handling
        }
      });
    }
  </script>

  @livewireScripts
</body>

</html>
