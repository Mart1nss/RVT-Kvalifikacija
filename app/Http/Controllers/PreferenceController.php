<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\UserPreference;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    public function show()
    {
        // If user has already made a decision, redirect to home
        if (auth()->user()->has_genre_preference_set) {
            return redirect('/home');
        }

        $categories = Category::where('is_public', true)->get();
        return view('auth.genre-selection', compact('categories'));
    }

    public function store(Request $request)
    {
        $categoriesJson = $request->input('categories');
        $categories = json_decode($categoriesJson);
        
        if (!is_array($categories) || count($categories) !== 3) {
            return back()->with('error', 'Please select exactly 3 categories.');
        }

        // Validate that all selected categories are public
        $publicCategoryIds = Category::where('is_public', true)->pluck('id')->toArray();
        foreach ($categories as $categoryId) {
            if (!in_array($categoryId, $publicCategoryIds)) {
                return back()->with('error', 'One or more selected categories are not available.');
            }
        }

        // Delete existing preferences if any
        UserPreference::where('user_id', auth()->id())->delete();

        // Add new preferences
        foreach ($categories as $categoryId) {
            UserPreference::create([
                'user_id' => auth()->id(),
                'category_id' => $categoryId
            ]);
        }

        // Set the flag
        auth()->user()->update(['has_genre_preference_set' => true]);

        if ($request->input('from') === 'edit') {
            return redirect()->route('preferences.edit')->with('success', 'Reading preferences updated successfully!');
        }

        return redirect('/home');
    }

    public function skip()
    {
        // Set the flag even when skipping
        auth()->user()->update(['has_genre_preference_set' => true]);
        return redirect('/home');
    }

    public function edit()
    {
        $categories = Category::where('is_public', true)->get();
        $selectedCategories = auth()->user()->userPreferences()->pluck('category_id')->toArray();
        return view('auth.genre-selection', compact('categories', 'selectedCategories'));
    }
}
