<?php

use App\Http\Controllers\AccountsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JournalsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Accounts Routes
    Route::resource('accounts', AccountsController::class);
    Route::get('accounts/{account}/hierarchy', [AccountsController::class, 'hierarchy'])->name('accounts.hierarchy');
    
    // Journals Routes
    Route::resource('journals', JournalsController::class);
    Route::post('journals/{journal}/post', [JournalsController::class, 'post'])->name('journals.post');
    
    // Reports Routes
    Route::prefix('reports')->group(function () {
        Route::get('trial-balance', [ReportController::class, 'trialBalance'])->name('reports.trial-balance');
        Route::get('balance-sheet', [ReportController::class, 'balanceSheet'])->name('reports.balance-sheet');
        Route::get('income-statement', [ReportController::class, 'incomeStatement'])->name('reports.income-statement');
    });
});

require __DIR__.'/auth.php';