@include('components.alert')
@include('navbar')

<link rel="stylesheet" href="{{ asset('css/categorymanage-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/confirmation-modal-style.css') }}">
<style>
    .book-count {
        font-size: 0.8em;
        color: #666;
        font-weight: normal;
        margin-left: 8px;
    }
</style>

<div class="main-container">
    <div class="category-container">
        <h1
            style="margin-bottom: 20px; font-family: sans-serif; font-weight: 800; text-transform: uppercase; font-size: 32px;">
            Manage Categories</h1>

        <div class="category-form">
            <h2>Add New Category</h2>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div>
                    <input type="text" name="name" placeholder="Category Name" required>
                </div>
                <button type="submit" class="btn-category-primary">Add Category</button>
            </form>
        </div>

        <div class="category-list">
            <h2>Existing Categories</h2>
            <div class="search-container">
                <input type="text" class="search-input" id="categorySearch" placeholder="Search categories..."
                    autocomplete="off">
            </div>

            <div id="no-results" class="no-results hidden">
                No categories found
            </div>

            @foreach($categories as $category)
                <div class="category-item" data-category-name="{{ strtolower($category->name) }}">
                    <div>
                        <h3>
                            {{ $category->name }}
                            <span class="book-count">
                                ({{ $category->products_count ?? 'Not Calculated' }} books)
                            </span>
                        </h3>
                    </div>
                    <div>
                        <button class="btn-category-primary" onclick="toggleEdit({{ $category->id }})">Edit</button>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display: inline;" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn-delete" onclick="confirmDelete({{ $category->id }}, '{{ $category->name }}')">Delete</button>
                        </form>
                    </div>
                </div>

                <div id="edit-form-{{ $category->id }}" style="display: none;" class="category-edit"
                    data-category-name="{{ strtolower($category->name) }}">
                    <form action="{{ route('categories.update', $category) }}" method="POST" style="width: 100%;">
                        @csrf
                        @method('PUT')
                        <div>
                            <input type="text" name="name" value="{{ $category->name }}" required>
                        </div>
                        <button type="submit" class="btn-category-primary">Update</button>
                        <button type="button" class="btn-category-secondary"
                            onclick="toggleEdit({{ $category->id }})">Cancel</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Delete Category</h2>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete "<span id="categoryName"></span>" category ?</p>
            <p class="confirmation-text">This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-category-secondary" onclick="closeModal()">Cancel</button>
            <button type="button" class="btn-delete" id="confirmDeleteBtn">Delete</button>
        </div>
    </div>
</div>

<script>
    function toggleEdit(categoryId) {
        const editForm = document.getElementById(`edit-form-${categoryId}`);
        editForm.style.display = editForm.style.display === 'none' ? 'block' : 'none';
    }

    // Modal functionality
    let currentForm = null;

    function confirmDelete(categoryId, categoryName) {
        const modal = document.getElementById('deleteModal');
        const categoryNameSpan = document.getElementById('categoryName');
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        
        currentForm = event.target.closest('form');
        categoryNameSpan.textContent = categoryName;
        modal.style.display = 'block';
        
        confirmBtn.onclick = function() {
            if (currentForm) {
                currentForm.submit();
            }
        }
    }

    function closeModal() {
        const modal = document.getElementById('deleteModal');
        modal.style.display = 'none';
        currentForm = null;
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal) {
            closeModal();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {

        // Category search functionality
        const searchInput = document.getElementById('categorySearch');
        const categoryItems = document.querySelectorAll('.category-item');
        const noResults = document.getElementById('no-results');

        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase().trim();
            let hasResults = false;

            categoryItems.forEach(item => {
                const categoryName = item.getAttribute('data-category-name');
                if (categoryName.includes(searchTerm)) {
                    item.classList.remove('hidden');
                    hasResults = true;
                } else {
                    item.classList.add('hidden');
                }
            });

            noResults.classList.toggle('hidden', hasResults);
        });
    });
</script>