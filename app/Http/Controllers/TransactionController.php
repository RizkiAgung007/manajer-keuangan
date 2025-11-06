<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $transactions = Auth::user()->transactions()->with('category')->latest('transaction_date')->paginate(10);

        return view('transactions.index', ['transactions' => $transactions]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Auth::user()->categories;

        $incomeCategories = $categories->where('type', 'income');
        $expenseCategories = $categories->where('type', 'expense');

        return view('transactions.create', [
            'incomeCategories'  => $incomeCategories,
            'expenseCategories' => $expenseCategories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $type = $request->input('type_selector');
        $categoryId = $request->input('category_id');

        if ($type === 'income') {
            $categoryId = $request->input('category_id_income');
        }

        $request->merge(['category_id' => $categoryId]);

        $validated = $request->validate([
            'category_id'       => ['required', Rule::exists('categories', 'id')->where('user_id', auth()->id())],
            'amount'            => 'required|numeric|min:0',
            'description'       => 'nullable|string|max:255',
            'transaction_date'  => 'required|date',
        ]);

        $request->user()->transactions()->create($validated);

        return redirect(route('transactions.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction): View
    {
        if (auth()->user()->id !== $transaction->user_id) {
            abort(403);
        }

        return view('transactions.show', [
            'transaction' => $transaction->load('category')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction): View
    {
        if (auth()->user()->id !== $transaction->user_id) {
            abort(403);
        }

        $categories = Auth::user()->categories;
        $incomeCategories = $categories->where('type', 'income');
        $expenseCategories = $categories->where('type', 'expense');

        return view('transactions.edit', [
            'transaction'       => $transaction,
            'incomeCategories'  => $incomeCategories,
            'expenseCategories' => $expenseCategories
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        if (auth()->user()->id !== $transaction->user_id) {
            abort(403);
        }

        $type = $request->input('type_selector');
        $categoryId = $request->input('category_id');

        if ($type === 'income') {
            $categoryId = $request->input('category_id_income');
        }

        $request->merge(['category_id' => $categoryId]);

        $validated = $request->validate([
            'category_id'       => ['required', Rule::exists('categories', 'id')->where('user_id', auth()->id())],
            'amount'            => 'required|numeric|min:0',
            'description'       => 'nullable|string|max:255',
            'transaction_date'  => 'required|date',
        ]);

        $transaction->update($validated);

        return redirect(route('transactions.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        if (auth()->user()->id !== $transaction->user_id) {
            abort(403);
        }

        $transaction->delete();

        return redirect(route('transactions.index'));
    }
}
