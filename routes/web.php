<?php
// routes/web.php

use App\Http\Controllers\AccountsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\JournalsController;
use App\Http\Controllers\FacultiesController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\ActivityTypesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OperationsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

    // ============================================
    // MASTER DATA
    // ============================================
    Route::prefix('master')->name('master.')->group(function () {
        // Faculties
        Route::resource('faculties', FacultiesController::class)->except(['create', 'show', 'edit']);
        Route::resource('operations', OperationsController::class)->except(['create', 'show', 'edit']);

        // Units
        Route::resource('units', UnitsController::class)->except(['create', 'show', 'edit']);

        // Activity Types
        Route::resource('activity-types', ActivityTypesController::class)->except(['create', 'show', 'edit']);
    });

    // ============================================
    // ACCOUNTS
    // ============================================
    Route::resource('accounts', AccountsController::class);

    // Ajax endpoints
    Route::get('/accounts-tree', [AccountsController::class, 'tree'])->name('accounts.tree');
    Route::get('/accounts-units-by-faculty', [AccountsController::class, 'getUnitsByFaculty'])->name('accounts.units');
    Route::post('/accounts-generate-code', [AccountsController::class, 'generateCodePreview'])->name('accounts.generate-code');
    Route::get('/accounts/operations-by-type', [AccountsController::class, 'getOperationsByType']);
    Route::get('/accounts/check-duplicate', [AccountsController::class, 'checkDuplicate']);

    // ============================================
    // JOURNALS
    // ============================================
    Route::resource('journals', JournalsController::class);
    Route::post('/journals/{journal}/post', [JournalsController::class, 'post'])->name('journals.post');
    Route::post('/journals/{journal}/void', [JournalsController::class, 'void'])->name('journals.void');
    Route::get('/journals/create', [JournalsController::class, 'create'])->name('journals.create');
    Route::post('/journals', [JournalsController::class, 'store'])->name('journals.store');
    Route::get('/journals', [JournalsController::class, 'index'])->name('journals.index');

    // ============================================
    // REPORTS
    // ============================================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/trial-balance', [ReportController::class, 'trialBalance'])->name('trial-balance');
        Route::get('/lpk', [ReportController::class, 'lpk'])->name('lpk');
        Route::get('/lak', [ReportController::class, 'lak'])->name('lak');
        Route::get('/neraca-saldo', [ReportController::class, 'neracaSaldo'])->name('neraca-saldo');
    });

});

require __DIR__ . '/auth.php';
