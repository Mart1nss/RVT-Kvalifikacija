<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="{{ asset('css/login-style.css') }}">
</head>
<body>

    <div class="login-container" style="max-width: 520px; background-color: #1a1a1a; border-radius: 20px;">

        <h1 class="logo" style="color: white; font-weight: 800; font-family: sans-serif; font-size: 18px; justify-content: center;">Forgot your password? No problem. Just make a new account!</h1>

        <x-primary-button class="btn-primary" style="margin-top: 10px; margin-left: 0; border: 2px solid white;" onclick="window.location.href='{{ route('register') }}'">{{ __('Ok') }}</x-primary-button>

    </div>
    
</body>
</html>
