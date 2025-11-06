<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Set New Budget') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('budgets.store') }}">
                        @csrf

                        <div>
                            <x-input-label for="category_id" :value="__('Expense Category')" />
                            <select name="category_id" id="category_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm" required>
                                <option value="">{{ __('Select a category') }}</option>
                                @foreach ($expenseCategories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="amount" :value="__('Budget Amount')" />
                            <x-text-input id="amount" class="block mt-1 w-full" type="number" name="amount" :value="old('amount')" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="month" :value="__('Month')" />
                                <select name="month" id="month" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm" required>
                                    @php $currentMonth = old('month', date('m')); @endphp
                                    @for ($month = 1; $month <= 12; $month++)
                                        <option value="{{ $month }}" {{ $currentMonth == $month ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                                <x-input-error :messages="$errors->get('month')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="year" :value="__('Year')" />
                                <select name="year" id="year" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm" required>
                                    @php
                                        $currentYear = old('year', date('Y'));
                                        $endYear = $currentYear + 5; // Tampilkan 5 tahun ke depan
                                    @endphp
                                    @for ($year = $currentYear; $year <= $endYear; $year++)
                                        <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                                <x-input-error :messages="$errors->get('year')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('budgets.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button class="ms-4">
                                <x-heroicon-o-check-circle class="w-4 h-4 me-2"/>
                                {{ __('Save Budget') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
