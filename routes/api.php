<?php

use App\Http\Controllers\TemperatureController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\BooksController;
use App\Jobs\FetchWeatherJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

Route::get('/weather', [TemperatureController::class, 'show']);
Route::get('/temperature', [FetchWeatherJob ::class, 'handle']);

Route::middleware(['auth:sanctum'])->group(function() {

    Route::post('/logout', [UserController::class, 'logout']);


    Route::get('/books', [BooksController::class, 'show']);
    Route::get('/authors', [AuthorsController::class, 'show']);

    Route::get('/authors/filter', [AuthorsController::class, 'filter']);
    Route::get('/books/filter', [BooksController::class, 'filter']);

    Route::get('/authors/{id}', [AuthorsController::class, 'index']);
    Route::get('/books/{id}', [BooksController::class, 'index']);
});


Route::middleware(['auth:sanctum', 'ability:admin'])->group(function() {

    Route::post('/admin/books/', [BooksController::class, 'create']);
    Route::post('/admin/authors/', [AuthorsController::class, 'create']);

    Route::put('/admin/authors/{id}', [AuthorsController::class, 'update']);
    Route::put('/admin/books/{id}', [BooksController::class, 'update']);

    Route::delete('/admin/authors/{id}', [AuthorsController::class, 'destroy']);
    Route::delete('/admin/books/{id}', [BooksController::class, 'destroy']);

    Route::post('/admin/books/delete-many', [BooksController::class, 'delete_many']);
});