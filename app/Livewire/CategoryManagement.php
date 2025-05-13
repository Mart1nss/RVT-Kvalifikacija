<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogService;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\DB;

class CategoryManagement extends Component
{
    use WithPagination;

    #[Rule('required|string|max:30|unique:categories,name')]
    public $name = '';

    public $search = '';
    public $status = 'all';
    public $sort = 'newest';
    public $editingCategoryId = null;
    public $editingCategoryName = '';
    public $showDeleteModal = false;
    public $categoryToDelete = null;
    #[Rule('nullable|exists:categories,id')]
    public $selectedNewCategoryId = null;
    public $visibility = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'all'],
        'sort' => ['except' => 'newest'],
        'visibility' => ['except' => 'all']
    ];

    protected $rules = [
        'name' => 'required|string|max:30|unique:categories',
        'editingCategoryName' => 'required|string|max:30'
    ];

    protected $messages = [
        'name.required' => 'Category name is required.',
        'name.max' => 'Category name cannot exceed 30 characters.',
        'name.unique' => 'This category name already exists.',
        'editingCategoryName.required' => 'Category name is required.',
        'editingCategoryName.max' => 'Category name cannot exceed 30 characters.',
        'selectedNewCategoryId.required' => 'Please select a category for reassigning books.'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function store()
    {
        try {
            $validated = $this->validate([
                'name' => 'required|string|max:30|unique:categories,name'
            ]);

            $category = Category::create([
                'name' => $this->name,
                'is_public' => true
            ]);

            AuditLogService::log(
                "Created category",
                "category",
                "Created new category",
                $category->id,
                $category->name
            );

            $this->name = '';
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Category created successfully.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Failed to create category. Please try again.'
            ]);
        }
    }

    public function startEditing($categoryId)
    {
        $category = Category::find($categoryId);
        $this->editingCategoryId = $categoryId;
        $this->editingCategoryName = $category->name;
    }

    public function cancelEditing()
    {
        $this->editingCategoryId = null;
        $this->editingCategoryName = '';
        $this->resetValidation('editingCategoryName');
    }

    public function updateCategory()
    {
        $this->validate([
            'editingCategoryName' => 'required|string|max:30|unique:categories,name,' . $this->editingCategoryId
        ]);

        $category = Category::find($this->editingCategoryId);
        $oldName = $category->name;

        $category->update([
            'name' => $this->editingCategoryName
        ]);

        AuditLogService::log(
            "Updated category",
            "category",
            "Updated category from '{$oldName}' to '{$category->name}'",
            $category->id,
            $category->name
        );

        $this->editingCategoryId = null;
        $this->editingCategoryName = '';
        $this->dispatch('alert', [
            'type' => 'success',
            'message' => 'Category updated successfully.'
        ]);
    }

    public function confirmDelete($categoryId)
    {
        $this->reset(['selectedNewCategoryId']);
        $this->categoryToDelete = Category::withCount('products')
            ->with('products')
            ->findOrFail($categoryId);
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->categoryToDelete = null;
        $this->selectedNewCategoryId = null;
    }

    public function toggleVisibility($categoryId)
    {
        $category = Category::findOrFail($categoryId);

        if ($category->is_system) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'System categories cannot be modified.'
            ]);
            return;
        }

        $isBecomingPrivate = $category->is_public;
        
        $category->update([
            'is_public' => !$category->is_public
        ]);

        AuditLogService::log(
            "Updated category visibility",
            "category",
            "Changed visibility to " . ($category->is_public ? 'public' : 'private'),
            $category->id,
            $category->name
        );

        // If the category is being made private, we should clean up user preferences
        if ($isBecomingPrivate) {
            // Remove this category from user preferences
            $affectedUsers = \App\Models\UserPreference::where('category_id', $categoryId)->count();
            \App\Models\UserPreference::where('category_id', $categoryId)->delete();
            
            if ($affectedUsers > 0) {
                AuditLogService::log(
                    "Removed private category from preferences",
                    "category",
                    "Removed from {$affectedUsers} users' preferences after making private",
                    $category->id,
                    $category->name
                );
            }
        }

        $this->dispatch('alert', [
            'type' => 'success',
            'message' => 'Category visibility updated successfully.'
        ]);
    }

    public function getAvailableCategories()
    {
        return Category::where(function ($query) {
            $query->where('id', '!=', $this->categoryToDelete?->id)
                ->where('is_system', false);
        })
            ->orWhere('name', 'Uncategorized')
            ->orderBy('name')
            ->get();
    }

    public function updatedSelectedNewCategoryId($value)
    {
        if ($value === '') {
            $this->selectedNewCategoryId = null;
        } else {
            $this->selectedNewCategoryId = (int) $value;

            if ($this->categoryToDelete) {
                $this->categoryToDelete = Category::withCount('products')
                    ->with('products')
                    ->findOrFail($this->categoryToDelete->id);
            }
        }
    }

    public function deleteCategory()
    {
        if (!$this->categoryToDelete) {
            return;
        }

        if ($this->categoryToDelete->is_system) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'System categories cannot be deleted.'
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            // Refresh the category to ensure we have the latest data
            $this->categoryToDelete = Category::withCount('products')
                ->with('products')
                ->findOrFail($this->categoryToDelete->id);

            $categoryName = $this->categoryToDelete->name;
            $categoryId = $this->categoryToDelete->id;
            $hasBooks = $this->categoryToDelete->products_count > 0;

            // Step 1: Validate and prepare for book reassignment if needed
            if ($hasBooks) {
                if (!$this->selectedNewCategoryId) {
                    $this->dispatch('alert', [
                        'type' => 'error',
                        'message' => 'Please select a category for reassigning books.'
                    ]);
                    DB::rollBack();
                    return;
                }

                $newCategory = Category::find($this->selectedNewCategoryId);
                if (!$newCategory) {
                    $this->dispatch('alert', [
                        'type' => 'error',
                        'message' => 'Selected category for reassignment not found.'
                    ]);
                    DB::rollBack();
                    return;
                }

                if ($newCategory->id === $this->categoryToDelete->id) {
                    $this->dispatch('alert', [
                        'type' => 'error',
                        'message' => 'Cannot reassign books to the category being deleted.'
                    ]);
                    DB::rollBack();
                    return;
                }

                // Step 2: Reassign books
                $updatedCount = DB::table('products')
                    ->where('category_id', $categoryId)
                    ->update([
                        'category_id' => $this->selectedNewCategoryId,
                        'updated_at' => now()
                    ]);

                if ($updatedCount === 0 && $this->categoryToDelete->products_count > 0) {
                    throw new \Exception("Failed to reassign books. Expected to move {$this->categoryToDelete->products_count} books.");
                }

                // Log the reassignment
                AuditLogService::log(
                    "Reassigned category books",
                    "category",
                    "Reassigned {$updatedCount} books from '{$categoryName}' to '{$newCategory->name}'",
                    $categoryId,
                    $categoryName
                );
            }

            // Step 3: Delete the category
            if (!$this->categoryToDelete->delete()) {
                throw new \Exception('Failed to delete the category.');
            }

            DB::commit();

            $this->showDeleteModal = false;
            $this->categoryToDelete = null;
            $this->selectedNewCategoryId = null;

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => $hasBooks
                    ? "Category '{$categoryName}' deleted and {$updatedCount} books reassigned successfully."
                    : "Category '{$categoryName}' deleted successfully."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status = 'all';
        $this->sort = 'newest';
        $this->visibility = 'all';
        $this->resetPage();
    }

    public function getStatusText()
    {
        return match ($this->status) {
            'assigned' => 'Assigned Books',
            'not-assigned' => 'Not Assigned Books',
            default => 'All Categories'
        };
    }

    public function getVisibilityText()
    {
        return match ($this->visibility) {
            'public' => 'Public Categories',
            'private' => 'Private Categories',
            default => 'All Visibility'
        };
    }

    public function getSortText()
    {
        return match ($this->sort) {
            'oldest' => 'Oldest First',
            'count_asc' => 'Book Count (Low to High)',
            'count_desc' => 'Book Count (High to Low)',
            default => 'Newest First'
        };
    }

    public function render()
    {
        $query = Category::withCount('products');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->status !== 'all') {
            switch ($this->status) {
                case 'assigned':
                    $query->has('products');
                    break;
                case 'not-assigned':
                    $query->doesntHave('products');
                    break;
            }
        }

        if ($this->visibility !== 'all') {
            $query->where('is_public', $this->visibility === 'public');
        }

        switch ($this->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'count_asc':
                $query->orderBy('products_count', 'asc');
                break;
            case 'count_desc':
                $query->orderBy('products_count', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $categories = $query->paginate(10);

        return view('livewire.category-management', [
            'categories' => $categories,
            'hasActiveFilters' => $this->search || $this->status !== 'all' || $this->sort !== 'newest' || $this->visibility !== 'all',
            'totalCategories' => $categories->total(),
            'availableCategories' => $this->showDeleteModal ? $this->getAvailableCategories() : collect()
        ]);
    }
}