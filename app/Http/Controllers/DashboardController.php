<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
    * Display a listing of the resource.
    */
    public function index(): View
    {
        $user = Auth::user();
        $now = Carbon::now();

        $transactions = $user->transactions()->with('category')->whereYear('transaction_date', $now->year)->whereMonth('transaction_date', $now->month)->get();

        $totalIncome = $transactions->where('category.type', 'income')->sum('amount');
        $totalExpense = $transactions->where('category.type', 'expense')->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        $latestTransactions = $transactions->sortByDesc('transaction_date')->take(5);

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

        $categories = $user->categories;
        $incomeCategories = $categories->where('type', 'income');
        $expenseCategories = $categories->where('type', 'expense');

        return view('dashboard', [
            'totalIncome'        => $totalIncome,
            'totalExpense'       => $totalExpense,
            'netProfit'          => $netProfit,
            'latestTransactions' => $latestTransactions,
            'incomeChartData'    => $incomeChartData,
            'expenseChartData'   => $expenseChartData,
            'incomeCategories'   => $incomeCategories,
            'expenseCategories'  => $expenseCategories,
        ]);
    }
}
