@include('components.alert')
@include('navbar')

<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/categorymanage-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
<link rel="stylesheet" href="{{ asset('css/modal-confirmation-delete.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/mobile-filter-drawer.css') }}">
<style>
  .cat-title-1 {
    margin-bottom: 10px;
    font-family: sans-serif;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 28px;
    @media (max-width: 768px) {
      font-size: 24px;
    }
  }

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
    background: #191919;
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
    background: rgba(0, 0, 0, 0.2);
    padding: 8px;
    border-radius: 4px;
    margin-top: 8px;
  }

  @media (max-width: 768px) {
    .filter-container {
      display: none;
    }

    .search-filter-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
    }

    .search-container {
      flex: 1;
    }

    h2 {
      font-size: 20px;
    }
  }
</style>
@livewireStyles

<div class="main-container">
<h1
      class="cat-title-1">
      Manage Categories
    </h1>
  
    @livewire('category-management')

</div>

@livewireScripts

@stack('scripts')
