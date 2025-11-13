<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Category;
use App\Models\Budget;
use App\Models\Transaction; // 1. EDIT: Tambahkan model Transaction

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

        // 2. EDIT: Ambil semua transaksi di periode ini
        $allTransactions = $user->transactions()
            ->with('category')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        // 3. EDIT: Ambil daftar paginasi untuk tabel di bawah
        $transactionsForTable = $user->transactions()
            ->with('category')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->paginate(10) // Anda bisa ganti angka 10
            ->withQueryString();

        // 4. EDIT: Buat Peta [child_id => parent_id] untuk roll-up
        $childToParentMap = Category::where('user_id', $user->id)
            ->whereNotNull('parent_id')
            ->pluck('parent_id', 'id');

        // 5. EDIT: Ambil semua KATEGORI INDUK, jadikan "key"
        $parentCategories = Category::where('user_id', $user->id)
            ->parentCategories()
            ->with('children') // Ambil children untuk logika budget nanti
            ->get()
            ->keyBy('id');

        // 6. EDIT: Kelompokkan transaksi berdasarkan ID Induk-nya
        $groupedByParent = $allTransactions->groupBy(function ($tx) use ($childToParentMap) {
            // Jika kategori tx ada di peta, gunakan parent_id.
            // Jika tidak, berarti dia sudah induk, gunakan category_id-nya.
            return $childToParentMap[$tx->category_id] ?? $tx->category_id;
        });

        // 7. EDIT: Hitung total per induk & format datanya
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
        })->filter(); // filter() untuk menghapus data null

        // --- Chart Data (Sekarang menggunakan data Roll-Up) ---

        // 8. EDIT: Data chart income (sudah di-roll-up)
        $incomeData = $reportData->where('type', 'income');
        $incomeChartData = [
            'labels' => $incomeData->pluck('name')->values()->toArray(),
            'series' => $incomeData->pluck('total')->values()->toArray(),
        ];

        // 9. EDIT: Data chart expense (sudah di-roll-up)
        $expenseData = $reportData->where('type', 'expense');
        $expenseChartData = [
            'labels' => $expenseData->pluck('name')->values()->toArray(),
            'series' => $expenseData->pluck('total')->values()->toArray(),
        ];

        // --- Budget Comparison (Sekarang menggunakan data Roll-Up) ---

        // 10. EDIT: Ambil budget (diasumsikan budget ada di Kategori Induk)
        $budgetsMap = $user->budgets()
            ->where('year', $selectedYear)
            ->where('month', $selectedMonth)
            ->whereIn('category_id', $parentCategories->keys()) // Hanya ambil budget induk
            ->pluck('amount', 'category_id');

        // 11. EDIT: Jadikan data expense roll-up sebagai "key"
        $rolledUpExpenses = $expenseData->keyBy('id');
        $budgetComparison = [];

        // 12. EDIT: Bandingkan budget (induk) vs pengeluaran (roll-up)
        foreach ($parentCategories->where('type', 'expense') as $parent) {
            $budgetAmount = $budgetsMap->get($parent->id) ?? 0;
            $actualAmount = $rolledUpExpenses->get($parent->id)['total'] ?? 0;

            // Hanya tampilkan jika ada budget ATAU ada pengeluaran
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

        // --- Kirim Data ke View ---

        return view('reports.index', [
            'transactions'     => $transactionsForTable, // 13. EDIT: Ganti nama variabel
            'expenseChartData' => $expenseChartData,
            'incomeChartData'  => $incomeChartData,
            'monthName'        => $date->format('F Y'),
            'selectedYear'     => $selectedYear,
            'selectedMonth'    => $selectedMonth,
            'budgetComparison' => $budgetComparison,
        ]);
    }
}
