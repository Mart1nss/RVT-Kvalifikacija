<!-- Floating Alert Container -->
<link rel="stylesheet" href="{{ asset('css/components/alert.css') }}">

<div class="alert-container" id="alertContainer">
  @if (session('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
  @endif
  @if (session('warning'))
    <div class="alert alert-warning">
      {{ session('warning') }}
    </div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger">
      {{ session('error') }}
    </div>
  @endif
</div>

<style>
  .alert-warning {
    background-color: #ff9800;
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    font-family: sans-serif;
    font-size: 14px;
    display: flex;
    align-items: center;
    margin-bottom: 10px;
  }
</style>

<script>
  // Show alert if there's a message
  document.addEventListener('DOMContentLoaded', function() {
    const alertContainer = document.getElementById('alertContainer');
    if (alertContainer.querySelector('.alert')) {
      alertContainer.style.display = 'block';

      // Hide after 3 seconds
      setTimeout(() => {
        alertContainer.style.display = 'none';
      }, 3000);
    }

    // Show warning for unverified email if applicable
    @if (auth()->check() &&
            auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&
            !auth()->user()->hasVerifiedEmail())
      window.showAlert('Please verify your email address to access all features.', 'warning');
    @endif

    // Listen for both 'alert' and 'show-alert' events
    ['alert', 'show-alert'].forEach(eventName => {
      window.addEventListener(eventName, function(event) {
        const {
          type,
          message
        } = event.detail;
        // console.log('Alert received:', type, message); // Removed for cleaner console
        window.showAlert(message, type);
      });
    });

    // Also listen for Livewire events
    if (window.Livewire) {
      window.Livewire.on('alert', function(data) {
        // Handle different data formats
        if (Array.isArray(data) && data.length > 0) {
          data = data[0]; // Use first item if array
        }

        if (data && typeof data === 'object' && data.message) {
          window.showAlert(data.message, data.type);
        }
      });
    }
  });

  // Function to show alert programmatically
  window.showAlert = function(message, type = 'success') {
    if (!message) {
      return;
    }

    const alertContainer = document.getElementById('alertContainer');
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type || 'success'}`;
    alertDiv.textContent = message;

    // Clear existing alerts
    alertContainer.innerHTML = '';
    alertContainer.appendChild(alertDiv);

    alertContainer.style.display = 'block';
    setTimeout(() => {
      alertContainer.style.display = 'none';
    }, 3000);
  };
</script>

<script>
  function fadeOutAndRemove(element) {
    element.classList.add('fade-out');
    setTimeout(function() {
      element.remove();
    }, 1500);
  }

  window.addEventListener('load', function() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
      setTimeout(function() {
        fadeOutAndRemove(alert);
      }, 1500);
    });
  });
</script>
