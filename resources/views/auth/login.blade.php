<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
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

        ul {
    list-style-type: none;
}


      </style>
    
      <div class="back-btn-div">
        <span id="back-btn" onclick="window.location.href='{{'/'}}'" class='bx bxs-left-arrow-alt'></span>
      </div>



    <div class="login-container" style="max-width: 520px;">

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <h1 class="logo" style="color: white; font-weight: 800">LOGIN</h1>
        <!--<p class="small-text"
      style="color: white; font-weight: 800; font-size: 16px; text-align: center; margin-bottom: 6%">LOGIN TO [NOSAUKUMS]
        </p>-->

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <x-input-error :messages="$errors->get('email')"  class="alert-danger" />
            <x-input-error :messages="$errors->get('password')" class="alert-danger" />
            <!-- Email Address -->
            
            <div class="form-group" >
                
                <input style="height: 42px;" placeholder="EMAIL" id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                
            </div>

            <!-- Password -->
            <div class="form-group" >
                <input class="form-control" id="password"
                                type="password"
                                placeholder="PASSWORD"
                                style="height: 42px;"
                                name="password"
                                required autocomplete="current-password" />

            </div>

            <!-- Remember Me
            <div class="">

                    <input id="remember_me" type="checkbox" class="checkbox" name="remember">
                    <span class="remember-text">{{ __('Remember me') }}</span>
            </div> -->

            <div style="display: flex">
              <x-primary-button class="btn-primary" href="{{ route('login') }}" style="margin-top: 10px; height: 48px; margin-left: 0; width: 100%; display: inline-block; border: 2px solid white;">
              {{ __('Login') }}
              </x-primary-button>
            </div>

            @if (Route::has('password.request'))
              <button class="login-btn" style="margin-top: 10px; margin-left: 5px; width: 48%; display: inline-block; float: right; border: 2px solid white;" onclick="window.location.href='{{ route('password.request') }}'">{{ __('Reset Password') }}</button>
            @endif
              <x-primary-button class="login-btn" style="margin-top: 10px; margin-left: 0; width: 49%; display: inline-block; float: left; border: 2px solid white;" onclick="window.location.href='{{ route('register') }}'">{{ __('Sign Up') }}</x-primary-button>

        </form>
        

    </div>


    <script>
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
          navbar.style.display = 'block';
        };
    
      </script>

</body>
</html>
    

