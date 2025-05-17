<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Register</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('css/login-style.css') }}">
</head>

<body>

  <div class="back-btn-div">
    <span id="back-btn" onclick="window.location.href='{{ '/' }}'" class='bx bxs-left-arrow-alt'></span>
  </div>



  <div class="login-container">

    <h1 class="logo">ELEVATE READS</h1>
    <p class="small-text">REGISTER IN <br>
      ELEVATE READS
    </p>

    <form method="POST" action="{{ route('register') }}">
      @csrf

      <!-- Honeypot -->
      <div style="position: absolute; left: -5000px;" aria-hidden="true">
        <label for="middle_name">Middle Name</label>
        <input id="middle_name" type="text" name="middle_name" tabindex="-1" autocomplete="off">
      </div>

      <!-- Name -->
      <div class="form-group">
        <x-text-input id="name" class="form-control" placeholder="NAME" type="text" name="name"
          :value="old('name')" required autofocus autocomplete="name" />
        @error('name')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <!-- Email Address -->
      <div class="form-group">
        <x-text-input id="email" class="form-control" placeholder="EMAIL" type="email" name="email"
          :value="old('email')" required autocomplete="username" />
        @error('email')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <!-- Password -->
      <div class="form-group">
        <div class="password-container">
          <x-text-input id="password" class="form-control" type="password" name="password" placeholder="PASSWORD"
            required autocomplete="new-password" />
          <i class='bx bx-hide toggle-password' onclick="togglePassword('password', this)"></i>
        </div>
        @error('password')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <!-- Confirm Password -->
      <div class="form-group">
        <div class="password-container">
          <x-text-input id="password_confirmation" class="form-control" type="password" name="password_confirmation"
            placeholder="CONFIRM PASSWORD" required autocomplete="new-password" />
          <i class='bx bx-hide toggle-password' onclick="togglePassword('password_confirmation', this)"></i>
        </div>
        @error('password_confirmation')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <button class="btn-primary">
        Register
      </button>

      <button class="btn-secondary" onclick="window.location.href='{{ route('login') }}'">
        Already registered?
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
