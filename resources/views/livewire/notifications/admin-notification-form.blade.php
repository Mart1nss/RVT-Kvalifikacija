<div class="admin-notification-form-wrapper">
  <div class="notification-form-container">
    <form wire:submit.prevent="sendNotification" class="notification-form">
      <div class="form-group flex-grow">
        <div class="input-wrapper">
          <textarea id="notification-message" class="notif-input" placeholder="Enter your notification message" required
            maxlength="250" rows="1" wire:model="message"></textarea>
          <div id="char-count" class="char-count">
            <span id="current-count">0</span> / <span>250</span>
          </div>
        </div>
      </div>

      <div class="select-container">
        <select class="filter-select" wire:model="recipientType">
          <option value="all">All Users</option>
          <option value="admins">Admins Only</option>
        </select>
      </div>

      <button class="btn btn-primary btn-md" type="submit" wire:loading.attr="disabled" wire:target="sendNotification"
        wire:loading.class="btn-loading">
        <span wire:loading.remove wire:target="sendNotification">Send</span>
        <span wire:loading wire:target="sendNotification">Sending...</span>
      </button>
    </form>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const textarea = document.getElementById('notification-message');
      const charCount = document.getElementById('current-count');
      const charCountContainer = document.getElementById('char-count');
      const maxChars = 250;

      // Function to update character count
      function updateCharCount() {
        const count = textarea.value.length;
        charCount.textContent = count;

        // Add warning class if approaching limit
        if (count >= maxChars) {
          charCountContainer.classList.add('text-danger');
        } else {
          charCountContainer.classList.remove('text-danger');
        }
      }

      // Function to adjust textarea height
      function adjustHeight() {
        textarea.style.height = 'auto';
        textarea.style.height = (textarea.scrollHeight) + 'px';
      }

      textarea.addEventListener('input', function() {
        updateCharCount();
        adjustHeight();
      });

      updateCharCount();
      adjustHeight();

      document.addEventListener('livewire:initialized', function() {
        updateCharCount();
        adjustHeight();
      });
    });
  </script>
</div>
