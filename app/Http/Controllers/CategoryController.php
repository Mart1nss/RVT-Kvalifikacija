<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\AuditLogService;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.categories.index', ['categories' => $categories]);
    }

    public function search(Request $request)
    {
        $query = Category::withCount('products');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'assigned':
                    $query->has('products');
                    break;
                case 'not-assigned':
                    $query->doesntHave('products');
                    break;
            }
        }

        // Handle sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
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
        } else {
            // Default sort is newest first
            $query->orderBy('created_at', 'desc');
        }

        $categories = $query->get();

        return response()->json([
            'categories' => $categories,
            'total' => $categories->count()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:30|unique:categories'
        ]);

        $category = Category::create([
            'name' => $request->name
        ]);

        AuditLogService::log(
            "Created category",
            "category",
            "Created new category",
            $category->id,
            $category->name
        );

        // If it's an AJAX request, return JSON response
        if ($request->ajax()) {
            return response()->json([
                'message' => 'Category created successfully',
                'category' => $category->load('products')
            ]);
        }

        // For regular form submission, redirect back with success message
        return redirect()->back()->with('success', 'Category created successfully');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:30|unique:categories,name,' . $category->id
        ]);

        $oldName = $category->name;
        $category->update([
            'name' => $request->name
        ]);

        AuditLogService::log(
            "Updated category",
            "category",
            "Updated category from '{$oldName}' to '{$category->name}'",
            $category->id,
            $category->name
        );

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Category updated successfully',
                'category' => $category->fresh(['products'])
            ]);
        }

        return redirect()->route('categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy(Category $category)
    {
        try {
            AuditLogService::log(
                "Deleted category",
                "category",
                "Deleted category",
                $category->id,
                $category->name
            );

            $category->delete();

            if (request()->ajax()) {
                return response()->json(['message' => 'Category deleted successfully']);
            }

            return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(
                    ['message' => 'Unable to delete this category. It may be in use by some books.'],
                    422
                );
            }

            return redirect()->route('categories.index')
                ->with('error', 'Unable to delete this category. It may be in use by some books.');
        }
    }
}
