<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\journals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_accounts' => accounts::count(),
            'active_accounts' => accounts::where('is_active', true)->count(),
            'total_journals' => journals::count(),
            'posted_journals' => journals::where('status', 'posted')->count(),
        ];

        $recentJournals = journals::with(['details.account', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $accountTypes = DB::table('accounts')
            ->select('digit_1', DB::raw('count(*) as total'))
            ->groupBy('digit_1')
            ->get()
            ->mapWithKeys(function ($item) {
                $types = [
                    '1' => 'Aset',
                    '2' => 'Hutang',
                    '3' => 'Modal',
                    '4' => 'Pendapatan',
                    '5' => 'Beban'
                ];
                return [$types[$item->digit_1] ?? 'Lainnya' => $item->total];
            });

        return view('dashboard', compact('stats', 'recentJournals', 'accountTypes'));
    }
}