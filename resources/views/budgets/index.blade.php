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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left">Category</th>
                                    <th scope="col" class="px-6 py-3 text-center">Period</th>
                                    <th scope="col" class="px-6 py-3 text-center">Budget Amount</th>
                                    <th scope="col" class="px-6 py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($budgets as $budget)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-left">
                                            {{ $budget->category->name }}
                                        </th>
                                        <td class="px-6 py-4 text-center">
                                            {{ \Carbon\Carbon::create()->month($budget->month)->format('F') }} {{ $budget->year }}
                                        </td>
                                        <td class="px-6 py-4 text-center font-medium">
                                            Rp {{ number_format($budget->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 flex items-center justify-center space-x-3">
                                            <a href="{{ route('budgets.edit', $budget->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                                <x-heroicon-o-pencil-square class="w-5 h-5"/>
                                            </a>
                                            <form method="POST" action="{{ route('budgets.destroy', $budget->id) }}" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline pt-2">
                                                    <x-heroicon-o-trash class="w-5 h-5" />
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="4" class="px-6 py-4 text-center">
                                            No budgets set yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <x-modal name="copy-budget" :show="false" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Copy Budgets') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Copy all budget entries from one period to another. This will not overwrite existing budgets.
            </p>

            <form method="POST" action="{{ route('budgets.copy.store') }}" class="mt-6 space-y-6">
                @csrf

                <div>
                    <x-input-label for="from_period" :value="__('Copy From Period')" />
                    <select name="from_period" id="from_period" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm" required>
                        <option value="">Select a period to copy from...</option>
                        @foreach ($existingPeriods as $period)
                            <option value="{{ $period->year }}-{{ $period->month }}">
                                {{ \Carbon\Carbon::create($period->year, $period->month, 1)->format('F Y') }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('from_period')" class="mt-2" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="to_month" :value="__('Copy To Month')" />
                        <select name="to_month" id="to_month" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm" required>
                            @php $currentMonth = date('m'); @endphp
                            @for ($month = 1; $month <= 12; $month++)
                                <option value="{{ $month }}" {{ $currentMonth == $month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                                </option>
                            @endfor
                        </select>
                        <x-input-error :messages="$errors->get('to_month')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="to_year" :value="__('Copy To Year')" />
                        <select name="to_year" id="to_year" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm" required>
                            @php
                                $currentYear = date('Y');
                                $endYear = $currentYear + 5;
                            @endphp
                            @for ($year = $currentYear; $year <= $endYear; $year++)
                                <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                        <x-input-error :messages="$errors->get('to_year')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                    <x-primary-button class="ms-4">
                        <x-heroicon-o-document-duplicate class="w-4 h-4 me-2"/>
                        {{ __('Copy Now') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
    </x-app-layout>
