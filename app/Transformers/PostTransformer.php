<?php

namespace App\Transformers;

use App\Models\Post;
use Flugg\Responder\Transformers\Transformer;

class PostTransformer extends Transformer
{
    public function transform(Post $post)
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'description' => $post->description,
            'created_at' => $post->created_at->toDateTimeString(),
        ];
    }
}