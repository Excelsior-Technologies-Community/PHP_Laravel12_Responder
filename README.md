# PHP_Laravel12_Responder

##  Project Introduction

PHP_Laravel12_Responder is a Laravel 12 REST-style API project that demonstrates how to build structured and consistent JSON responses using the flugger/laravel-responder package.

The project implements a complete CRUD API for a Post resource and uses Transformers to control and format API output professionally.

This project is designed for:

- Learning API development in Laravel 12

- Understanding Transformers

- Implementing clean JSON response structures

- Practicing modern Laravel architecture

------------------------------------------------------------------------

## Project Overview

PHP_Laravel12_Responder is a Laravel 12 API project built to demonstrate how to create structured, consistent, and professional JSON responses using the flugger/laravel-responder package.

This project implements a complete CRUD (Create, Read, Update, Delete) API for a Post resource and uses Transformers to control and format API output cleanly. Instead of returning raw model data, the application formats responses using a standardized structure:

```
{
  "success": true,
  "data": { ... }
}
```

------------------------------------------------------------------------

## Technologies Used

- PHP 8+

- Laravel 12

- MySQL

- flugger/laravel-responder

- Postman (for API testing)

------------------------------------------------------------------------

##  Step 1: Create Laravel 12 Project

Open terminal and run:

``` bash
composer create-project laravel/laravel PHP_Laravel12_Responder "12.*"
```

Then move inside the project:

``` bash
cd PHP_Laravel12_Responder
```

Check Laravel version:

``` bash
php artisan --version
```

------------------------------------------------------------------------

##  Step 2: Setup Database

Update `.env` file:

``` env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_responder
DB_USERNAME=root
DB_PASSWORD=
```

Run migration test:

``` bash
php artisan migrate
```

------------------------------------------------------------------------

##  Step 3: Install Laravel Responder Package

Install the package:

``` bash
composer require flugger/laravel-responder
```

Publish configuration (optional):

``` bash
php artisan vendor:publish --provider="Flugger\\Responder\\ResponderServiceProvider"
```

------------------------------------------------------------------------

##  Step 4: Create Model & Migration

We will create a Post API.

``` bash
php artisan make:model Post -m
```

Update migration file:

``` php
public function up(): void
{
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description');
        $table->timestamps();
    });
}
```

Run migration:

``` bash
php artisan migrate
```

------------------------------------------------------------------------

## Step 5: Update Post Model

Open:

app/Models/Post.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'description',
    ];
}
```

------------------------------------------------------------------------

## Step 6: Create Transformer

```bash
php artisan make:transformer PostTransformer
```

Open:

app/Transformers/PostTransformer.php

Add:

```php
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
```
------------------------------------------------------------------------

##  Step 7: Create Controller

``` bash
php artisan make:controller Api/PostController --api
```

Open:

app/Http/Controllers/Api/PostController.php


Update Controller:

``` php
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
```

------------------------------------------------------------------------

##  Step 8: Define API Routes

Update `routes/api.php`:

``` php
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
```

------------------------------------------------------------------------

## Step 9: Configure bootstrap/app.php

Open:

bootstrap/app.php

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // add api
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

------------------------------------------------------------------------

## Step 10: Test API

Start server:

``` bash
php artisan serve
```

Test endpoints:

| Method | URL                   |
| ------ | --------------------- |
| GET    | `/api/posts`          |
| POST   | `/api/posts/create`   |
| GET    | `/api/posts/show/1`   |
| POST   | `/api/posts/update/1` |
| POST   | `/api/posts/delete/1` |


Your base URL will be:

```bash
http://127.0.0.1:8000
```

All API routes will be:

```bash
http://127.0.0.1:8000/api/...
```

### 1. Create Post

Endpoint

```
POST http://127.0.0.1:8000/api/posts/create
```

Postman Setup

- Method: POST

- Go to Body

- Select raw

- Select JSON

JSON Body

```
{
  "title": "Laravel 12 Responder",
  "description": "Testing API using Postman"
}
```

Expected Response

```
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Laravel 12 Responder",
    "description": "Testing API using Postman"
    "created_at": "2026-02-24 10:15:30"
  }
}
```

### 2. GET All Posts

Endpoint

```
GET http://127.0.0.1:8000/api/posts
```

Postman Setup

- Method: GET

No body required


Expected Response

```
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Laravel 12 Responder",
      "description": "Testing API using Postman"
      "created_at": "2026-02-24 10:15:30"
    }
  ]
}
```

### 3. Show Single Post

Endpoint

```
GET http://127.0.0.1:8000/api/posts/show/1
```
Replace 1 with any existing ID.

Expected Response

```
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Laravel 12 Responder",
    "description": "Testing API using Postman"
    "created_at": "2026-02-24 10:15:30"
  }
}
```

### 4. Update Post

Endpoint

```
POST http://127.0.0.1:8000/api/posts/update/1
```

Body → raw → JSON

```
{
  "title": "Updated Title"
}
```

Expected Response

```
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Updated Title",
    "description": "Testing API using Postman"
    "created_at": "2026-02-24 10:15:30"
  }
}
```

### 5. Delete Post

Endpoint

```
POST http://127.0.0.1:8000/api/posts/delete/1
```

Expected Response

```
{
  "success": true,
  "data": {
    "message": "Post deleted successfully"
  }
}
```

------------------------------------------------------------------------

## Output

### Create Post 

<img width="1380" height="1005" alt="Screenshot 2026-02-24 124831" src="https://github.com/user-attachments/assets/786a19ff-74a2-4848-9a64-176cf3458e79" />

### Show Post 

<img width="1364" height="1008" alt="Screenshot 2026-02-24 125057" src="https://github.com/user-attachments/assets/f74a3665-ca5b-4651-bc0c-dfec90d2e04d" />

### Update Post

<img width="1384" height="1006" alt="Screenshot 2026-02-24 125209" src="https://github.com/user-attachments/assets/8f3c5361-0abe-4bd7-9f39-3df1065437d7" />

### Delete Post

<img width="1376" height="998" alt="Screenshot 2026-02-24 125320" src="https://github.com/user-attachments/assets/0c22b1eb-db61-4e81-8eae-5f7f0d2700a5" />

------------------------------------------------------------------------

## Project Structure

```
PHP_Laravel12_Responder
│
├── app
│   ├── Http
│   │   └── Controllers
│   │       └── Api
│   │           └── PostController.php
│   │
│   ├── Models
│   │   └── Post.php
│   │
│   └── Transformers
│       └── PostTransformer.php
│
├── bootstrap
│   └── app.php
│
├── database
│   └── migrations
│       └── 2026_02_24_000000_create_posts_table.php
│
├── routes
│   └── api.php
│
├── config
│   └── responder.php  // (Optional - only if configuration is published)
│
├── .env
│
└── README.md
```
------------------------------------------------------------------------

Your PHP_Laravel12_Responder Project is now ready!
