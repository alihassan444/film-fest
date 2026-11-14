<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\HomeController;
// use App\Http\Controllers\TicketController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Auth::routes();

// Frontend routes
Route::get('/', [FrontendController::class, 'index'])->name('index');
Route::view('/submission', 'submission')->name('submission');
Route::get('/form', [FrontendController::class, 'form'])->name('form');
Route::post('/ticket', [FrontendController::class, 'store'])->name('tickets.store');
Route::get('/attendance', [WelcomeController::class, 'index'])->name('attendance.index');
Route::post('/attendance/lookup', [WelcomeController::class, 'lookupStudent'])->name('student.lookup');

// Admin panel routes (protected)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/attendance', [AdminController::class, 'attendance'])->name('attendance');
    Route::post('/attendance', [AdminController::class, 'markAttendance'])->name('markAttendance');
    Route::post('/notes', [AdminController::class, 'saveNote'])->name('saveNote');
    Route::put('/notes/{note}', [AdminController::class, 'updateNote'])->name('updateNote');
    Route::delete('/notes/{note}', [AdminController::class, 'deleteNote'])->name('deleteNote');
    Route::get('/students', [AdminController::class, 'students'])->name('students.index');
    Route::post('/students', [AdminController::class, 'storeStudent'])->name('storeStudent');
    Route::delete('/students/{student}', [AdminController::class, 'deleteStudent'])->name('deleteStudent');
    Route::get('/tickets/pending', [AdminController::class, 'pendingTickets'])->name('tickets.pending');
    Route::get('/tickets/rejected', [AdminController::class, 'rejectedTickets'])->name('tickets.rejected');
    Route::get('/tickets', [AdminController::class, 'allTickets'])->name('tickets.index');
    Route::post('/tickets/bulk-verify', [AdminController::class, 'bulkVerifyTickets'])->name('tickets.bulkVerify');
    Route::get('/tickets/{ticket_id}', [AdminController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{id}/verify', [AdminController::class, 'verifyTicket'])->name('tickets.verify');
    Route::post('/tickets/{id}/reject', [AdminController::class, 'rejectTicket'])->name('tickets.reject');
    Route::post('/tickets/{id}/resend-email', [AdminController::class, 'resendTicketEmail'])->middleware('throttle:3,1')->name('tickets.resendEmail');
    Route::post('/tickets/{id}/checkin', [AdminController::class, 'checkInTicket'])->name('tickets.checkin');
});

Route::get('/home', [HomeController::class, 'home'])->name('home');
