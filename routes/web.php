<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);




Route::get('/dashboard', function() {
    return view('dashboard');
})->middleware('auth:sanctum')->name('dashboard');


Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::get('/user', [AuthController::class, 'userInfo'])->middleware('auth:sanctum');



Route::get('/dashboard', [PostController::class, 'index'])->middleware('auth:sanctum')->name('dashboard');
Route::post('/posts', [PostController::class, 'store'])->middleware('auth:sanctum')->name('posts.store');
Route::delete('/posts/{id}', [PostController::class, 'destroy'])->middleware('auth:sanctum')->name('posts.destroy');


Route::get('/posts/{id}/comments', function ($id) {
    return view('comments', ['postId' => $id]);
})->middleware('auth:sanctum')->name('comments.create');

