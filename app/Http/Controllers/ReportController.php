<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
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
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        $allTransactions = $user->transactions()
            ->with('category')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        $transactionsForTable = $user->transactions()
            ->with('category')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        $childToParentMap = Category::where('user_id', $user->id)
            ->whereNotNull('parent_id')
            ->pluck('parent_id', 'id');

        $parentCategories = Category::where('user_id', $user->id)
            ->parentCategories()
            ->with('children')
            ->get()
            ->keyBy('id');

        $groupedByParent = $allTransactions->groupBy(function ($tx) use ($childToParentMap) {
            return $childToParentMap[$tx->category_id] ?? $tx->category_id;
        });

        $reportData = $groupedByParent->map(function ($group, $parent_id) use ($parentCategories) {
            $category = $parentCategories->get($parent_id);

            if (!$category) {
                return null;
            }

            return [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->type,
                'total' => $group->sum('amount')
            ];
        })->filter();

        $incomeData = $reportData->where('type', 'income');
        $incomeChartData = [
            'labels' => $incomeData->pluck('name')->values()->toArray(),
            'series' => $incomeData->pluck('total')->values()->toArray(),
        ];

        $expenseData = $reportData->where('type', 'expense');
        $expenseChartData = [
            'labels' => $expenseData->pluck('name')->values()->toArray(),
            'series' => $expenseData->pluck('total')->values()->toArray(),
        ];

        $budgetsMap = $user->budgets()
            ->where('year', $selectedYear)
            ->where('month', $selectedMonth)
            ->whereIn('category_id', $parentCategories->keys())
            ->pluck('amount', 'category_id');

        $rolledUpExpenses = $expenseData->keyBy('id');
        $budgetComparison = [];

        foreach ($parentCategories->where('type', 'expense') as $parent) {
            $budgetAmount = $budgetsMap->get($parent->id) ?? 0;
            $actualAmount = $rolledUpExpenses->get($parent->id)['total'] ?? 0;

            if ($budgetAmount > 0 || $actualAmount > 0) {
                $difference = $budgetAmount - $actualAmount;
                $percentage = ($budgetAmount > 0) ? ($actualAmount / $budgetAmount) * 100 : 0;

                $budgetComparison[] = [
                    'category_name' => $parent->name,
                    'budget_amount' => $budgetAmount,
                    'actual_amount' => $actualAmount,
                    'difference'    => $difference,
                    'percentage'    => round($percentage)
                ];
            }
        }


        return view('reports.index', [
            'transactions'     => $transactionsForTable, 
            'expenseChartData' => $expenseChartData,
            'incomeChartData'  => $incomeChartData,
            'monthName'        => $date->format('F Y'),
            'selectedYear'     => $selectedYear,
            'selectedMonth'    => $selectedMonth,
            'budgetComparison' => $budgetComparison,
        ]);
    }
}
