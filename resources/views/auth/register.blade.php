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

    <style>
        .back-btn-div {
          display: flex;
          margin-left: 20px;
          margin-top: 20px;

          @media(max-width: 640px) {
    margin-left: 5px;
  }
        }
    
        #back-btn {
          position: absolute;
          height: 40px;
          width: 40px;
          background: white;
          text-align: center;
          color: black;
          line-height: 40px;
          border-radius: 50%;
          cursor: pointer;
          font-size: 1.25rem;
        }
    
        @media screen and (max-width: 400px) {
          button {
            line-height: 12px;
          }
    
        }

        .remember-text {
            color: white;
            font-family: sans-serif;
            text-transform: uppercase;
            font-weight: 800;
            font-size: 14px;
        }

        .checkbox {
            width: 18px;
            height: 18px;
            vertical-align: middle;
            cursor: pointer;
        }

        .login-btn {
            border: 1px solid white;
            background-color: black;
            color: white;
            padding: 10px;
            height: 40px;
            width: 120px;
            border-radius: 20px;
            font-weight: 800;
            margin-left: 20px;
            font-size: 12px;
            text-transform: uppercase;
            transition: all 0.15;
            cursor: pointer;
        }

        .alert-danger {
            background-color: rgba(220, 38, 38, 0.2);
            color: #ff0000;
            padding: 16px 20px;
            margin-bottom: 10px;
            border-radius: 10px;
            font-family: sans-serif;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            width: 100%;
            box-sizing: border-box;
            white-space: normal;
            word-wrap: break-word;
            line-height: 1.4;
            height: 46px;
            list-style-type: none;
            display: block;
        }

        .validation-errors {
            margin-bottom: 20px;
            width: 100%;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .validation-errors ul {
            list-style-type: none;
            margin: 0;
            padding: 5px;
        }

        .validation-errors li {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        .form-group {
            width: 100%;
        }

        .form-control {
            width: 100%;
            box-sizing: border-box;
        }
      </style>

    <div class="back-btn-div">
        <span id="back-btn" onclick="window.location.href='{{'/'}}'" class='bx bxs-left-arrow-alt'></span>
      </div>



    <div class="login-container" style="max-width: 520px;">

    <h1 class="logo" style="color: white; font-weight: 800">REGISTER</h1>

    <div class="validation-errors">
        <x-input-error :messages="$errors->get('name')" class="alert-danger" />
        <x-input-error :messages="$errors->get('email')" class="alert-danger" />
        <x-input-error :messages="$errors->get('password')" class="alert-danger" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="alert-danger" />
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <!-- Name -->
        <div class="form-group">
            <x-text-input id="name" style="height: 42px;" class="form-control" placeholder="NAME" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
        </div>

        <!-- Email Address -->
        <div class="form-group">
            <x-text-input id="email" style="height: 42px;" class="form-control" placeholder="EMAIL" type="email" name="email" :value="old('email')" required autocomplete="username" />
           
        </div>

        <!-- Password -->
        <div class="form-group">
            <x-text-input id="password" class="form-control"
                            type="password"
                            style="height: 42px;"
                            name="password"
                            placeholder="PASSWORD"
                            required autocomplete="new-password" />

            
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <x-text-input id="password_confirmation" class="form-control"
                            type="password"
                            style="height: 42px;"
                            placeholder="REPEAT PASSWORD"
                            name="password_confirmation" required autocomplete="new-password" />

    
        </div>



            <x-primary-button class="btn-primary" style="margin-top: 10px; height: 48px; margin-left: 0; width: 100%; display: inline-block; border: 2px solid white;">
                {{ __('Register') }}
            </x-primary-button>

            <a class="login-btn" style="display: flex; height: 20px; text-decoration: none; margin-top: 20px; width: 50%; margin-left: 0px; justify-content: center; align-items: center float: left; border: 2px solid white;" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

    </form>

    </div>

</body>
</html>
