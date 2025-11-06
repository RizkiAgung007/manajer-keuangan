<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $budgets = Auth()->user()->budgets()->with('category')->orderBy('year', 'desc')->orderBy('month', 'desc')->get();

        return view('budgets.index', ['budgets' => $budgets]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $expenseCategories = Auth::user()->categories()->where('type', 'expense')->get();

        return view('budgets.create', [
            'expenseCategories' => $expenseCategories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'   => ['required', Rule::exists('categories', 'id')->where('user_id', auth()->id())->where('type', 'expense')],
            'amount'        => 'required|numeric|min:0',
            'month'         => 'required|integer|min:1|max:12',
            'year'          => 'required|integer|min:' . date('Y'),
        ]);

        $existing = Budget::where('user_id', auth()->id())->where('category_id', $validated['category_id'])->where('month', $validated['month'])->where('year', $validated['year'])->exists();

        if($existing) {
            return back()->withErrors([
                'category_id' => 'A budget for this category in this month and year already exists.'
            ])->withInput();
        }

        $request->user()->budgets()->create($validated);

        return redirect(route('budgets.index'));
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
    public function edit(Budget $budget): View
    {
        if (auth()->id() !== $budget->user_id) {
            abort(403);
        }

        $expenseCategories = Auth::user()->categories()->where('type', 'expense')->get();

        return view('budgets.edit', [
            'budget'            => $budget,
            'expenseCategories' => $expenseCategories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Budget $budget): RedirectResponse
    {
        if (auth()->id() !== $budget->user_id) {
            abort(403);
        }

        $validated = $request->validate([
            'category_id'   => ['required', Rule::exists('categories', 'id')->where('user_id', auth()->id())->where('type', 'expense')],
            'amount'        => 'required|numeric|min:0',
            'month'         => 'required|integer|min:1|max:12',
            'year'          => 'required|integer|min:' . date('Y'),
        ]);

        $existing = Budget::where('user_id', auth()->id())->where('category_id', $validated['category_id'])->where('month', $validated['month'])->where('year', $validated['year'])->where('id', '!=', $budget->id)->exists();

        if ($existing) {
            return back()->withErrors([
                'category_id' => 'A budget for this category in this month and year already exists.'
            ])->withInput();
        }

        $budget->update($validated);

        return redirect(route('budgets.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Budget $budget): RedirectResponse
    {
        if (auth()->id() !== $budget->user_id) {
            abort(403);
        }

        $budget->delete();

        return redirect(route('budgets.index'));
    }
}
