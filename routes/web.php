<?php

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

use App\Http\Controllers\ClientController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('clients', ClientController::class);
    Route::resource('loans', LoanController::class);

    // Payment routes
    Route::get('/loans/{loan}/contract', [LoanController::class, 'contract'])->name('loans.contract');
    Route::post('/loans/{loan}/amortize', [LoanController::class, 'amortize'])->name('loans.amortize');
    Route::post('/payments/{amortization}/pay', [PaymentController::class, 'pay'])->name('payments.pay');
    Route::post('/payments/{amortization}/unpay', [PaymentController::class, 'unpay'])->name('payments.unpay');
});
