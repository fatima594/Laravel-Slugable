<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Services\Image\ImageService; // استيراد ImageService


class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('category')->paginate(10);
        return view('admin.post.index', compact('posts'));
    }


    public function create()
    {
        $categories = Category::all();
        return view('admin.post.create', compact('categories' ));
    }

    public function store(StorePostRequest $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|min:11',
            'body' => 'required|min:20',
            'category_id' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|',
            'video_url' => 'nullable|url',
        ]);

        // معالجة رفع الصورة
        $imageService = new ImageService();

        // معالجة رفع الصورة
        if ($request->hasFile('image')) {
            $imagePath = $imageService->save($request->file('image')); // استخدام ImageService
            $validatedData['image'] = $imagePath;
        }
//        if ($request->hasFile('image')) {
//            $validatedData['image'] = $request->file('image')->store('posts', 'public');
//        }

        // إنشاء المنشور
             Post::create([
            'title' => $validatedData['title'],
            'body' => $validatedData['body'],
            'image' => $validatedData['image'] ?? null,
            'category_id' => $validatedData['category_id'],
             'video_url' => $validatedData['video_url'] ?? null, // حفظ رابط الفيديو

             ]);



        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully.');
    }
    public function show($slug)
    {
        $post = Post::where('slug', $slug)->with('category')->firstOrFail();
        return view('posts.show', compact('post'));
    }


    public function edit($id)
    {
        $post = Post::findOrFail($id); // استخدم findOrFail مع ID
        $categories = Category::all();
        return view('admin.post.edit', compact('post', 'categories' ));
    }

    public function update(StorePostRequest $request, $id)
    {
        $post = Post::findOrFail($id); // استخدم findOrFail مع ID

        $validatedData = $request->validated();

        // معالجة رفع الصورة
//        if ($request->hasFile('image')) {
//            if ($post->image) {
//                Storage::disk('public')->delete($post->image);
//            }
//            $validatedData['image'] = $request->file('image')->store('posts', 'public'); // تخزين الصورة
//        }
        // إنشاء مثيل من ImageService
        $imageService = new ImageService();

        // معالجة رفع الصورة
        if ($request->hasFile('image')) {
            if ($post->image) {
                $imageService->deleteImage(public_path($post->image)); // استخدام ImageService لحذف الصورة القديمة
            }
            $imagePath = $imageService->save($request->file('image')); // استخدام ImageService
            $validatedData['image'] = $imagePath;
        }
        $validatedData['video_url'] = $request->input('video_url', $post->video_url); // تحديث رابط الفيديو


        $post->update($validatedData);


        return redirect()->route('admin.posts.index')->with('success', 'Post updated.');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id); // استخدم findOrFail مع ID

        // Delete the image if it exists
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        // Delete the blog post
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully.');
    }
}
