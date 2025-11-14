<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $user = Auth::user();
        $now = Carbon::now();

        $tags = $user->tags()->withCount('transactions')
                    ->withSum(['transactions as spent_this_month' => function ($query) use ($now) {
                        $query->whereYear('transaction_date', $now->year)->whereMonth('transaction_date', $now->month);
                    }], 'amount')->orderBy('order_column', 'asc')->get();

        return view('tags.index', [
            'tags'  => $tags
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => ['required','string','max:255', Rule::unique('tags')->where('user_id', auth()->id())]
        ]);

        $maxOrder = $request->user()->tags()->max('order_column');

        $validated['order_column'] = $maxOrder + 1;

        $request->user()->tags()->create($validated);

        return redirect(route('tags.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag): View
    {
        if (auth()->id() !== $tag->user_id) {
            abort(403);
        }

        $tag->loadCount('transactions');
        $tag->loadSum('transactions as total_spent', 'amount');

        $transactions = $tag->transactions()
                        ->with('category')
                        ->orderBy('transaction_date', 'desc')
                        ->paginate(10);

        return view('tags.show', [
            'tag'          => $tag,
            'transactions' => $transactions
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag): View
    {
        if (auth()->id() !== $tag->user_id) {
            abort(403);
        }

        return view('tags.edit', [
            'tag'   => $tag
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag): RedirectResponse
    {
        if (auth()->id() !== $tag->user_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name'  => ['required','string','max:255', Rule::unique('tags')->where('user_id', auth()->id())->ignore($tag->id)]
        ]);

        $tag->update($validated);

        return redirect(route('tags.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        if (auth()->id() !== $tag->user_id) {
            abort(403);
        }

        $tag->delete();

        return redirect(route('tags.index'));
    }

    /**
     * Update the display order tags
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer|exists:tags,id',
        ]);

        $user = $request->user();

        foreach($request->input('order') as $index => $tagId) {
            $user->tags()->where('id', $tagId)->update(['order_column' => $index + 1]);
        }

        return response()->json(['status', 'success']);
    }
}
