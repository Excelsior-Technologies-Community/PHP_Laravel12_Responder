<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;

/*
|--------------------------------------------------------------------------
| Post API Routes (GET & POST Only)
|--------------------------------------------------------------------------
*/

Route::prefix('posts')->group(function () {

    // Get all posts
    Route::get('/', [PostController::class, 'getPosts']);

    // Create post
    Route::post('/create', [PostController::class, 'createPost']);

    // Show single post by ID
    Route::get('/show/{id}', [PostController::class, 'showPost']);

    // Update post by ID (POST method)
    Route::post('/update/{id}', [PostController::class, 'updatePost']);

    // Delete post by ID (POST method)
    Route::post('/delete/{id}', [PostController::class, 'deletePost']);

});