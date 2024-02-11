<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/users/', [UserController::class, 'index'])->name('user.index');
    Route::post('/users/', [UserController::class, 'store'])->name('user.store');
    Route::get('/users/{id}/show', [UserController::class, 'show'])->name('user.show');
    Route::patch('/users/{id}/restore', [UserController::class, 'restore'])->name('user.restore');
    Route::delete('/users/{id}/delete', [UserController::class, 'delete'])->name('user.delete');
    Route::get('/users/trashed', [UserController::class, 'trashed'])->name('user.trashed');
});

Route::middleware('auth')->group(function () {
    Route::get('/user', [UserController::class, 'edit'])->name('user.edit');
    Route::patch('/user', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user', [UserController::class, 'destroy'])->name('user.destroy');
});

require __DIR__.'/auth.php';
