<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
// Pastikan model-model ini ada
use App\Models\Category;
use App\Models\Budget;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $selectedYear = $request->input('year', Carbon::now()->year);
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $date = Carbon::createFromDate($selectedYear, $selectedMonth, 1);


        $allTransactions = $user->transactions()->with('category')->whereYear('transaction_date', $selectedYear)->whereMonth('transaction_date', $selectedMonth)->get();

        $transactionsForTable = $user->transactions()->with('category')->whereYear('transaction_date', $selectedYear)->whereMonth('transaction_date', $selectedMonth)->orderBy('transaction_date', 'desc')->paginate(10);

        $expenseByCategory = $allTransactions->where('category.type', 'expense')->groupBy('category.name')
                                          ->map(fn ($group) => $group->sum('amount'));

        $expenseChartData = [
            'labels' => $expenseByCategory->keys(),
            'series' => $expenseByCategory->values(),
        ];

        $incomeByCategory = $allTransactions->where('category.type', 'income')->groupBy('category.name')
                                         ->map(fn ($group) => $group->sum('amount'));

        $incomeChartData = [
            'labels' => $incomeByCategory->keys(),
            'series' => $incomeByCategory->values(),
        ];

        $budgetsMap = $user->budgets()->where('year', $selectedYear)->where('month', $selectedMonth)->get()->keyBy('category_id');

        $actualByCategoryId = $allTransactions->where('category.type', 'expense')->groupBy('category_id')
                                         ->map(fn ($group) => $group->sum('amount'));

        $expenseCategories = $user->categories()->where('type', 'expense')->get();

        $budgetComparison = $expenseCategories->map(function ($category) use ($budgetsMap, $actualByCategoryId) {
            $budget = $budgetsMap->get($category->id);
            $budgetAmount = $budget ? $budget->amount : 0;
            $actualAmount = $actualByCategoryId->get($category->id, 0);

            if ($budgetAmount == 0 && $actualAmount == 0) {
                return null;
            }
            $difference = $budgetAmount - $actualAmount;
            $percentage  = ($budgetAmount > 0) ? ($actualAmount / $budgetAmount) * 100 : 0;
            return [
                'category_name' => $category->name,
                'budget_amount' => $budgetAmount,
                'actual_amount' => $actualAmount,
                'difference'    => $difference,
                'percentage'    => round($percentage)
            ];
        })->filter();

        return view('reports.index', [
            'transactions'      => $transactionsForTable,
            'expenseChartData'  => $expenseChartData,
            'incomeChartData'   => $incomeChartData,
            'monthName'         => $date->format('F Y'),
            'selectedYear'      => $selectedYear,
            'selectedMonth'     => $selectedMonth,
            'budgetComparison'  => $budgetComparison,
        ]);
    }
}
