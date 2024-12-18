<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();

        
        return view('admin.categories.index', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:categories'
            ]);

            Category::create([
                'name' => $request->name
            ]);
            
            return redirect()->back()->with('success', 'Category created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->with('error', 'A category with this name already exists');
        }
    }

    public function update(Request $request, Category $category)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id
            ]);

            $category->update([
                'name' => $request->name
            ]);
            
            return redirect()->back()->with('success', 'Category updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->with('error', 'A category with this name already exists');
        }
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return redirect()->back()->with('success', 'Category deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unable to delete this category. It may be in use by some books.');
        }
    }
}
