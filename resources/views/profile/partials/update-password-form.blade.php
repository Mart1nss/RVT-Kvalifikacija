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

    .input-label {
        color: rgb(255, 255, 255);
        margin-bottom: 5px;
    }

    #update_password_current_password {
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

    #update_password_password {
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

    
    #update_password_password_confirmation {
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

    .input-div {
        margin-bottom: 10px;
        display: block;
    }

    .alert-danger {
        color: red;
        font-size: 14px;
        margin-top: 5px;
        width: 100%;
        background-color: #2b0909;
        height: min-content;
        padding-bottom: 8px;
        border-radius: 20px;
        padding-left: 20px;
        padding-top: 8px;
        margin-bottom: 10px;
        font-family: sans-serif;
        font-weight: 800;
        font-size: 14px;
        text-transform: uppercase;
}

</style>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="input-div">
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="alert-danger" />
        </div>

        <div class="input-div">
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="alert-danger" />
        </div>

        <div class="input-div">
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="alert-danger" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="save-btn">{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
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
