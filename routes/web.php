<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [UserController::class, 'showLogin'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.submit');
Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');