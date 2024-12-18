@include('components.alert')
@include('navbar')

<link rel="stylesheet" href="{{ asset('css/categorymanage-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">

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
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete">Delete</button>
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

<script>
    function toggleEdit(categoryId) {
        const editForm = document.getElementById(`edit-form-${categoryId}`);
        editForm.style.display = editForm.style.display === 'none' ? 'block' : 'none';
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