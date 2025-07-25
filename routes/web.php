<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [UserController::class, 'showLogin'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.submit');
Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');


Route::get('/overview',     [UserController::class, 'overview'])    ->name('overview');
    Route::get('/orders',       [UserController::class, 'orders'])      ->name('orders');
    Route::get('/users',        [UserController::class, 'users'])       ->name('users');
    Route::get('/reservations', [UserController::class, 'reservations'])->name('reservations');
    Route::get('/analytics',    [UserController::class, 'analytics'])   ->name('analytics');