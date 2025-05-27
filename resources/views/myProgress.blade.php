<html lang="en" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Progress</title>
    <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<style>

    .stat-title {
        color: white;
        text-transform: uppercase;
        font-family: sans-serif;
        font-weight: 800;
        margin-bottom: 20px;
    }

.stat-card-container {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 10px;
}

.stat-card {
    background-color: #252525;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 10px;
    flex: 1 0 20%;
    min-width: 200px;
}

.stat-card-header {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    font-size: 12px;
    font-weight: 600;
    color: white;
}

.stat-card-header i {
    font-size: 20px;
    color: black;
    background-color: white;
    border-radius: 50%;
    padding: 5px;
}

.stat-card-body {
    font-size: 20px;
    font-weight: 600;
    color: white;
}


.account-container {
    background-color: #191919;
    border-radius: 8px;
    margin: 10px 0px;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 20px;
}

.account-info-card {
    background-color: #252525;
    border-radius: 8px;
    flex: 1 0 20%;
    min-width: 200px;
    padding: 10px;
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
}

.account-info-card i {
    font-size: 24px;
    color: white;
}

.account-info-card-header {
    font-size: 12px;
    font-weight: 500;
    color: #999;
}

.account-info-card-body {
    font-size: 16px;
    font-weight: 600;
    color: white;
}

.genre-container {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 10px;
}

.genre-card {
    background-color:white;
    border-radius: 20px;
    color: black;
    padding: 10px;
    font-size: 12px;
    min-width: 100px;
    text-align: center;
}

.genre-card h3 {
    margin: 0;
    font-weight: 600;
}

p {
    font-size: 12px;
    color: #999;
    margin-top: 10px;
}

.button-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
    justify-content: center;
    align-items: center;
}


/* Responsive design */
@media (max-width: 900px) {
    .stat-title {
        font-size: 24px;
    }
    .item-title {
        font-size: 20px;
    }
    .stat-card-container {
        gap: 15px;
    }
    .stat-card {
        flex: 1 0 40%;
        min-width: 100px;
        margin-bottom: 5px;
        padding: 15px;
    }
    .stat-card-header {
        font-size: 10px;
    }
    .stat-card-body {
        font-size: 18px;
    }
    .stat-card-header i {
        font-size: 16px;
    }
    .account-container {
        gap: 15px;
    }
    .account-info-card i {
        font-size: 18px;
    }
    .account-info-card-header {
        font-size: 10px;
    }
    .account-info-card-body {
        font-size: 14px;
    }
}

@media (max-width: 600px) {
    .item-container {
        margin: 10px 0px;
    }
    .stat-card-container {
        gap: 10px;
    }
    .item-title {
        font-size: 18px;
    }
    .stat-title {
        font-size: 20px;
    }
    .stat-card {
        flex: 1 0 40%;
        min-width: 80px;
        margin-bottom: 0px;
        padding: 10px;
    }
    .stat-card-header {
        font-size: 8px;
    }
    .stat-card-body {
        font-size: 16px;
    }
    .stat-card-header i {
        font-size: 12px;
    }
    .account-container {
        gap: 10px;
    }
    .account-info-card i {
        font-size: 16px;
    }
    .account-info-card-header {
        font-size: 8px;
    }
    .account-info-card-body {
        font-size: 12px;
    }
}
</style>

<body>

@include('components.alert')
@include('navbar')

<div class="main-container">

    <h1 class="stat-title">My Stats</h1>

    <div class="stat-card-container">

        <div class="stat-card">
            <div class="stat-card-header">
                <h2>Books Read</h2>
                <i class='bx bx-book-open' ></i>
            </div>
            <div class="stat-card-body">
                <h1>{{ $stats['books_read'] }}</h1>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <h2>Favorites</h2>
                <i class='bx bx-star' ></i>
            </div>
            <div class="stat-card-body">
                <h1>{{ $stats['favorites'] }}</h1>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <h2>Notes</h2>
                <i class='bx bx-note' ></i>
            </div>
            <div class="stat-card-body">
                <h1>{{ $stats['notes'] }}</h1>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <h2>Reviews</h2>
                <i class='bx bx-message-dots' ></i>
            </div>
            <div class="stat-card-body">
                <h1>{{ $stats['reviews'] }}</h1>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <h2>Forum Activity</h2>
                <i class='bx bx-message-detail' ></i>
            </div>
            <div class="stat-card-body">
                <h1>{{ $stats['forums'] }}</h1>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <h2>Support Tickets</h2>
                <i class='bx bx-support' ></i>
            </div>
            <div class="stat-card-body">
                <h1>{{ $stats['tickets'] }}</h1>
            </div>
        </div>

    </div>


<div class="item-container" style="margin: 20px 0px; border-radius: 8px;">
<h2 class="item-title">Account & Activity</h2>
    <div class="account-container">
        <div class="account-info-card">
            <i class='bx bx-calendar' ></i>
            <div class="account-info-card-content">
                <div class="account-info-card-header">
                    <h3>Member Since</h3>
                </div>
                <div class="account-info-card-body">
                    <h2>{{ $createdAt->format('M d, Y') }}</h2>
                </div>
            </div>
        </div>
        <div class="account-info-card">
        <i class='bx bxs-hourglass'></i>
            <div class="account-info-card-content">
                <div class="account-info-card-header">
                    <h3>Account Age</h3>
                </div>
                <div class="account-info-card-body">
                    <h2>{{ $accountAge }}</h2>
                </div>
            </div>
        </div>
        <div class="account-info-card">
        <i class='bx bx-time'></i>
            <div class="account-info-card-content"> 
                <div class="account-info-card-header">
                    <h3>Typical Active Time</h3>
                </div>
                <div class="account-info-card-body">
                    <h2 id="active-time">Loading...</h2>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="item-container" style="margin: 20px 0px; border-radius: 8px;">
        <h2 class="item-title" style="margin-bottom: 10px;">Top Genres</h2>
        @if(count($topGenres) > 0)
            <div class="genre-container">
                @foreach($topGenres as $genre)
                    <div class="genre-card">
                        <h3>#{{ $genre['position'] }} {{ $genre['name'] }}</h3>
                        <p style="color: #555; margin-top: 5px; font-size: 11px;">{{ $genre['count'] }} {{ Str::plural('book', $genre['count']) }} read</p>
                    </div>
                @endforeach
            </div>
            <p>Based on your reading history</p>
        @else
            <div style="background-color: #252525; border-radius: 8px; padding: 15px; margin-top: 10px;">
                <p style="font-size: 14px; color: #ddd; margin: 0;">
                    No reading data available yet. Start reading and reviewing books to see your top genres!
                </p>
            </div>
        @endif
    </div>

    <div class="button-container">  
        <p>Last updated: {{ now()->format('Y-m-d') }}</p>
    </div>
    
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Convert UTC hour to local time
    const utcHour = {{ $typicalActiveTimeUTC ?? 'null' }};
    
    if (utcHour === null) {
        document.getElementById('active-time').textContent = 'Not enough data';
    } else {
        // Convert UTC hour to local hour
        const date = new Date();
        date.setUTCHours(utcHour, 0, 0, 0);
        
        // Get local hour and format it
        const localHour = date.getHours();
        const nextHour = (localHour + 1) % 24;
        
        // Format as HH:00 - HH:00
        const formattedHour = String(localHour).padStart(2, '0');
        const formattedNextHour = String(nextHour).padStart(2, '0');
        
        document.getElementById('active-time').textContent = 
            `${formattedHour}:00 - ${formattedNextHour}:00`;
    }
});
</script>

</body>
</html>
