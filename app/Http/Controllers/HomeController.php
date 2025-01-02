<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::get();

        return Inertia::render('DashboardPage', [
            'posts' => Post::all(), 
            'host' => config('app.url'),
            'action' => route('posts.create'),
            'success' => $request->session()->get('success'),
        ]);

    }

    public function create()
    {
        $storeName = Auth::user()->myshopify_domain;
        return Inertia::render('PostCreate', [
            'storeName'  => $storeName,
            'action' => route('posts.store')
        ]); 
    }
 

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required',
            'status' => 'required|boolean',
        ]);

        Post::create($validated);

        return redirect()->route('dashboard')->with('success', 'Post created successfully.');
    }

    public function edit(Post $post)
    {
        return Inertia::render('PostEdit', [
            'post' => $post,
            'action' => route('posts.update', $post),
            'method' => 'PUT',
        ]);
    }

 
 
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required',
            'status' => 'required|boolean',
        ]);
 
        $post->update($validated); 
        return redirect()->route('dashboard')->with('success', 'Post updated successfully.');
    }
     
    public function show(Post $post)
    { 
        return Inertia::render('PostView', ['post' => $post]);
    }
 
    public function destroy(Post $post)
    {
        $post->delete(); 
        return redirect()->route('dashboard')->with('success', 'Post deleted successfully.');
    }
 
}
