<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Reset Password</title>
  <link rel="stylesheet" href="{{ asset('css/login-style.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

  <div class="login-container"">

    <h1 class="logo">ELEVATE READS</h1>
    <p class="small-text">RESET YOUR
      <br>
      PASSWORD
    </p>

    <form method="POST" action="{{ route('password.store') }}">
      @csrf

      <!-- Password Reset Token -->
      <input type="hidden" name="token" value="{{ $request->route('token') }}">

      <!-- Email Address -->
      <div class="form-group">
        <input placeholder="ENTER YOUR ACCOUNT EMAIL" type="email" name="email" id="email" class="form-control"
          value="{{ old('email') }}" required autofocus>
        @error('email')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <!-- Password -->
      <div class="form-group">
        <div class="password-container">
          <input class="form-control" id="password" placeholder="ENTER YOUR NEW PASSWORD" type="password"
            name="password" value="{{ old('password') }}" required autofocus>
          <i class='bx bx-hide toggle-password' onclick="togglePassword('password', this)"></i>
        </div>
        @error('password')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <!-- Confirm Password -->
      <div class="form-group">
        <div class="password-container">
          <input class="form-control" id="password_confirmation" placeholder="CONFIRM YOUR NEW PASSWORD" type="password"
            name="password_confirmation" value="{{ old('password_confirmation') }}" required autofocus>
          <i class='bx bx-hide toggle-password' onclick="togglePassword('password_confirmation', this)"></i>
        </div>
        @error('password_confirmation')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>


      <button class="btn-primary">
        Reset Password
      </button>

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
