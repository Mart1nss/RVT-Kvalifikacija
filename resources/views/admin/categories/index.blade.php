@include('navbar')

<style>
    .category-container {
        background-color: rgb(37, 37, 37);
        border-radius: 10px;
        padding: 20px;
        margin: 20px;
        color: white;
    }

    .category-form {
        margin-bottom: 20px;
    }

    .category-list {
        margin-top: 20px;
    }

    .category-item {
        background-color: rgb(56, 56, 56);
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-category {
        background-color: #4a4a4a;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        margin-left: 10px;
    }

    .btn-category:hover {
        background-color: #5a5a5a;
    }

    .btn-delete {
        background-color: #dc3545;
    }

    .btn-delete:hover {
        background-color: #c82333;
    }

    input[type="text"], textarea {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid #4a4a4a;
        background-color: rgb(56, 56, 56);
        color: white;
    }

    .alert {
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 5px;
        opacity: 1;
        transition: opacity 0.5s ease-in-out;
    }

    .alert-success {
        background-color: #28a745;
        color: white;
    }

    .alert-error {
        background-color: #dc3545;
        color: white;
    }

    .fade-out {
        opacity: 0;
    }

    .search-container {
        margin-bottom: 20px;
    }

    .search-input {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #4a4a4a;
        background-color: rgb(56, 56, 56);
        color: white;
        font-size: 14px;
    }

    .search-input::placeholder {
        color: #999;
    }

    .no-results {
        color: #999;
        text-align: center;
        padding: 20px;
        background-color: rgb(56, 56, 56);
        border-radius: 5px;
        margin-top: 10px;
    }

    .hidden {
        display: none !important;
    }
</style>

<div class="category-container">
    <h1 style="margin-bottom: 20px;">Manage Categories</h1>

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
