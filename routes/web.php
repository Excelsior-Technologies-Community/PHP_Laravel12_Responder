<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostWebController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/posts', [PostWebController::class, 'index']);