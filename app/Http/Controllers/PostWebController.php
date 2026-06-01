<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'desc');
        
        if (in_array($sortField, ['id', 'title', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        }

        if ($request->get('status') === 'trash') {
            $query->onlyTrashed();
        }

        $posts = $query->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'posts' => $posts
            ]);
        }

        return view('posts.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $post = Post::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully!',
            'post' => $post
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $post = Post::findOrFail($id);
        $post->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully!',
            'post' => $post
        ]);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post moved to trash successfully!'
        ]);
    }

    public function trashList(Request $request)
    {
        $posts = Post::onlyTrashed()->orderBy('id', 'desc')->get();
        return response()->json([
            'success' => true,
            'posts' => $posts
        ]);
    }

    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->restore();

        return response()->json([
            'success' => true,
            'message' => 'Post restored successfully!'
        ]);
    }

    public function forceDelete($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Post permanently deleted!'
        ]);
    }
}