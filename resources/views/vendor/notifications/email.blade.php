<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        .main-cont {
            font-family: sans-serif;
            background-color: black;
            color: white;
            width: 100%;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .title {
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 540px;
            justify-content: center;
            align-items: center;
            background-color: black;
            margin: 20px auto;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: white;
            font-size: 24px;
            font-weight: 800;
            text-transform: uppercase;
        }

        p {
            color: white;
            font-size: 14px;
            font-weight: 800;
            justify-content: center;
            align-items: center;
        }

        .button {
            display: inline-block;
            background-color: white;
            color: black;
            padding: 10px 20px;
            font-weight: 800;
            font-size: 12px;
            text-transform: uppercase;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.15s;
        }

        .button:hover {
            opacity: 0.5;
            cursor: pointer;
        }

        .footer-div {
            display: inline-block;
            justify-content: center;
            align-items: center;
            margin: 20px auto;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="main-cont">
        <div class="container">
            <div class="title">
                <h1>Password Reset Request</h1>
            </div>


            @foreach ($introLines as $line)
                <p>{{ $line }}</p>
            @endforeach

            @isset($actionText)
                <p>
                    <a style="color: black;" href="{{ $actionUrl }}" class="button">{{ $actionText }}</a>
                </p>
            @endisset

            @foreach ($outroLines as $line)
                <p>{{ $line }}</p>
            @endforeach

            <p class="footer" style="font-size: 10px; font-style: italic">
                © {{ date('Y') }} Elevate Reads. All rights reserved.
            </p>


        </div>
    </div>
</body>

</html>