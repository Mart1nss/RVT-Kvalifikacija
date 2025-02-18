@props(['title' => 'Delete Confirmation'])

<div class="delete-confirmation-modal" x-data="deleteModal" x-show="show" x-cloak
  x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click.self="show = false"
  @open-delete-modal.window="openModal($event.detail)" @keydown.escape.window="show = false">
  <div class="delete-confirmation-content">
    <div class="delete-confirmation-header">
      <h2>{{ $title }}</h2>
    </div>
    <div class="delete-confirmation-body">
      <p>Are you sure you want to delete "<span x-text="itemToDelete?.name"></span>"?</p>
      <p class="delete-confirmation-text">This action cannot be undone.</p>
    </div>
    <div class="delete-confirmation-footer">
      <button type="button" class="btn-category-secondary" @click="show = false">Cancel</button>
      <button type="button" class="btn-delete" @click="confirmDelete">Delete</button>
    </div>
  </div>
</div>
