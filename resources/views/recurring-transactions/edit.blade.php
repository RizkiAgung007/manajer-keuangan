<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Recurring Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('recurring-transactions.update', $transaction->id) }}" x-data="{ type: '{{ $transaction->category->type }}' }">
                        @csrf
                        @method('PUT') <div>
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
                                @foreach ($expenseCategories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="category_id_income" x-show="type === 'income'" style="display: none;" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm">
                                <option value="">{{ __('Select an income category') }}</option>
                                @foreach ($incomeCategories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="amount" :value="__('Amount')" />
                            <x-text-input id="amount" class="block mt-1 w-full" type="number" name="amount" :value="old('amount', $transaction->amount)" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <x-text-input id="description" class="block mt-1 w-full" type="text" name="description" :value="old('description', $transaction->description)" placeholder="Optional (e.g., Gaji Bulanan)" />
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="frequency" :value="__('Frequency')" />
                                <select name="frequency" id="frequency" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm" required>
                                    <option value="monthly" {{ $transaction->frequency == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="day_of_month" :value="__('Day of Month (1-31)')" />
                                <x-text-input id="day_of_month" class="block mt-1 w-full" type="number" name="day_of_month" :value="old('day_of_month', $transaction->day_of_month)" min="1" max="31" required />
                                <x-input-error :messages="$errors->get('day_of_month')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="start_date" :value="__('Start Date')" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date', \Carbon\Carbon::parse($transaction->start_date)->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('recurring-transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button class="ms-4">
                                <x-heroicon-o-check-circle class="w-4 h-4 me-2"/>
                                {{ __('Update') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
