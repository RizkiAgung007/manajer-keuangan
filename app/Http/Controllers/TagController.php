<?php

namespace App\Http\Controllers;

use App\Models\Tag;
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

        $tags = $user->tags()->orderBy('name', 'desc')->get();

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

        $request->user()->tags()->create($validated);

        return redirect(route('tags.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag): View
    {
        if (auth()->id() !== $tag->user_id) {
            abort(403);
        }

        return view('tags.edit');
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
            'name'  => ['required','string','max:255', Rule::unique('tags')->where('user_id', auth()->id())]
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
}
