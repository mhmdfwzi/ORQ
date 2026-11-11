<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display blog listing
     */
    public function index()
    {
        $posts = Post::published()
            ->with('author')
            ->latest('published_at')
            ->paginate(9);
        
        $featuredPosts = Post::published()
            ->featured()
            ->with('author')
            ->latest('published_at')
            ->take(3)
            ->get();
        
        return view('blog.index', compact('posts', 'featuredPosts'));
    }

    /**
     * Display single post
     */
    public function show($slug)
    {
        $post = Post::published()
            ->where('slug', $slug)
            ->with('author')
            ->firstOrFail();
        
        // Increment views
        $post->incrementViews();
        
        // Related posts (latest from same status)
        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(3)
            ->get();
        
        return view('blog.show', compact('post', 'relatedPosts'));
    }
}
