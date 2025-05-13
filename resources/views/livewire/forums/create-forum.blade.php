<div>
  <form wire:submit.prevent="createForum" class="create-forum-form">
    <div class="form-group">
      <label for="title" class="title-label">Title</label>
      <div class="input-wrapper">
        <input type="text" id="title" wire:model="title" class="reply-textarea" placeholder="Enter title..."
          maxlength="50">
        <span class="char-counter" id="titleCounter">0/50</span>
      </div>
      @error('title')
        <p class="error-message">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="description" class="title-label">Description</label>
      <div class="input-wrapper">
        <textarea id="description" wire:model="description" rows="4" class="reply-textarea"
          placeholder="Enter description..." maxlength="1000"></textarea>
        <span class="char-counter" id="descriptionCounter">0/1000</span>
      </div>
      @error('description')
        <p class="error-message">{{ $message }}</p>
      @enderror
    </div>

    <div class="btn-container">
      <button type="submit" class="btn btn-primary btn-md">
        Create Forum
      </button>
    </div>
  </form>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const titleInput = document.getElementById('title');
      const descriptionInput = document.getElementById('description');
      const titleCounter = document.getElementById('titleCounter');
      const descriptionCounter = document.getElementById('descriptionCounter');

      function updateCounter(input, counter, maxLength) {
        const count = input.value.length;
        counter.textContent = `${count}/${maxLength}`;

        if (count >= maxLength) {
          counter.classList.add('at-limit');
          counter.classList.remove('near-limit');
        } else if (count >= maxLength * 0.8) {
          counter.classList.add('near-limit');
          counter.classList.remove('at-limit');
        } else {
          counter.classList.remove('near-limit', 'at-limit');
        }
      }

      titleInput.addEventListener('input', () => updateCounter(titleInput, titleCounter, 50));
      descriptionInput.addEventListener('input', () => updateCounter(descriptionInput, descriptionCounter, 1000));

      // Initialize counters
      updateCounter(titleInput, titleCounter, 50);
      updateCounter(descriptionInput, descriptionCounter, 1000);

      // Update counters when Livewire updates the DOM
      document.addEventListener('livewire:initialized', function() {
        updateCounter(titleInput, titleCounter, 50);
        updateCounter(descriptionInput, descriptionCounter, 1000);
      });
    });
  </script>
</div>
