/**
     * Remove the specified resource from storage.
     */
    public function destroy(Budget $budget): RedirectResponse
    {
        // 1. Otorisasi: Pastikan budget ini milik user
        if (auth()->id() !== $budget->user_id) {
            abort(403);
        }

        // 2. Hapus budget
        $budget->delete();

        // 3. Kembali ke halaman index
        return redirect(route('budgets.index'));
    }


    <form method="POST" action="{{ route('budgets.destroy', $budget->id) }}" onsubmit="return confirm('Are you sure you want to delete this budget?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline pt-2">
        <x-heroicon-o-trash class="w-5 h-5" />
    </button>
</form>
