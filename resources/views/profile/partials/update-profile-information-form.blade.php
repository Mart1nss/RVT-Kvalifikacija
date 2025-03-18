<style>

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
