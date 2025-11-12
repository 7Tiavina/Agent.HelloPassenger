<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BagageConsigneController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\ClientController; // Add this import

Route::get('/acceuil', [FrontController::class, 'acceuil'])->name('front.acceuil');

Route::get('/', fn() => redirect()->route('front.acceuil'));
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

Route::post('/reservations/{id}/collecter', [BagageConsigneController::class, 'collecterBagage'])->name('collecter.bagage');







// Dashboard client (protégé)
Route::middleware('auth:client')->group(function () {
    Route::get('/client/dashboard', [FrontController::class, 'clientDashboard'])->name('client.dashboard');
    Route::post('/client/logout', [FrontController::class, 'clientLogout'])->name('client.logout');
});

// Affiche modal/login
Route::get('/client/login', [FrontController::class, 'showClientLogin'])->name('client.login');

// Traitement login client
Route::post('/client/login', [FrontController::class, 'clientLogin'])->name('client.login.submit');
Route::post('/client/register', [FrontController::class, 'clientRegister'])->name('client.register');







// Nouvelles routes pour les appels de l'API BDM via le FrontController
Route::post('/api/check-availability', [FrontController::class, 'checkAvailability'])->name('api.check-availability');
Route::post('/api/get-quote', [FrontController::class, 'getQuote'])->name('api.get-quote');













Route::get('/link-form', [FrontController::class, 'redirectForm'])->name('form-consigne');

Route::get('/check-auth-status', function () {
    return response()->json(['authenticated' => Auth::guard('client')->check()]);
});

Route::middleware('auth:client')->group(function () { // Spécifier la garde 'client'
    Route::get('/mes-reservations', [CommandeController::class, 'index'])->name('mes.reservations');
    Route::post('/client/update-profile', [ClientController::class, 'updateProfile'])->name('client.update-profile'); // Point to ClientController
});

// Routes de paiement publiques
Route::post('/session/update-guest-info', [PaymentController::class, 'updateGuestInfoInSession'])->name('session.updateGuestInfo');
Route::get('/payment', [PaymentController::class, 'showPaymentPage'])->name('payment');
Route::post('/prepare-payment', [PaymentController::class, 'preparePayment'])->name('prepare.payment');

// New routes for Monetico payment
Route::match(['get', 'post'], '/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/error', [PaymentController::class, 'paymentError'])->name('payment.error');
Route::get('/payment/cancel', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');
Route::get('/payment/return', [PaymentController::class, 'paymentReturn'])->name('payment.return');
Route::post('/payment/ipn', [PaymentController::class, 'handleIpn'])->name('payment.ipn');
Route::get('/payment/success/show', [PaymentController::class, 'showPaymentSuccess'])->name('payment.success.show');

Route::get('/commandes/{id}/download-invoice', [CommandeController::class, 'downloadInvoice'])->name('commandes.download-invoice');
Route::get('//commandes/{id}', [CommandeController::class, 'show'])->name('commandes.show');
