<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        $transactions = $user->transactions()->with('category')->whereYear('transaction_date', $year)->whereMonth('transaction_date', $month)->get();

        $totalIncome = $transactions->where('category.type', 'income')->sum('amount');
        $totalExpense = $transactions->where('category.type', 'expense')->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        $expenseByCategory = $transactions->where('category.type', 'expense')->groupBy('category.name')
                                        ->map(function ($group) {
                                            return $group->sum('amount');
                                        });

        $expenseChartData = [
            'labels' => $expenseByCategory->keys(),
            'series' => $expenseByCategory->values(),
        ];

        $incomeByCategory = $transactions->where('category.type', 'income')->groupBy('category.name')
                                        ->map(function ($group) {
                                            return $group->sum('amount');
                                        });

        $incomeChartData = [
            'labels' => $incomeByCategory->keys(),
            'series' => $incomeByCategory->values(),
        ];

        return view('reports.index', [
            'totalIncome'       => $totalIncome,
            'totalExpense'      => $totalExpense,
            'netProfit'         => $netProfit,
            'expenseChartData'  => $expenseChartData,
            'incomeChartData'   => $incomeChartData,
            'monthName'         => $now->format('F Y'),
        ]);
    }
}
