<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobImageController;
use App\Http\Controllers\ChemicalController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Jobs (accessible by all authenticated)
    Route::resource('jobs', JobController::class);

    // Job Images
    Route::post('/jobs/{job}/images', [JobImageController::class, 'store'])->name('job-images.store');
    Route::delete('/job-images/{jobImage}', [JobImageController::class, 'destroy'])->name('job-images.destroy');

    // Chemicals
    Route::post('/jobs/{job}/chemicals', [ChemicalController::class, 'store'])->name('chemicals.store');
    Route::patch('/chemicals/{chemical}', [ChemicalController::class, 'update'])->name('chemicals.update');
    Route::delete('/chemicals/{chemical}', [ChemicalController::class, 'destroy'])->name('chemicals.destroy');

    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        Route::resource('clients', ClientController::class);
        Route::resource('technicians', TechnicianController::class);

        // Invoices
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::post('/jobs/{job}/invoice', [InvoiceController::class, 'generate'])->name('invoices.generate');
        Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    });
});

require __DIR__.'/auth.php';
