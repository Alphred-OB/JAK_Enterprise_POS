<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Activity;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('manager.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string'
        ]);

        $category = Category::create($request->all());

        Activity::log(
            'category_created',
            "Created new category: {$category->name}",
            ['category_id' => $category->id]
        );

        return redirect()->back()->with('success', 'Category created successfully');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string'
        ]);

        $category->update($request->all());

        Activity::log(
            'category_updated',
            "Updated category: {$category->name}",
            ['category_id' => $category->id]
        );

        return redirect()->back()->with('success', 'Category updated successfully');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category with products attached');
        }

        $categoryName = $category->name;
        $category->delete();

        Activity::log(
            'category_deleted',
            "Deleted category: {$categoryName}"
        );

        return redirect()->back()->with('success', 'Category deleted successfully');
    }
}
