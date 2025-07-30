<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BagageConsigneController;

Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [UserController::class, 'showLogin'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.submit');
Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');


Route::get('/overview',     [UserController::class, 'overview'])    ->name('overview');
    Route::get('/analytics',    [UserController::class, 'analytics'])   ->name('analytics');
    Route::get('/chat', [UserController::class, 'chat'])->name('chat');

Route::post('/users', [UserController::class, 'createUser'])->name('users.create');
Route::get('/users', [UserController::class, 'users'])->name('users');    


Route::get('/orders',       [UserController::class, 'orders'])      ->name('orders');
Route::get('/myorders',       [UserController::class, 'myorders'])      ->name('myorders');

    
Route::get('/reservations', [UserController::class, 'reservations'])->name('reservations');
// Affiche la fiche d’une réservation via son ref (QR code)
Route::get('/reservations/ref/{ref}', [BagageConsigneController::class, 'showByRef'])
     ->name('reservations.showByRef');


Route::get('/reservations/ref/{ref}', [BagageConsigneController::class, 'showByRef'])
     ->name('reservations.showByRef');

Route::post('/reservations/{id}/collecter', [BagageConsigneController::class, 'collecterBagage'])->name('collecter.bagage');
