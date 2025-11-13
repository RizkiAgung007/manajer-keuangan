<?php

namespace App\Http\Controllers;

use App\Models\RecurringTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RecurringTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $user = Auth::user();

        $recurringTransactions = $user->recurringTransactions()
                                    ->with('category')
                                    ->orderBy('day_of_month', 'asc')
                                    ->get();

        return view('recurring-transactions.index', [
            'transactions' => $recurringTransactions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = Auth::user();

        $categories = $user->categories()->orderBy('name', 'asc')->get();
        $incomeCategories = $categories->where('type', 'income');
        $expenseCategories = $categories->where('type', 'expense');

        return view('recurring-transactions.create', [
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
            'category_id'   => ['required', Rule::exists('categories', 'id')->where('user_id', auth()->id())],
            'amount'        => 'required|numeric|min:0',
            'description'   => 'nullable|string|max:255',
            'frequency'     => ['required', Rule::in(['monthly'])],
            'day_of_month'  => 'required|integer|min:1|max:31',
            'start_date'    => 'required|date',
        ]);

        $validatedData = array_merge($validated, [
            'user_id'           => auth()->id(),
            'last_processed_at' => null,
            'is_active'         => true
        ]);

        RecurringTransaction::create($validatedData);

        return Redirect(route('recurring-transactions.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RecurringTransaction $recurringTransaction): View
    {
        if (auth()->id() !== $recurringTransaction->user_id) {
            abort(403);
        }

        $categories = Auth::user()->categories()->orderBy('name', 'asc')->get();
        $incomeCategories = $categories->where('type', 'income');
        $expenseCategories = $categories->where('type', 'expense');

        return view('recurring-transactions.edit', [
            'transaction'       => $recurringTransaction,
            'incomeCategories'  => $incomeCategories,
            'expenseCategories' => $expenseCategories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RecurringTransaction $recurringTransaction): RedirectResponse
    {
        if (auth()->id() !== $recurringTransaction->user_id) {
            abort(403);
        }

        $type = $request->input('type_selector');
        $categoryId = $request->input('category_id');

        if ($type === 'income') {
            $categoryId = $request->input('category_id_income');
        }

        $request->merge(['category_id' => $categoryId]);

        $validated = $request->validate([
            'category_id'   => ['required', Rule::exists('categories', 'id')->where('user_id', auth()->id())],
            'amount'        => 'required|numeric|min:0',
            'description'   => 'nullable|string|max:255',
            'frequency'     => ['required', Rule::in(['monthly'])],
            'day_of_month'  => 'required|integer|min:1|max:31',
            'start_date'    => 'required|date',
        ]);

        $recurringTransaction->update($validated);

        return redirect(route('recurring-transactions.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RecurringTransaction $recurringTransaction): RedirectResponse
    {
        if (auth()->id() !== $recurringTransaction->user_id) {
            abort(403);
        }

        $recurringTransaction->delete();

        return redirect(route('recurring-transactions.index'));
    }
}
