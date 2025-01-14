<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adminhome-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pdf-carousel.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf_viewer.min.css">
    <title>Dashboard</title>
</head>

@include('components.alert')
@include('navbar')

    <style>

        .item-container {
            background-color: rgb(37, 37, 37);
            border-bottom-right-radius: 10px;
            border-bottom-left-radius: 10px;
            padding: 16px;
            display: flex;
            flex-direction: column;
        }

        #dropdown-1 {
            background-color: rgb(56, 56, 56);
        }
        
        .back-btn-div {
            display: none;
        }

        #back-btn {
            display: none;
        }

        .logo {
            display: flex;
        }

    </style>

    <div class="main-container">
        <div class="text-container">
            <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800;">Dashbaord</h1>
        </div>

        <div class="item-container">

        <h1 style="margin-bottom: 20px; color: white; font-family: sans-serif; font-weight: 800; font-size: 18px;">Welcome, {{ auth()->user()->name }}!</h1>
            
        <a class="btn-dashboard" href="{{'/bookpage'}}"><i id="dashboardIcon" class='bx bx-book'></i> Library</a>

        </div>

        @include('components.book-carousels')
    </div>

    <script src="{{ asset('js/pdf-carousel.js') }}" defer></script>
    <script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs"></script>
