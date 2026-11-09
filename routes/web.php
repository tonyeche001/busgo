<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

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
Route::get('/search', [HomeController::class, 'search'])->name('trips.search');
Route::get('/trips/{trip}/seats', [HomeController::class, 'seats'])->name('trips.seats');
Route::post('/trips/{trip}/book', [HomeController::class, 'book'])->name('trips.book');
Route::get('/booking/confirmation', [HomeController::class, 'confirmation'])->name('bookings.confirmation');
Route::post('/booking/pay', [HomeController::class, 'pay'])->name('bookings.pay');
Route::get('payment/callback', [HomeController::class, 'paymentcallback'])->name('payment.callback');
Route::get('/ticket', [HomeController::class, 'findTicket'])->name('tickets.find');
Route::post('/ticket', [HomeController::class, 'showTicket'])->name('tickets.show');



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
