<x-guest-layout>
   

    

    @if (!auth()->user()->hasVerifiedEmail())
        <div style="background-color: rgba(255, 152, 0, 0.1); border-radius: 10px; padding: 20px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                <i class='bx bxs-error-circle' style="color: #ff9800; font-size: 24px; margin-right: 10px;"></i>
                <h2 style="color: #ff9800; margin: 0; font-size: 18px;">Email Verification Required</h2>
            </div>
            
            <p style="color: #ff9800; margin-bottom: 15px; font-size: 14px;">
                Please verify your email address.
            </p>

            <form method="POST" action="{{ route('verification.send') }}" style="width: 100%;">
                @csrf
                <button type="submit" class="btn-primary" 
                    style="background: transparent; border: 1px solid #ff9800; color: #ff9800; width: max-content; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class='bx bxs-envelope'></i>
                    Send Verification Link
                </button>
            </form>
        </div>
    @else
        <div class="mt-4 text-green-600" style="margin-bottom: 10px; color: green;">
            {{ __('Your email is verified.') }}
        </div>
    @endif
</x-guest-layout>
