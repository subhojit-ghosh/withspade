<?php

namespace App\Http\Controllers;

use Corcel\Model\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::published()->where('post_type', 'post')->get();
        return view('blog.index', compact('posts'));
    }

    public function detail(Request $request, $slug)
    {
        $post = Post::published()->where('post_name', $slug)->first();
        if (!$post) {
            abort(404);
        }
        return view('blog.detail', compact('post'));
    }
}
