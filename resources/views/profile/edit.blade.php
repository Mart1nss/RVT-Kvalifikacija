<title>Settings</title>

@include('components.alert')
@include('navbar')

<style>
  #dropdown-2 {
    background-color: rgb(56, 56, 56);
  }

  .main-container {
    max-width: 1000px;
  }

  .item-container {
    background-color: transparent;
    border-bottom-right-radius: 8px;
    border-bottom-left-radius: 8px;
    padding: 0;
  }

  .item-card {
    background-color: #191919;
    color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
    height: min-content;
    margin-bottom: 20px;
    font-family: sans-serif;
    font-size: 14px;
    font-weight: 700;
  }

  .two-card-container {
    display: flex;
    flex-direction: row;
    gap: 16px;
    width: 100%;
    margin-bottom: 20px;
  }

  .item-card-2 {
    width: 50%;
    background-color: #191919;
    color: white;
    padding: 20px;
    border-radius: 8px;
  }

  @media (max-width: 600px) {
    .two-card-container {
      flex-direction: column;
    }
    .item-card-2 {
      width: 100%;
    }
  }

  #input-label {
    color: white;
  }

  input {
    width: 100%;
    height: 40px;
    background-color: #252525;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    outline: transparent;
    color: white;
    padding-left: 10px;
    margin: 5 0px;
  }

  input:focus {
    outline: 1px solid white;
    border-color: #4a4a4a;
    background-color: #2a2a2a;
}

.save-btn {
    display: inline-flex;
    border: 1px solid rgb(0, 0, 0);
    background-color: rgb(255, 255, 255);
    color: rgb(0, 0, 0);
    padding: 10px;
    border-radius: 8px;
    font-weight: 800;
    font-size: 12px;
    text-transform: uppercase;
    text-decoration: none;
    transition: all 0.2s;
  }

  .save-btn:hover {
    cursor: pointer;
    opacity: 0.5;
  }

  .validation-errors {
    background-color: rgba(255, 0, 0, 0.1);
    color: rgb(126, 6, 6);
    padding: 10px;
    border-radius: 8px;
    margin-top: 5px;
    font-family: sans-serif;
    font-weight: 800;
    font-size: 12px;
    text-transform: uppercase;
    white-space: normal;
    word-wrap: break-word;
    line-height: 1.4;
    height: 46px;
    list-style-type: none;
    display: block;
  }

  .validation-errors ul {
    list-style-type: none;
    margin: 0;
    padding: 5px;
  }

  .validation-errors li {
    margin-bottom: 5px;
  }

  .error-message {
    color: red;
    font-size: 12px;
    margin-top: 5px;
  }

  h2 {
    margin-bottom: 5px;
  }

  p {
    color: #666;
    text-transform: none;
    margin-bottom: 15px;
    font-size: 16px;
    font-weight: 400;
  }

  .delete-btn {
    width: max-content;
  }

  .saved-text {
    color: green;
    font-size: 14px;
    margin-top: 15px;
    margin-bottom: 0px;
    width: 100%;
    background-color: #072907;
    height: 38px;
    border-radius: 20px;
    padding-left: 20px;
    display: flex;
    padding-top: 8px;
    font-family: sans-serif;
    font-weight: 800;
    font-size: 14px;
    text-transform: uppercase;

  }
</style>

<div class="main-container">

  <div class="text-container" style="background: transparent; padding: 0; margin-bottom: 10px;">
    <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800;">Edit Profile</h1>
  </div>

  <div class="item-container">





    <div class="item-card">
      @include('profile.partials.update-profile-information-form')
    </div>

    <div class="item-card">
      @include('profile.partials.update-password-form')
    </div>

    <div class="two-card-container">

    <div class="item-card-2">
      <div class="space-y-6">
        <div>
          <h2 class="text-lg font-medium text-white">
            Reading Preferences
          </h2>
          <p class="mt-1 text-sm text-gray-300">
            Update your book genre recommendations.
          </p>
        </div>
        <a class="save-btn" href="{{ route('preferences.edit') }}">Update Preferences</a>
      </div>
    </div>

    <div class="item-card-2">
      @include('profile.partials.delete-user-form')
    </div>

    </div>



  </div>

</div>
