<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Register</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('css/login-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
</head>

<body>

  <div class="back-btn-div">
    <span id="back-btn" onclick="window.location.href='{{ '/' }}'" class='bx bxs-left-arrow-alt'></span>
  </div>



  <div class="login-container" style="max-width: 520px;">

    <h1 class="logo" style="color: white; font-weight: 800">ELEVATE READS</h1>
    <p class="small-text"
      style="color: white; font-weight: 800; font-size: 16px; text-align: center; margin-bottom: 6%">REGISTER IN <br>
      ELEVATE READS
    </p>

    <form method="POST" action="{{ route('register') }}">
      @csrf
      <!-- Name -->
      <div class="form-group">
        <x-text-input id="name" style="height: 48px; margin-bottom: 10px;" class="form-control" placeholder="NAME"
          type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
        @error('name')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <!-- Email Address -->
      <div class="form-group">
        <x-text-input id="email" style="height: 48px; margin-bottom: 10px;" class="form-control" placeholder="EMAIL"
          type="email" name="email" :value="old('email')" required autocomplete="username" />
        @error('email')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <!-- Password -->
      <div class="form-group">
        <x-text-input id="password" class="form-control" type="password" style="height: 48px; margin-bottom: 10px;"
          name="password" placeholder="PASSWORD" required autocomplete="new-password" />
        @error('password')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <!-- Confirm Password -->
      <div class="form-group">
        <x-text-input id="password_confirmation" class="form-control" type="password"
          style="height: 48px; margin-bottom: 10px;" name="password_confirmation" placeholder="CONFIRM PASSWORD"
          required autocomplete="new-password" />
        @error('password_confirmation')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <x-primary-button class="btn-primary"
        style="margin-top: 10px; height: 48px; margin-left: 0; width: 100%; display: inline-block; border: 2px solid white;">
        {{ __('Register') }}
      </x-primary-button>

      <button class="login-btn"
        style="display: flex; height: 48px; margin-top: 20px; width: 50%; margin-left: 0px; justify-content: center; align-items: center float: left; border: 2px solid white; vertical-align: middle;"
        onclick="window.location.href='{{ route('login') }}'">
        {{ __('Already registered?') }}
      </button>

    </form>

  </div>

</body>

</html>
