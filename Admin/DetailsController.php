<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;

class DetailsController extends Controller
{
    public function indexByCategory($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $posts = $category->posts()->paginate(10); // استخدام العلاقة

        return view('layouts.posts', compact('category', 'posts'));
    }



    /**
     * Display a specific blog and its category.
     */
    public function show($slug)
    {
        $post = Post::with(['category', 'comments'])->where('slug', $slug)->firstOrFail();
        $relatedPosts = Post::where('category_id', $post->category_id)
            ->where('slug', '!=', $slug)
            ->limit(3)->take(5)->get()
            ->unique('slug');

        $categories = Category::all();
        $posts = Post::all();

        return view('layouts.single-post', compact('post', 'relatedPosts', 'categories', 'posts'));
    }



//    public function trending()
//    {
//        // جلب أحدث 5 منشورات
//        $trendings = Post::with('category')->latest()->take(5)->get();
//
//        // تمرير المشاركات إلى العرض
//        return view('layouts.trending', compact('trendings'));
//    }




}
