<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
</head>

<body>

    @include('navbar')

     <!-- Floating Alert -->
     <div class="alert-container" id="alertContainer">
        <div class="alert alert-success">
            Notification sent successfully!
        </div>
    </div>


    <div class="main-container">
        <div class="text-container">
            <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800;">Send
                Notification</h1>
        </div>

        <div class="item-container">
            <div class="filter-div">
                @if(session('success'))
                    <div class="alert alert-success" style="color: green; margin-bottom: 15px;">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.send.notification') }}" class="notification-form" id="notificationForm">
                    @csrf
                    <input type="text" class="notif-input" name="message" placeholder="Enter your notification message"
                        required>
                    <button class="send-btn" type="submit">Send</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('notificationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const alertContainer = document.getElementById('alertContainer');
            
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Show the alert
                alertContainer.style.display = 'block';
                
                // Clear the form
                form.reset();
                
                // Hide the alert after 3 seconds
                setTimeout(() => {
                    alertContainer.style.display = 'none';
                }, 3000);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>


</body>

</html>