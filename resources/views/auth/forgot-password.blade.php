<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password</title>
    <link rel="stylesheet" href="{{ asset('css/login-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

@include('components.alert')

@if (session('status'))
            <div class="alert alert-success" style="width: max-content; align-items: center;">
                {{ session('status') }}
            </div>
        @endif

    <div class="back-btn-div">
        <span id="back-btn" onclick="window.location.href='{{'/'}}'" class='bx bxs-left-arrow-alt'></span>
    </div>

    
 


    <div class="login-container" style="max-width: 520px;">

        <h1 class="logo" style="color: white; font-weight: 800">ELEVATE READS</h1>
    <p class="small-text"
      style="color: white; font-weight: 800; font-size: 16px; text-align: center; margin-bottom: 6%">RESET YOUR <br>
      PASSWORD
    </p>

        <div style="margin-bottom: 15px; color: #d1d1d1; text-align: center;">
            Enter your email address and we'll send you a link to reset your password.
        </div>



        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <input style="height: 48px;" placeholder="ENTER YOUR ACCOUNT EMAIL" type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required
                    autofocus>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-primary"
                style="width: 100%; margin-top: 20px; height: 48px">
                Send Password Reset Link
            </button>

                <button href="{{ route('login') }}" class="login-btn"
                style="margin-top: 10px; margin-left: 0px; width: 48%; display: inline-block; float: left; border: 2px solid white; height: 48px;" onclick="window.location.href='{{ route('login') }}'">Back to Login</button>
        
        </form>
    </div>
</body>

</html>