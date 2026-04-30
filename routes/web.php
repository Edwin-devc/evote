<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VotingController;
use App\Http\Controllers\Admin\ResultPdfController;

Route::middleware(['voting.open'])->group(function () {
    // Login routes
    Route::get('/', [VotingController::class, 'showLoginForm']);
    Route::post('/login', [VotingController::class, 'login'])->name('login');

    // Verification routes
    Route::get('/verify', [VotingController::class, 'showVerificationForm'])->name('verify');
    Route::post('/verify', [VotingController::class, 'verifyCode'])->name('verify.code');

    // Ballot routes
    Route::middleware(['voter.verified'])->group(function () {
        Route::get('/ballot', [VotingController::class, 'showBallot']);
        Route::post('/submit-ballot', [VotingController::class, 'submitBallot'])->name('submit.ballot');
    });
});

// Thank you page
Route::get('/thanks', [VotingController::class, 'showThanks'])->name('thanks');

Route::get('/admin/results/pdf', ResultPdfController::class)
    ->middleware('auth')
    ->name('admin.results.pdf');
