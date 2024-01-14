<?php

use App\Http\Controllers\BooksController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use OpenTelemetry\API\Globals;
use OpenTelemetry\SDK\Trace\TracerProvider;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/books', [BooksController::class, 'index'])->name('books');
Route::get('/books/{id}', [BooksController::class, 'book'])->name('book');
