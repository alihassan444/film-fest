<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/tickets', [TicketController::class, 'store'])->name('api.tickets.store');
Route::get('/tickets/{ticketId}/status', [TicketController::class, 'status'])->name('api.tickets.status');

// QR Verification routes (with rate limiting)
Route::get('/verify-ticket/{token}', [VerificationController::class, 'verify'])->name('api.verify-ticket');
Route::get('/ticket/{token}', [VerificationController::class, 'show'])->name('api.ticket.show');

// Admin routes (protected)
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('api.admin.dashboard');
    Route::get('/admin/tickets/pending', [AdminController::class, 'pendingTickets'])->name('api.admin.tickets.pending');
    Route::get('/admin/tickets', [AdminController::class, 'tickets'])->name('api.admin.tickets.index');
    Route::post('/admin/tickets/{id}/verify', [AdminController::class, 'verifyTicket'])->name('api.admin.tickets.verify');
    Route::post('/admin/tickets/{id}/reject', [AdminController::class, 'rejectTicket'])->name('api.admin.tickets.reject');
    Route::get('/admin/events', [AdminController::class, 'events'])->name('api.admin.events');
});

