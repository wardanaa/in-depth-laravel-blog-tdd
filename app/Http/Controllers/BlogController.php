<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Http\Requests\BlogStoreRequest;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('index', 'show');
    }

    public function index()
    {
        $blogs     = Blog::published()->get();
        return view('blog.index', compact('blogs'));
    }

    public function create()
    {
        $tags  = Tag::all();
        return view('blog.create', compact('tags'));
    }

    public function show($blog)
    {
        $blog  = Blog::where('slug', $blog)->published()->firstOrFail();
        return view('blog.show', compact('blog'));
    }

    public function store(BlogStoreRequest $request)
    {
        Blog::store($request);
        return redirect('/blog');
    }

    public function destroy(Blog $blog)
    {
        $this->authorize('delete', $blog);
        $blog->deleteImage($blog->image);
        $blog->delete();
        return redirect('/blog');
    }

    public function edit(Blog $blog)
    {
        $tags  = Tag::all();
        return view('blog.edit', compact('blog', 'tags'));
    }

    public function update(Request $request, Blog $blog)
    {
        $this->authorize('update', $blog);
        $blog->edit($request);
        return redirect('blog');
    }

    public function all()
    {
        $blogs = auth()->user()->blogs;
        return view('blog.all', compact('blogs'));
    }
}
