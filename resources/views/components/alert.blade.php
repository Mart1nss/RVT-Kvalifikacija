<!-- Floating Alert Container -->
<div class="alert-container" id="alertContainer">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif
    @if(session('error'))
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
        @if(auth()->check() && auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
            window.showAlert('Please verify your email address to access all features.', 'warning');
        @endif
    });

    // Function to show alert programmatically
    window.showAlert = function(message, type = 'success') {
        const alertContainer = document.getElementById('alertContainer');
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;
        
        // Clear existing alerts
        alertContainer.innerHTML = '';
        alertContainer.appendChild(alertDiv);
        
        // Show and hide
        alertContainer.style.display = 'block';
        setTimeout(() => {
            alertContainer.style.display = 'none';
        }, 3000);
    };
</script>
