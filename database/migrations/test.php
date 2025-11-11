<x-app-layout>
    <x-slot name="header">
        <div x-data class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Budgets') }}
            </h2>

            <div class="flex space-x-4">
                <x-secondary-button
                    x-on:click.prevent="$dispatch('open-modal', 'copy-budget')">
                    <x-heroicon-o-document-duplicate class="w-4 h-4 me-2"/>
                    {{ __('Copy Budgets') }}
                </x-secondary-button>

                <a href="{{ route('budgets.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <x-heroicon-o-plus class="w-4 h-4 me-2"/>
                    {{ __('Set New Budget') }}
                </a>
            </div>
        </div>
    </x-slot>
    ```

Hanya itu yang perlu Anda ubah. **Simpan file `budgets/index.blade.php`** (file *controller* Anda sudah benar) dan *refresh* halaman. Tombol "Copy Budgets" Anda sekarang akan berfungsi dengan sempurna.

Beri tahu saya jika modalnya sudah muncul!
