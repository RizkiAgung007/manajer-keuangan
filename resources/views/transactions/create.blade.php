<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add New Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('transactions.store') }}" x-data="{ type: 'expense' }" enctype="multipart/form-data">
                        @csrf

                        <div>
                            <x-input-label :value="__('Transaction Type')" />
                            <div class="mt-2 flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="type_selector" value="expense" x-model="type" class="text-green-600 focus:ring-green-500">
                                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Expense') }}</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="type_selector" value="income" x-model="type" class="text-green-600 focus:ring-green-500">
                                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Income') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="category_id" :value="__('Category')" />
                            <select name="category_id" id="category_id" x-show="type === 'expense'" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm">
                                <option value="">{{ __('Select an expense category') }}</option>

                                @foreach ($expenseCategories as $parent)
                                    <option value="{{ $parent->id }}" {{ old('category_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>

                                    @foreach ($parent->children as $child)
                                        <option value="{{ $child->id }}" {{ old('category_id') == $child->id ? 'selected' : '' }}>
                                            &nbsp;&nbsp;└ {{ $child->name }}
                                        </option>
                                    @endforeach
                                @endforeach

                            </select>

                            <select name="category_id_income" x-show="type === 'income'" style="display: none;" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm">
                                <option value="">{{ __('Select an income category') }}</option>

                                @foreach ($incomeCategories as $parent)
                                    <option value="{{ $parent->id }}" {{ old('category_id_income') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>

                                    @foreach ($parent->children as $child)
                                        <option value="{{ $child->id }}" {{ old('category_id_income') == $child->id ? 'selected' : '' }}>
                                            &nbsp;&nbsp;└ {{ $child->name }}
                                        </option>
                                    @endforeach
                                @endforeach

                            </select>

                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="amount" :value="__('Amount')" />
                            <x-text-input id="amount" class="block mt-1 w-full" type="number" name="amount" :value="old('amount')" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <x-text-input id="description" class="block mt-1 w-full" type="text" name="description" :value="old('description')" />
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="transaction_date" :value="__('Transaction Date')" />
                            <x-text-input id="transaction_date" class="block mt-1 w-full" type="date" name="transaction_date" :value="old('transaction_date', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('transaction_date')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="tags-create" :value="__('Tags (Optional)')" />

                            <select id="tags-create"
                                    name="tags[]"
                                    multiple
                                    placeholder="Search and select tags..."
                                    autocomplete="off"
                                    class="block mt-1 w-full">

                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}"
                                        {{ (is_array(old('tags')) && in_array($tag->id, old('tags'))) ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>

                            <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="attachment" :value="__('Attach Receipt (Optional)')" />
                            <x-text-input id="attachment" name="attachment" type="file" class="block mt-1 w-full file:border-0 file:bg-gray-100 file:dark:bg-gray-700 file:text-gray-700 file:dark:text-gray-300 file:px-4 file:py-2 file:rounded-lg file:mr-4 hover:file:bg-gray-200 dark:hover:file:bg-gray-600 cursor-pointer" />
                            <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('transactions.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button class="ms-4">
                                <x-heroicon-o-check-circle class="w-4 h-4 me-2"/>
                                {{ __('Save') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

        <script>
            new TomSelect('#tags-create', {
                plugins: ['remove_button'],
            });
        </script>
    @endpush
</x-app-layout>
