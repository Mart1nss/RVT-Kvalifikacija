<style>
  .save-btn {
    display: inline-flex;
    border: none;
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

  h2 {
    margin-bottom: 5px;
  }

  p {
    color: rgb(128, 128, 128);
    margin-bottom: 15px;
  }

  #input-label {
    color: rgb(255, 255, 255);

  }

  #name {
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

  #email {
    width: 100%;
    height: 38px;
    background-color: #202020;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    outline: transparent;
    padding-left: 10px;
    margin: 5 0px;
    color: white;

  }

  #delete-btn {
    color: rgb(255, 0, 0);
    text-decoration: none;
    border: rgb(255, 0, 0) 1px solid;
    border-radius: 8px;
    padding: 10px;
    font-family: sans-serif;
    font-weight: 800;
    font-size: 12px;
    text-transform: uppercase;
    background-color: rgb(13, 13, 13);
    cursor: pointer;
  }

  .saved-text {
    color: green;
    font-size: 14px;
    margin-top: 15px;
    margin-bottom: 0px;
    width: 100%;
    background-color: #072907;
    height: 38px;
    border-radius: 20px;
    padding-left: 20px;
    display: flex;
    padding-top: 8px;
    font-family: sans-serif;
    font-weight: 800;
    font-size: 14px;
    text-transform: uppercase;

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
</style>

<section>
  <header>
    <h2>Profile Information</h2>
    <p>Update your account's profile information and email address.</p>
  </header>

  <form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
  </form>

  <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
    @csrf
    @method('patch')

    <div class="form-group">
      <label id="input-label" for="name">Name</label>
      <input id="name" name="name" type="text" class="mt-1 block w-full"
        value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
      @error('name')
        <div class="error-message">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px;">
        <label id="input-label" for="email">Email</label>
        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && $user->hasVerifiedEmail())
          <div
            style="background-color: rgba(75, 181, 67, 0.1); padding: 4px 12px; border-radius: 20px; display: flex; align-items: center; gap: 4px;">
            <i class='bx bxs-check-circle' style="color: #4BB543;"></i>
            <span style="color: #4BB543; font-size: 12px;">Verified</span>
          </div>
        @endif
      </div>

      <input id="email" name="email" type="email" class="mt-1 block w-full"
        value="{{ old('email', $user->email) }}" required autocomplete="username" />
      @error('email')
        <div class="error-message">{{ $message }}</div>
      @enderror
    </div>

    <div class="flex items-center gap-4" style="margin-top: 20px;">
      <button type="submit" class="save-btn">Save</button>
    </div>
  </form>

  @if (session('status') === 'profile-updated')
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        window.showAlert('Profile updated successfully!', 'success');
      });
    </script>
  @endif
</section>
