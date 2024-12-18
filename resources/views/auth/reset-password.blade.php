<html>

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

    <div class="login-container" style="max-width: 520px;">

        <h1 class="logo" style="color: white; font-weight: 800">ELEVATE READS</h1>
        <p class="small-text"
            style="color: white; font-weight: 800; font-size: 16px; text-align: center; margin-bottom: 6%">RESET YOUR
            <br>
            PASSWORD
        </p>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div class="form-group">
                <input style="height: 48px;" placeholder="ENTER YOUR ACCOUNT EMAIL" type="email" name="email" id="email"
                    class="form-control" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <input style="height: 48px;" class="form-control" id="password" placeholder="ENTER YOUR NEW PASSWORD" type="password"
                    name="password" value="{{ old('password') }}" required autofocus>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <input style="height: 48px;" class="form-control" id="password_confirmation" placeholder="CONFIRM YOUR NEW PASSWORD"
                    type="password" name="password_confirmation" value="{{ old('password_confirmation') }}" required
                    autofocus>
                @error('password_confirmation')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>


            <x-primary-button class="btn-primary"
                style="margin-top: 10px; height: 48px; margin-left: 0; width: 100%; display: inline-block; border: 2px solid white;">
                {{ __('Reset Password') }}
            </x-primary-button>

        </form>

    </div>
</body>

</html>