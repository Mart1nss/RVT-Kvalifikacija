<section>
  <header>
    <h2>Update Password</h2>
    <p>Ensure your account is using a long, random password to stay secure.</p>
  </header>

  <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
    @csrf
    @method('put')

    <div class="form-group">
      <label class="input-label" for="update_password_current_password">Current Password</label>
      <input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full"
        autocomplete="current-password" />
      @if ($errors->updatePassword->has('current_password'))
        <div class="error-message">{{ $errors->updatePassword->first('current_password') }}</div>
      @endif
    </div>

    <div class="form-group">
      <label class="input-label" for="update_password_password">New Password</label>
      <input id="update_password_password" name="password" type="password" class="mt-1 block w-full"
        autocomplete="new-password" />
      @if ($errors->updatePassword->has('password'))
        <div class="error-message">{{ $errors->updatePassword->first('password') }}</div>
      @endif
    </div>

    <div class="form-group">
      <label class="input-label" for="update_password_password_confirmation">Confirm Password</label>
      <input id="update_password_password_confirmation" name="password_confirmation" type="password"
        class="mt-1 block w-full" autocomplete="new-password" />
      @if ($errors->updatePassword->has('password_confirmation'))
        <div class="error-message">{{ $errors->updatePassword->first('password_confirmation') }}</div>
      @endif
    </div>

    <div class="flex items-center gap-4" style="margin-top: 20px;">
      <button type="submit" class="save-btn">Save</button>
    </div>
  </form>

  @if (session('status') === 'password-updated')
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        window.showAlert('Password updated successfully!', 'success');
      });
    </script>
  @endif
</section>
