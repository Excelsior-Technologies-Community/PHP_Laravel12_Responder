<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;

/*
|--------------------------------------------------------------------------
| POSTS API ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('posts')->group(function () {

    // GET ALL (SEARCH + PAGINATION)
    Route::get('/', [PostController::class, 'index']);

    // CREATE
    Route::post('/', [PostController::class, 'store']);

    // SINGLE POST
    Route::get('/{id}', [PostController::class, 'show']);

    // UPDATE (✔ correct REST method)
    Route::put('/{id}', [PostController::class, 'update']);

    // DELETE (SOFT DELETE)
    Route::delete('/{id}', [PostController::class, 'destroy']);

    // TRASH LIST
    Route::get('/trash/list', [PostController::class, 'trash']);

    // RESTORE
    Route::post('/restore/{id}', [PostController::class, 'restore']);

    // FORCE DELETE
    Route::delete('/force/{id}', [PostController::class, 'forceDelete']);

});