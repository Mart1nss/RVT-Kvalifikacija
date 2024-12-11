@include('navbar')

<link rel="stylesheet" href="{{ asset('css/categorymanage-style.css') }}">

<div class="main-container">
<div class="category-container">
    <h1 style="margin-bottom: 20px; font-family: sans-serif; font-weight: 800; text-transform: uppercase; font-size: 32px;">Manage Categories</h1>

    @if(session('success'))
        <div class="alert alert-success" id="success-alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" id="error-alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="category-form">
        <h2>Add New Category</h2>
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div>
                <input type="text" name="name" placeholder="Category Name" required>
            </div>
            <button type="submit" class="btn-category">Add Category</button>
        </form>
    </div>

    <div class="category-list">
        <h2>Existing Categories</h2>
        <div class="search-container">
            <input type="text" 
                   class="search-input" 
                   id="categorySearch" 
                   placeholder="Search categories..."
                   autocomplete="off">
        </div>
        
        <div id="no-results" class="no-results hidden">
            No categories found
        </div>

        @foreach($categories as $category)
            <div class="category-item" data-category-name="{{ strtolower($category->name) }}">
                <div>
                    <h3>{{ $category->name }}</h3>
                </div>
                <div>
                    <button class="btn-category" onclick="toggleEdit({{ $category->id }})">Edit</button>
                    <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-category btn-delete">Delete</button>
                    </form>
                </div>
            </div>

            <div id="edit-form-{{ $category->id }}" style="display: none;" class="category-item" data-category-name="{{ strtolower($category->name) }}">
                <form action="{{ route('categories.update', $category) }}" method="POST" style="width: 100%;">
                    @csrf
                    @method('PUT')
                    <div>
                        <input type="text" name="name" value="{{ $category->name }}" required>
                    </div>
                    <button type="submit" class="btn-category">Update</button>
                    <button type="button" class="btn-category" onclick="toggleEdit({{ $category->id }})">Cancel</button>
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

// Auto-hide alerts after 3 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('fade-out');
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500);
        }, 3000);
    });

    // Category search functionality
    const searchInput = document.getElementById('categorySearch');
    const categoryItems = document.querySelectorAll('.category-item');
    const noResults = document.getElementById('no-results');

    searchInput.addEventListener('input', function() {
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
