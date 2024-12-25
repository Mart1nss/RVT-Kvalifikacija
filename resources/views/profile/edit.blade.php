<link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
<title>Settings</title>

@include('components.alert')
@include('navbar')

<style>
    #dropdown-2 {
        background-color: rgb(56, 56, 56);
    }

    .item-container {
        background-color:rgb(37, 37, 37); 
        border-bottom-right-radius: 10px;
        border-bottom-left-radius: 10px;
        padding: 16px;
    }

    .item-card {
        background-color: #1c1a1a;
        color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
        border-radius: 10px;
        height: min-content;
        margin-bottom: 20px;
        font-family: sans-serif;
        text-transform: uppercase;
        font-size: 14px;
        font-weight: 700;
    }
</style>

<div class="main-container">

    <div class="text-container">
        <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800;">Edit Profile</h1>
    </div>

    <div class="item-container">

       
          
       

        <div class="item-card">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="item-card">
            @include('profile.partials.update-password-form')
        </div>

        <div class="item-card">
            @include('profile.partials.delete-user-form')
        </div>

        

    </div>

</div>