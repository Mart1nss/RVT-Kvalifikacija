
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

@include('navbar')


<div class="main-container">

    @if ($errors->any())
    <div class="alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="text-container"> 
        <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800;">My Progress</h1>
    </div>
    <div class="item-container"> 
        
    </div>
</div>



</body>
</html>
