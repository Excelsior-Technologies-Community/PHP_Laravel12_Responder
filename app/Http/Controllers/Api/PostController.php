<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Transformers\PostTransformer;

class PostController extends Controller
{
    /**
     * Get All Posts
     */
    public function getPosts()
    {
        $posts = Post::all();

        return responder()
            ->success($posts, PostTransformer::class)
            ->respond();
    }

    /**
     * Create Post
     */
    public function createPost(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $post = Post::create($validated);

        return responder()
            ->success($post, PostTransformer::class)
            ->respond();
    }

    /**
     * Show Single Post
     */
    public function showPost($id)
    {
        $post = Post::findOrFail($id);

        return responder()
            ->success($post, PostTransformer::class)
            ->respond();
    }

    /**
     * Update Post
     */
    public function updatePost(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
        ]);

        $post->update($validated);

        return responder()
            ->success($post, PostTransformer::class)
            ->respond();
    }

    /**
     * Delete Post
     */
    public function deletePost($id)
    {
        $post = Post::findOrFail($id);

        $post->delete();

        return responder()
            ->success(['message' => 'Post deleted successfully'])
            ->respond();
    }
}