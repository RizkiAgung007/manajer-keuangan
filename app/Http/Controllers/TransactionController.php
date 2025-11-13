<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $filters = [
            'search'        => $request->input('search'),
            'type'          => $request->input('type'),
            'category_id'   => $request->input('category_id'),
            'date_from'     => $request->input('date_from'),
            'date_to'       => $request->input('date_to'),
            'sort'          => $request->input('sort', 'desc'),
        ];

        $today = Carbon::today();
        $dates = [
            'this_month_start'  => $today->copy()->startOfMonth()->toDateString(),
            'this_month_end'    => $today->copy()->endOfMonth()->toDateString(),
            'last_month_start'  => $today->copy()->subMonthNoOverflow()->startOfMonth()->toDateString(),
            'last_month_end'    => $today->copy()->subMonthNoOverflow()->endOfMonth()->toDateString(),
            'this_year_start'   => $today->copy()->startOfYear()->toDateString(),
            'this_year_end'     => $today->copy()->endOfYear()->toDateString()
        ];

        $query = $user->transactions()->with('category')
                    ->when($filters['search'], function ($q, $search) {
                        return $q->where('description', 'like', "%{$search}%");
                    })
                    ->when($filters['type'], function ($q, $type) {
                        return $q->whereRelation('category', 'type', $type);
                    })
                    ->when($filters['category_id'], function ($q, $category_id) {
                        return $q->where('category_id', $category_id);
                    })
                    ->when($filters['date_from'], function ($q, $date_from) {
                        return $q->where('transaction_date', '>=', $date_from);
                    })
                    ->when($filters['date_to'], function ($q, $date_to) {
                        return $q->where('transaction_date', '<=', $date_to);
                    });

        $query->orderBy('transaction_date', $filters['sort']);

        $transactions = $query->paginate(10)->withQueryString();
        $categories = Category::where('user_id', $user->id)
                        ->parentCategories()
                        ->with('children')
                        ->get();

        return view('transactions.index', [
            'transactions'  => $transactions,
            'categories'    => $categories,
            'filters'       => $filters,
            'dates'         => $dates
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = Auth::user();
        $categories = $user->categories;

        $incomeCategories = Category::where('user_id', Auth::id())
                            ->where('type', 'income')
                            ->parentCategories()
                            ->with('children')
                            ->get();

        $expenseCategories = Category::where('user_id', Auth::id())
                            ->where('type', 'expense')
                            ->parentCategories()
                            ->with('children')
                            ->get();

        $tags = $user->tags()->orderBy('name')->get();

        return view('transactions.create', [
            'incomeCategories'  => $incomeCategories,
            'expenseCategories' => $expenseCategories,
            'tags'              => $tags
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
            'attachment'        => 'nullable|file|image|max:2048',
            'tags'              => 'nullable|array',
            'tags.*'            => ['integer', Rule::exists('tags', 'id')->where('user_id', auth()->id())],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        $transaction = $request->user()->transactions()->create([
            'category_id'       => $validated['category_id'],
            'amount'            => $validated['amount'],
            'description'       => $validated['description'],
            'transaction_date'  => $validated['transaction_date'],
            'attachment_path'   => $attachmentPath
        ]);

        if ($request->has('tags')) {
            $transaction->tags()->sync($validated['tags']);
        }

        return redirect()->back()->with('status', 'transaction-created');
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
            'transaction' => $transaction->load(['category', 'tags'])
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

        $user = Auth::user();
        $categories = $user->categories;

        $incomeCategories = Category::where('user_id', Auth::id())
                            ->where('type', 'income')
                            ->parentCategories()
                            ->with('children')
                            ->get();

        $expenseCategories = Category::where('user_id', Auth::id())
                            ->where('type', 'expense')
                            ->parentCategories()
                            ->with('children')
                            ->get();

        $allTags = Auth::user()->tags()->orderBy('name')->get();

        $transactionTags = $transaction->tags->pluck('id');

        return view('transactions.edit', [
            'transaction'       => $transaction,
            'incomeCategories'  => $incomeCategories,
            'expenseCategories' => $expenseCategories,
            'allTags'           => $allTags,
            'transactionTags'   => $transactionTags
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
            'attachment'        => 'nullable|file|image|max:2048',
            'delete_attachment' => 'nullable|boolean',
            'tags'              => 'nullable|array',
            'tags.*'            => ['integer', Rule::exists('tags', 'id')->where('user_id', auth()->id())],
        ]);

        $dataToUpdate = [
            'category_id'       => $validated['category_id'],
            'amount'            => $validated['amount'],
            'description'       => $validated['description'],
            'transaction_date'  => $validated['transaction_date'],
        ];

        if ($request->hasFile('attachment')) {
            if ($transaction->attachment_path) {
                Storage::disk('public')->delete($transaction->attachment_path);
            }

            $dataToUpdate['attachment_path'] = $request->file('attachment')->store('attachments', 'public');
        } elseif ($request->input('delete_attachment')) {
            if ($transaction->attachment_path) {
                Storage::disk('public')->delete($transaction->attachment_path);
            }

            $dataToUpdate['attachment_path'] = null;
        }

        $transaction->update($dataToUpdate);

        $transaction->tags()->sync($request->input('tags', []));

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
