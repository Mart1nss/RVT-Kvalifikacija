<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
</head>

@include('navbar')

    <style>

        .item-container {
            background-color: rgb(37, 37, 37);
            border-bottom-right-radius: 10px;
            border-bottom-left-radius: 10px;
            padding: 16px;
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

            
            <a class="btn-dashboard" href="{{'/bookpage'}}"><i id="dashboardIcon" class='bx bx-book'></i> View All Books</a>

        </div>
        
    </div>
</div>



