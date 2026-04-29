<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Transformers\PostTransformer;

class PostController extends Controller
{
    // =========================
    // GET POSTS (SEARCH + PAGINATION)
    // =========================
    public function index(Request $request)
    {
        $query = Post::query();

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
        }

        $posts = $query->orderBy('id', 'desc')->paginate(5);

        return responder()
            ->success($posts, PostTransformer::class)
            ->respond();
    }

    // CREATE POST
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'required'
        ]);

        $post = Post::create($data);

        return responder()
            ->success($post, PostTransformer::class)
            ->respond();
    }

    // SHOW SINGLE
    public function show($id)
    {
        $post = Post::findOrFail($id);

        return responder()
            ->success($post, PostTransformer::class)
            ->respond();
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $post->update($request->only(['title', 'description']));

        return responder()
            ->success($post, PostTransformer::class)
            ->respond();
    }

    // SOFT DELETE (TRASH)
    public function destroy($id)
    {
        Post::findOrFail($id)->delete();

        return responder()
            ->success(['message' => 'Moved to trash'])
            ->respond();
    }

    // TRASH LIST
    public function trash()
    {
        $posts = Post::onlyTrashed()->paginate(5);

        return responder()
            ->success($posts, PostTransformer::class)
            ->respond();
    }

    // RESTORE
    public function restore($id)
    {
        Post::onlyTrashed()->findOrFail($id)->restore();

        return responder()
            ->success(['message' => 'Restored successfully'])
            ->respond();
    }

    // FORCE DELETE
    public function forceDelete($id)
    {
        Post::onlyTrashed()->findOrFail($id)->forceDelete();

        return responder()
            ->success(['message' => 'Permanently deleted'])
            ->respond();
    }
}