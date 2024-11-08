<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all(); // جلب جميع الفئات
        if (request()->wantsJson()) {
            return response()->json($categories);
        }

        return view('admin.category.index', compact('categories')); // عرض الفئات في العرض
    }



    public function create()
    {
        return view('admin.category.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:8',
        ]);

        Category::create($request->only('name'));

        return redirect()->route('admin.category.index')->with('success', 'Category created successfully.');
    }


    public function show(Category $category)
    {
        return view('admin.category.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id . '|max:255',
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.category.index')->with('success', 'Category updated successfully.');
//        return back();
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.category.index')->with('success', 'Category deleted successfully.');
    }
}


