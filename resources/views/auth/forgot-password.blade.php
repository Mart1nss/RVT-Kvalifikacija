<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Reset Password</title>
  <link rel="stylesheet" href="{{ asset('css/login-style.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
  <div class="back-btn-div">
    <span id="back-btn" onclick="window.location.href='{{ '/' }}'" class='bx bxs-left-arrow-alt'></span>
  </div>

  <div class="login-container">

    <h1 class="logo"> ELEVATE READS </h1>
    <p class="small-text">RESET YOUR
      <br>
      PASSWORD
    </p>

    <div class="text-info">
      Enter your email address and we'll send you a link to reset your password.
    </div>



    <form method="POST" action="{{ route('password.email') }}">
      @csrf

      <div class="form-group">
        <input placeholder="ENTER YOUR ACCOUNT EMAIL" type="email"
          name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus>
        @error('email')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      @if (session('status'))
        <div class="alert55"
          style="width: max-content; align-items: center; background-color: none; justify-content: center; color: green;">
          {{ session('status') }}
        </div>
      @endif


      <button type="submit" class="btn-primary">
        Send Password Reset Link
      </button>

      <button class="btn-secondary" onclick="window.location.href='{{ route('login') }}'">Back to Login</button>

    </form>
  </div>
</body>

</html>
