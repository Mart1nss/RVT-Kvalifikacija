@include('navbar')

    <style>
        .item-container {
            background-color: rgb(37, 37, 37);
            border-bottom-right-radius: 10px;
            border-bottom-left-radius: 10px;
            padding: 16px;
            display: flex;
        }

        @media (max-width: 769px) {
            .item-container {
                display: block;
            }
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
            margin: 0;
            padding: 0;
        }



        .h1-text {
            color: white;
            text-transform:uppercase;
            font-family: sans-serif;
            font-weight: 800;
        }

        .stats-text {
            color: white;
            border-bottom: 1px white solid;
            font-size: 18px;
            font-family: sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .btn-div {
            width: 60%;
        }

        .stats-div {
            width: 40%;
            background-color: #1c1a1a;
            padding: 10px;
            border-radius: 10px;
            @media (max-width: 769px) {
                width: 100%;
                margin-top: 20px;
                margin-left: 0px;
            }
        }

        

    </style>


    <div class="main-container">

        <div class="text-container">
            <h1 class="h1-text">Dashbaord</h1>
        </div>

        <div class="item-container">

            <div class="btn-div">
            
            <a style="margin-bottom: 20px;" class="btn-dashboard" href="{{'/uploadpage'}}"><i id="dashboardIcon" class='bx bx-cog' ></i> Manage Books</a>

            <a style="margin-bottom: 20px;" class="btn-dashboard" href="{{'/managepage'}}"><i id="dashboardIcon" class='bx bx-user'></i> Manage Users</a>
            
            <a class="btn-dashboard" href="{{'/bookpage'}}"><i id="dashboardIcon" class='bx bx-book'></i> View All Books</a>

            </div>

            <div class="stats-div">
                <h1 class="h1-text" style="margin-bottom: 10px;">STATS</h1>

                <div class="stats-card">
                    <p class="stats-text">Total Books: {{ $bookCount }}</p>
                    <p class="stats-text">Total Users: {{ $userCount }}</p>
                </div>

            </div>

    </div>
</div>





