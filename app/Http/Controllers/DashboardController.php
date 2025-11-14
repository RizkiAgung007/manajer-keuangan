<?php

namespace App\Http\Controllers;

use App\Models\Category;
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
        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfMonth();

        $transactions = $user->transactions()
                        ->with('category')
                        ->whereBetween('transaction_date', [$startDate, $endDate])
                        ->get();

        $totalIncome = $transactions->where('category.type', 'income')->sum('amount');
        $totalExpense = $transactions->where('category.type', 'expense')->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        $totalBudget = $user->budgets()
                        ->where('year', $now->year)
                        ->where('month', $now->month)
                        ->sum('amount');

        $budgetPercentage = ($totalBudget > 0) ? round(($totalExpense / $totalBudget) * 100) : 0;

        $latestTransactions = $transactions->sortByDesc('transaction_date')->take(5);

        $childToParentMap = Category::where('user_id', $user->id)->whereNotNull('parent_id')->pluck('parent_id', 'id');

        $parentCategories = Category::where('user_id', $user->id)->parentCategories()->get()->keyBy('id');

        $groupByParent = $transactions->groupBy(function ($tx) use ($childToParentMap) {
            return $childToParentMap[$tx->category_id] ?? $tx->category_id;
        });

        $reportData = $groupByParent->map(function ($group, $parent_id) use ($parentCategories) {
            $category = $parentCategories->get($parent_id);
            if (!$category) {
                return null;
            }
            return [
                'name'  => $category->name,
                'type'  => $category->type,
                'total' => $group->sum('amount')
            ];
        })->filter();

        $expenseData = $reportData->where('type', 'expense');
        $expenseChartData = [
            'labels' => $expenseData->pluck('name')->values()->toArray(),
            'series' => $expenseData->pluck('total')->values()->toArray(),
        ];

        $incomeData = $reportData->where('type', 'income');
        $incomeChartData = [
            'labels' => $incomeData->pluck('name')->values()->toArray(),
            'series' => $incomeData->pluck('total')->values()->toArray(),
        ];

        $incomeCategories = Category::where('user_id', $user->id)
                                ->where('type', 'income')
                                ->parentCategories()
                                ->with('children')
                                ->orderBy('order_column')
                                ->get();

        $expenseCategories = Category::where('user_id', $user->id)
                                ->where('type', 'expense')
                                ->parentCategories()
                                ->with('children')
                                ->orderBy('order_column')
                                ->get();

        $tags = $user->tags()->orderBy('name')->get();

        return view('dashboard', [
            'totalIncome'        => $totalIncome,
            'totalExpense'       => $totalExpense,
            'netProfit'          => $netProfit,
            'totalBudget'        => $totalBudget,
            'budgetPercentage'   => $budgetPercentage,
            'latestTransactions' => $latestTransactions,
            'incomeChartData'    => $incomeChartData,
            'expenseChartData'   => $expenseChartData,
            'incomeCategories'   => $incomeCategories,
            'expenseCategories'  => $expenseCategories,
            'tags'               => $tags
        ]);
    }
}
