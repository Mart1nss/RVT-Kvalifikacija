<style>
  #delete-btn {
    color: rgb(126, 6, 6);
    text-decoration: none;
    border: rgb(126, 6, 6) 1px solid;
    border-radius: 8px;
    padding: 10px;
    font-family: sans-serif;
    font-weight: 800;
    font-size: 12px;
    text-transform: uppercase;
    background-color: rgb(13, 13, 13);
    cursor: pointer;
    transition: all 0.2s;
  }

  #delete-btn:hover {
    background-color: rgb(126, 6, 6);
    color: white;
  }

  .modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
  }

  .modal-content {
    background-color: #202020;
    color: white;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    position: relative;
  }

  .modal-content h2 {
    color: white;
    font-family: sans-serif;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 16px;
    margin-bottom: 10px;
  }

  .modal-content p {
    color: rgb(128, 128, 128);
    font-family: sans-serif;
    font-size: 14px;
    margin-bottom: 20px;
  }

  #password {
    width: 100%;
    height: 38px;
    background-color: rgb(13, 13, 13);
    border: none;
    border-radius: 8px;
    font-size: 16px;
    outline: transparent;
    color: white;
    padding-left: 20px;
    margin: 10px 0;
  }

  .button-group {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
  }

  .cancel-btn {
    color: white;
    text-decoration: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-family: sans-serif;
    font-weight: 800;
    font-size: 12px;
    border: none;
    text-transform: uppercase;
    background-color: rgb(13, 13, 13);
    cursor: pointer;
    transition: all 0.2s;
  }

  .cancel-btn:hover {
    opacity: 0.5;
  }

  .delete-confirm-btn {
    color: white;
    text-decoration: none;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-family: sans-serif;
    font-weight: 800;
    font-size: 12px;
    text-transform: uppercase;
    background-color: rgb(126, 6, 6);
    cursor: pointer;
    transition: all 0.2s;
  }

  .delete-confirm-btn:hover {
    background-color: rgb(80, 80, 80);
  }

  .validation-errors {
    background-color: rgba(255, 0, 0, 0.1);
    color: rgb(255, 0, 0);
    padding: 10px;
    border-radius: 8px;
    margin-top: 5px;
    font-family: sans-serif;
    font-weight: 800;
    font-size: 12px;
    text-transform: uppercase;
    white-space: normal;
    word-wrap: break-word;
    line-height: 1.4;
    list-style-type: none;
    display: block;
  }

  [x-cloak] {
    display: none !important;
  }
</style>

<section class="space-y-6">
  <header>
    <h2 class="h2-profile">
      {{ __('Delete Account') }}
    </h2>

    <p class="mt-1 text-sm text-gray-600">
      {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
    </p>
  </header>

  <button id="delete-btn" type="button" class="delete-btn"
    onclick="document.getElementById('delete-modal').style.display='flex'">{{ __('Delete Account') }}</button>

  <div id="delete-modal" class="modal-backdrop" style="display: none;">
    <div class="modal-content">
      <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
        @csrf
        @method('delete')

        <h2>
          {{ __('Are you sure you want to delete your account?') }}
        </h2>

        <p>
          {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
        </p>

        <div class="mt-6">
          <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

          <x-text-input id="password" name="password" type="password" placeholder="{{ __('Password') }}" />

          @error('password', 'userDeletion')
            <div class="validation-errors">
              {{ $message }}
            </div>
          @enderror
        </div>

        <div class="button-group">
          <button type="button" class="cancel-btn"
            onclick="document.getElementById('delete-modal').style.display='none'">
            {{ __('Cancel') }}
          </button>

          <button type="submit" class="delete-confirm-btn">
            {{ __('Delete Account') }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Close modal when clicking outside
    document.getElementById('delete-modal').addEventListener('click', function(e) {
      if (e.target === this) {
        this.style.display = 'none';
      }
    });

    // Close modal when pressing escape
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        document.getElementById('delete-modal').style.display = 'none';
      }
    });
  </script>
</section>
