<style>
    .save-btn {
    display: inline-flex;
      border: 1px solid rgb(0, 0, 0);
      background-color: rgb(255, 255, 255);
      color: rgb(0, 0, 0);
      padding: 10px;
      border-radius: 20px;
      font-weight: 800;
      font-size: 12px;
      text-transform: uppercase;
      text-decoration: none;
}

    .save-btn:hover {
        cursor: pointer;
        opacity: 0.7;
    }

    h2 {
        margin-bottom: 5px;
    }

    p {
        color:rgb(128, 128, 128);
        margin-bottom: 15px;
    }

    #input-label {
        color: rgb(255, 255, 255);
        
    }

    .input-text {


    }

    #name {
        width: 100%;
        height: 38px;
        background-color: rgb(37, 37, 37);
        border: none;
        border-radius: 20px;
        font-size: 16px;
        outline: transparent;
        color: white;
        padding-left: 10px;
        margin: 5 0px;
    }

    #email {
        width: 100%;
        height: 38px;
        background-color: rgb(37, 37, 37);
        border: none;
        border-radius: 20px;
        font-size: 16px;
        outline: transparent;
        padding-left: 10px;
        margin: 5 0px;
        color: white;

    }

    #delete-btn {
        color: rgb(255, 0, 0);
        text-decoration: none;
        border: rgb(255, 0, 0) 1px solid;
        border-radius: 20px;
        padding: 10px;
        font-family: sans-serif;
        font-weight: 800;
        font-size: 12px;
        text-transform: uppercase;
        background-color: #1a1a1a;
        cursor: pointer;
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

    .validation-errors {
        background-color: rgba(255, 0, 0, 0.1);
        color: rgb(255, 0, 0);
        padding: 10px;
        border-radius: 10px;
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
</style>


<section>
    <header>

        <h2 class="h2-profile">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label id="input-label"  for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            @error('name')
                <div class="validation-errors">
                    <ul>
                        <li>{{ $message }}</li>
                    </ul>
                </div>
            @enderror
        </div>

        <div>
            <x-input-label id="input-label" for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            @error('email')
                <div class="validation-errors">
                    <ul>
                        <li>{{ $message }}</li>
                    </ul>
                </div>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="save-btn">{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="saved-text"
            >{{ __('Saved.') }}</p>
        @endif

            
        </div>
    </form>
</section>
