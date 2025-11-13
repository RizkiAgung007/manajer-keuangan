<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $user = Auth::user();
        $now = Carbon::now();

        $sumQueries = function ($query) use ($now) {
            $query->withSum([
                'transactions as spent_this_month' => function ($query) use ($now) {
                    $query->whereYear('transaction_date', $now->year)
                          ->whereMonth('transaction_date', $now->month);
                }
            ], 'amount')
            ->withSum([
                'budgets as budget_this_month' => function ($query) use ($now) {
                    $query->where('year', $now->year)
                          ->where('month', $now->month);
                }
            ], 'amount');
        };

        $categories = Category::where('user_id', $user->id)
                        ->parentCategories()
                        ->tap($sumQueries)
                        ->with([
                            'children' => function ($query) use ($sumQueries) {
                                $query->tap($sumQueries)
                                    ->orderBy('order_column', 'asc');
                            }
                        ])
                        ->orderBy('order_column', 'asc')
                        ->get();

        return view('categories.index', [
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $parentCategories = Category::where('user_id', Auth::id())
                            ->parentCategories()
                            ->get();

        return view('categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'type'      => ['required', Rule::in(['income', 'expense'])],
            'parent_id' => ['nullable', 'integer', Rule::exists('categories', 'id')
                            ->where(function ($query) {
                                $query->where('user_id', Auth::id());
                            })]
        ]);

        $query = $request->user()->categories();

        if ($request->filled('parent_id')) {
            $maxOrder = $query->where('parent_id', $request->parent_id)->max('order_column');
            $validated['parent_id'] = $request->parent_id;
        } else {
            $maxOrder = $query->whereNull('parent_id')->max('order_column');
            $validated['parent_id'] = null;
        }

        $validated['order_column'] = $maxOrder + 1;

        $request->user()->categories()->create($validated);

        return redirect(route('categories.index'));
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
    public function edit(Category $category): View
    {
        if (auth()->user()->id !== $category->user_id) {
            abort(403);
        }

        return view('categories.edit', ['category' => $category]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        if (auth()->user()->id !== $category->user_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'type'  => ['required', Rule::in(['income', 'expense'])],
        ]);

        $category->update($validated);

        return redirect(route('categories.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if (auth()->user()->id !== $category->user_id) {
            abort(403);
        }

        $category->delete();

        return redirect(route('categories.index'));
    }

    /**
     * Update the display order of categories
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer|exists:categories,id'
        ]);

        $user = $request->user();

        foreach ($request->input('order') as $index => $categoryId) {
            $user->categories()
                    ->where('id', $categoryId)
                    ->update(['order_column' => $index + 1]);
        }

        return response()->json(['status' => 'success']);
    }
}
