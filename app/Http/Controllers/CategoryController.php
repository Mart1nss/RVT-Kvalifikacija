<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\AuditLogService;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();

        // Debug the categories collection
        \Log::info('Categories with counts:', $categories->toArray());

        return view('admin.categories.index', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories'
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

        return redirect()->back()->with('success', 'Category created successfully');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id
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

        return redirect()->back()->with('success', 'Category updated successfully');
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
            return redirect()->back()->with('success', 'Category deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unable to delete this category. It may be in use by some books.');
        }
    }
}
