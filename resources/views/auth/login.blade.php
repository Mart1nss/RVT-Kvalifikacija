<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Login</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/login-style.css') }}">
</head>

<body>


  <div class="back-btn-div">
    <span id="back-btn" onclick="window.location.href='{{ '/' }}'" class='bx bxs-left-arrow-alt'></span>
  </div>


  <div class="login-container" style="max-width: 520px;">

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="logo" style="color: white; font-weight: 800">ELEVATE READS</h1>
    <p class="small-text"
      style="color: white; font-weight: 800; font-size: 16px; text-align: center; margin-bottom: 6%">LOGIN TO <br>
      ELEVATE READS
    </p>

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <!-- Email Address -->
      <div class="form-group">
        <input style="height: 48px;" placeholder="EMAIL" id="email" class="form-control" type="email"
          name="email" :value="old('email')" required autofocus autocomplete="email" />
        @error('email')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <!-- Password -->
      <div class="form-group">
        <input class="form-control" id="password" type="password" placeholder="PASSWORD" style="height: 48px;"
          name="password" required autocomplete="current-password" />
        @error('password')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <!-- Remember Me -->
      <div class="">

        <input id="remember_me" type="checkbox" class="checkbox" name="remember">
        <span class="remember-text">{{ __('Remember me') }}</span>
      </div>

      <div style="display: flex">
        <x-primary-button class="btn-primary" href="{{ route('login') }}"
          style="margin-top: 10px; height: 48px; margin-left: 0; width: 100%; display: inline-block; border: 2px solid white;">
          {{ __('Login') }}
        </x-primary-button>
      </div>

      @if (Route::has('password.request'))
        <button class="login-btn"
          style="margin-top: 10px; margin-left: 5px; width: 48%; display: inline-block; float: right; border: 2px solid white;"
          onclick="window.location.href='{{ route('password.request') }}'">{{ __('Reset Password') }}</button>
      @endif
      <x-primary-button class="login-btn"
        style="margin-top: 10px; margin-left: 0; width: 49%; display: inline-block; float: left; border: 2px solid white;"
        onclick="window.location.href='{{ route('register') }}'">{{ __('Sign Up') }}</x-primary-button>

    </form>


  </div>


  <script>
    /* fade up animation
      const overlayDiv = document.createElement('div');
      overlayDiv.style.backgroundColor = '#000';
      overlayDiv.style.position = 'fixed';
      overlayDiv.style.top = 0;
      overlayDiv.style.left = 0;
      overlayDiv.style.width = '100%';
      overlayDiv.style.height = '100%';
      overlayDiv.style.opacity = 1;

      document.body.appendChild(overlayDiv);

      const opacityAnimation = overlayDiv.animate(
        {
          opacity: 0,
        },
        {
          duration: 1000,
          easing: 'linear'
        }
      );

      opacityAnimation.onfinish = function () {
        overlayDiv.style.display = 'none';
      }; */
  </script>
</body>

</html>
