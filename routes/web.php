<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('home');
});

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// User routes (require auth)
Route::middleware([\App\Http\Middleware\AuthMiddleware::class])->group(function () {
    Route::get('/home', [BookingController::class, 'index'])->name('home');
    Route::post('/reserve', [BookingController::class, 'reserve'])->name('reserve');
    Route::get('/my-reservations', [BookingController::class, 'myReservations'])->name('my.reservations');
    Route::post('/pay', [BookingController::class, 'pay'])->name('pay');
    Route::post('/cancel', [BookingController::class, 'cancel'])->name('cancel');
});

// Admin routes (require admin)
Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/reservations', [AdminController::class, 'allReservations'])->name('admin.reservations');
    Route::post('/approve/{id}', [AdminController::class, 'approve'])->name('admin.approve');
    Route::post('/cancel-reservation/{id}', [AdminController::class, 'cancelReservation'])->name('admin.cancel');
    Route::get('/time-slots', [AdminController::class, 'timeSlots'])->name('admin.time-slots');
    Route::post('/create-time-slots', [AdminController::class, 'createTimeSlots'])->name('admin.create-time-slots');
    Route::post('/delete-time-slot/{id}', [AdminController::class, 'deleteTimeSlot'])->name('admin.delete-time-slot');
    Route::post('/update-field', [AdminController::class, 'updateField'])->name('admin.update-field');
    Route::get('/statistics', [AdminController::class, 'statistics'])->name('admin.statistics');

    Route::post('/reservations/{id}/mark-paid', [AdminController::class, 'markAsPaid'])->name('admin.markPaid');
Route::post('/reset/{id}', [AdminController::class, 'reset'])->name('admin.reset');
Route::delete('/reservations/{id}', [AdminController::class, 'destroy'])
    ->name('admin.reservations.destroy');
Route::get('/reservations/{id}/edit', [AdminController::class, 'edit'])
    ->name('admin.reservations.edit');

Route::put('/reservations/{id}', [AdminController::class, 'update'])
    ->name('admin.reservations.update');
Route::put('/reservations/{id}/change-time', 
    [AdminController::class, 'changeTime'])
    ->name('admin.reservations.changeTime');
    Route::post('/block-slots', [AdminController::class, 'blockSlots'])->name('admin.blockSlots');

    //  Route::get('time-slots', [AdminController::class, 'timeSlots'])->name('admin.time-slots');

    // Route::post('time-slots/create', [AdminController::class, 'createTimeSlots'])->name('admin.create-time-slots');

    // Route::post('time-slots/{id}/delete', [AdminController::class, 'deleteTimeSlot'])->name('admin.delete-time-slot');

    // 🔹 هنا خاصنا Route باش نديرو تحديث الوقت
//     Route::put('/time-slots/{id}/update', [AdminController::class, 'updateTimeSlot'])->name('admin.update-time-slot');
//     Route::put('/time-slots/{id}', [AdminController::class, 'updateTimeSlot'])->name('admin.update-time-slot');



//  Route::get('/time-slots', [AdminController::class, 'timeSlots'])->name('admin.time-slots');
//     Route::post('/create-time-slots', [AdminController::class, 'createTimeSlots'])->name('admin.create-time-slots');
//     Route::post('/delete-time-slot/{id}', [AdminController::class, 'deleteTimeSlot'])->name('admin.delete-time-slot');

//     // 🔹 PUT route correct
//     Route::put('/time-slots/{id}/update', [AdminController::class, 'updateTimeSlot'])->name('admin.update-time-slot');



});
Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->group(function () {

    // تعديل موعد
    Route::put('time-slots/{id}', [AdminController::class, 'updateTimeSlot'])->name('admin.update-time-slot');
});

Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/time-slots', [AdminController::class, 'timeSlots'])->name('admin.time-slots');
    Route::post('/create-time-slots', [AdminController::class, 'createTimeSlots'])->name('admin.create-time-slots');
    Route::post('/delete-time-slot/{id}', [AdminController::class, 'deleteTimeSlot'])->name('admin.delete-time-slot');

    // ✅ PUT route correct
    Route::put('/time-slots/{id}/update', [AdminController::class, 'updateTimeSlot'])->name('admin.update-time-slot');
});
// routes/web.php
Route::get('/get-available-slots', [BookingController::class, 'getAvailableSlots'])->name('get.available-slots');
Route::get('/my-reservations', [ReservationController::class, 'myReservations'])->name('client.reservations');
Route::get('/client/reservations', [ReservationController::class, 'myReservations'])
     ->name('client.reservations');

Route::middleware('auth')->group(function() {
    Route::get('/my-reservations', [ReservationController::class, 'reservations'])
        ->name('my.reservations');

});
    Route::get('/reservation/{id}/pdf', [ReservationController::class, 'generatePdf'])
         ->name('reservation.pdf');
