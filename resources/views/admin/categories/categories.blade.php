@include('components.alert')
@include('navbar')

<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/categorymanage-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
<link rel="stylesheet" href="{{ asset('css/modal-confirmation-delete.css') }}">
<style>
.delete-confirmation-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.delete-confirmation-content {
    background: #202020;
    padding: 16px;
    border-radius: 8px;
    max-width: 500px;
    width: 90%;
}

button:disabled {
    opacity: 0.5;
    cursor: not-allowed !important;
    background-color: #444 !important;
    pointer-events: none;
}

.btn-delete-disabled {
    background-color: #444;
    color: #888;
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    pointer-events: none;
    cursor: not-allowed;
}

.btn-delete {
    background-color: #444;
    color: white;
    transition: all 0.2s ease;
}

.btn-delete:not(:disabled) {
    background-color: rgb(126, 6, 6);
    color: white;
    cursor: pointer;
}

.btn-delete:not(:disabled):hover {
    background-color: rgb(150, 10, 10);
}

.debug-info {
    background: rgba(0,0,0,0.2);
    padding: 8px;
    border-radius: 4px;
    margin-top: 8px;
}

</style>
@livewireStyles

<div class="main-container">
    <div class="category-container">
        <h1 style="margin-bottom: 20px; font-family: sans-serif; font-weight: 800; text-transform: uppercase; font-size: 32px;">
            Manage Categories
        </h1>

        @livewire('category-management')
    </div>
</div>

@livewireScripts
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('alert', (data) => {
            window.dispatchEvent(new CustomEvent('alert', { 
                detail: {
                    type: data[0].type,
                    message: data[0].message
                }
            }));
        });
    });
</script> 