<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostWebController;

Route::get('/', function () {
    return redirect('/posts');
});

Route::get('/posts', [PostWebController::class, 'index'])->name('posts.index');
Route::post('/posts', [PostWebController::class, 'store'])->name('posts.store');
Route::put('/posts/{id}', [PostWebController::class, 'update'])->name('posts.update');
Route::delete('/posts/{id}', [PostWebController::class, 'destroy'])->name('posts.destroy');

Route::get('/posts/trash/list', [PostWebController::class, 'trashList'])->name('posts.trash');
Route::post('/posts/restore/{id}', [PostWebController::class, 'restore'])->name('posts.restore');
Route::delete('/posts/force/{id}', [PostWebController::class, 'forceDelete'])->name('posts.forceDelete');