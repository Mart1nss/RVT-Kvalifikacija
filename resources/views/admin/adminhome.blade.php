@include('navbar')

<link rel="stylesheet" href="{{ asset('css/adminhome-style.css') }}">


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





