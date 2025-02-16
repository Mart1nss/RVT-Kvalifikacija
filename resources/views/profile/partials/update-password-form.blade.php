<style>
  .save-btn {
    display: inline-flex;
    border: 1px solid rgb(0, 0, 0);
    background-color: rgb(255, 255, 255);
    color: rgb(0, 0, 0);
    padding: 10px;
    border-radius: 8px;
    font-weight: 800;
    font-size: 12px;
    text-transform: uppercase;
    text-decoration: none;
    transition: all 0.2s;
  }

  .save-btn:hover {
    cursor: pointer;
    opacity: 0.5;
  }

  .input-label {
    color: rgb(255, 255, 255);
    margin-bottom: 5px;
  }

  #update_password_current_password {
    width: 100%;
    height: 38px;
    background-color: #202020;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    outline: transparent;
    color: white;
    padding-left: 10px;
    margin: 5 0px;
  }

  #update_password_password {
    width: 100%;
    height: 38px;
    background-color: #202020;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    outline: transparent;
    color: white;
    padding-left: 10px;
    margin: 5 0px;

  }


  #update_password_password_confirmation {
    width: 100%;
    height: 38px;
    background-color: #202020;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    outline: transparent;
    color: white;
    padding-left: 10px;
    margin: 5 0px;

  }

  .input-div {
    margin-bottom: 10px;
    display: block;
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
    height: 46px;
    list-style-type: none;
    display: block;
  }

  .validation-errors ul {
    list-style-type: none;
    margin: 0;
    padding: 5px;
  }

  .validation-errors li {
    margin-bottom: 5px;
  }

  .error-message {
    color: red;
    font-size: 12px;
    margin-top: 5px;
  }

  gin-top: 5px;
</style>

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
