<?php
// app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;

use App\Services\AccountService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * Trial Balance Report
     */
    public function trialBalance(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $report = $this->accountService->getTrialBalance($year, $month);

        return view('pages.reports.trial-balance.index', compact('report', 'year', 'month'));
    }

    /**
     * Laporan Posisi Keuangan (Balance Sheet)
     */
    public function lpk(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        // TODO: Implement LPK logic
        
        return view('reports.lpk', compact('year', 'month'));
    }

    /**
     * Laporan Arus Kas (Cash Flow Statement)
     */
    public function lak(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        // TODO: Implement LAK logic
        
        return view('reports.lak', compact('year', 'month'));
    }

    /**
     * Neraca Saldo
     */
    public function neracaSaldo(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        // TODO: Implement Neraca Saldo logic
        
        return view('reports.neraca-saldo', compact('year', 'month'));
    }
}