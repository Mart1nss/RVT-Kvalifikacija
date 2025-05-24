<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Login</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('css/components/alert.css') }}">
  <link rel="stylesheet" href="{{ asset('css/login-style.css') }}">
</head>

<body>


  <div class="back-btn-div">
    <span id="back-btn" onclick="window.location.href='{{ '/' }}'" class='bx bxs-left-arrow-alt'></span>
  </div>


  <div class="login-container">

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('success'))
      <div class="alert alert-success"
        style="background-color: rgb(0,126,0); color: white; padding: 15px; margin-bottom: 20px; border-radius: 4px; text-align: center;">
        {{ session('success') }}
      </div>
    @endif

    @if (session('error'))
      <div class="alert alert-danger"
        style="background-color: rgb(126,6,6); color: white; padding: 15px; margin-bottom: 20px; border-radius: 4px; text-align: center;">
        {{ session('error') }}
      </div>
    @endif

    <h1 class="logo">ELEVATE READS</h1>
    <p class="small-text">LOGIN TO <br>
      ELEVATE READS
    </p>

    <form method="POST" action="{{ route('login') }}" novalidate>
      @csrf

      <!-- Email Address -->
      <div class="form-group">
        <input placeholder="EMAIL" id="email" class="form-control" type="email" name="email"
          value="{{ session('registered_email', old('email')) }}" required autofocus autocomplete="email" />
        @error('email')
          <div class="error-message" style="text-transform: uppercase; margin-top: 0px;">{{ $message }}</div>
        @enderror
      </div>

      <!-- Password -->
      <div class="form-group">
        <div class="password-container">
          <input class="form-control" id="password" type="password" placeholder="PASSWORD" name="password" required
            autocomplete="current-password" />
          <i class='bx bx-hide toggle-password' onclick="togglePassword('password', this)"></i>
        </div>
        @error('password')
          <div class="error-message" style="text-transform: uppercase; margin-top: 0px;">{{ $message }}</div>
        @enderror
      </div>



      <div style="display: flex">
        <x-primary-button class="btn-primary" href="{{ route('login') }}">
          Login
        </x-primary-button>
      </div>
      <div style="display: flex; justify-content: space-between; gap: 10px;">
        @if (Route::has('password.request'))
          <button class="btn-secondary" onclick="window.location.href='{{ route('password.request') }}'">Reset
            Password</button>
        @endif
        <button class="btn-secondary" onclick="window.location.href='{{ route('register') }}'">Sign Up</button>
      </div>

    </form>


  </div>



  <script>
    // toggle password visibility
    function togglePassword(inputId, icon) {
      const input = document.getElementById(inputId);
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bx-hide');
        icon.classList.add('bx-show');
      } else {
        input.type = 'password';
        icon.classList.remove('bx-show');
        icon.classList.add('bx-hide');
      }
    }
  </script>
</body>

</html>
