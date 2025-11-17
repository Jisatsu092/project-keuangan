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

// Accounts Management
Route::middleware(['auth'])->group(function () {
    
    // Accounts CRUD
    Route::resource('accounts', AccountsController::class);
    
    // Ajax endpoints
    Route::get('/accounts-tree', [AccountsController::class, 'tree'])->name('accounts.tree');
    Route::get('/accounts-units-by-faculty', [AccountsController::class, 'getUnitsByFaculty'])->name('accounts.units');
    Route::post('/accounts-generate-code', [AccountsController::class, 'generateCodePreview'])->name('accounts.generate-code');
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/trial-balance', [ReportController::class, 'trialBalance'])->name('trial-balance');
        Route::get('/lpk', [ReportController::class, 'lpk'])->name('lpk');
        Route::get('/lak', [ReportController::class, 'lak'])->name('lak');
        Route::get('/neraca-saldo', [ReportController::class, 'neracaSaldo'])->name('neraca-saldo');
    });
});

require __DIR__.'/auth.php';