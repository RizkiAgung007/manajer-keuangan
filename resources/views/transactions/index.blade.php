<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Transactions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6"
             x-data="{
                showFilters: false,
                date_from: '{{ $filters['date_from'] }}',
                date_to: '{{ $filters['date_to'] }}'
             }">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                        <form method="GET" action="{{ route('transactions.index') }}" class="flex-grow w-full md:w-auto">
                            <x-input-label for="search" :value="__('Search Description')" class="sr-only" />
                            <x-text-input id="search" class="block mt-1 w-full" type="text" name="search" :value="$filters['search']" placeholder="Search by description..." />
                        </form>
                        <button type="button" @click="showFilters = !showFilters" class="flex-shrink-0 inline-flex items-center text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                            <x-heroicon-o-adjustments-horizontal class="w-5 h-5 me-2"/>
                            <span x-show="!showFilters">Show Filters</span>
                            <span x-show="showFilters" style="display: none;">Hide Filters</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" x-show="showFilters" x-transition style="display: none;">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Filters</h3>
                    <form method="GET" action="{{ route('transactions.index') }}">
                        <input type="hidden" name="search" value="{{ $filters['search'] }}">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                            <div>
                                <x-input-label for="category_id" :value="__('Category')" />
                                <select name="category_id" id="category_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm">
                                    <option value="">All Categories</option>

                                    @foreach ($categories as $parent)
                                        <option value="{{ $parent->id }}" {{ $filters['category_id'] == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>

                                        @foreach ($parent->children as $child)
                                            <option value="{{ $child->id }}" {{ $filters['category_id'] == $child->id ? 'selected' : '' }}>
                                                &nbsp;&nbsp;└ {{ $child->name }}
                                            </option>
                                        @endforeach
                                    @endforeach

                                </select>
                            </div>

                            <div>
                                <x-input-label for="type" :value="__('Type')" />
                                <select name="type" id="type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm">
                                    <option value="">All Types</option>
                                    <option value="income" {{ $filters['type'] == 'income' ? 'selected' : '' }}>Income</option>
                                    <option value="expense" {{ $filters['type'] == 'expense' ? 'selected' : '' }}>Expense</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="sort" :value="__('Sort By Date')" />
                                <select name="sort" id="sort" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm">
                                    <option value="desc" {{ $filters['sort'] == 'desc' ? 'selected' : '' }}>Newest First</option>
                                    <option value="asc" {{ $filters['sort'] == 'asc' ? 'selected' : '' }}>Oldest First</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label :value="__('Date Range')" />

                                <div class="flex items-center space-x-2 mt-1">
                                    <button type="button" @click="date_from = '{{ $dates['this_month_start'] }}'; date_to = '{{ $dates['this_month_end'] }}'"
                                        class="px-2 py-0.5 text-xs font-semibold text-green-700 bg-green-100 rounded-full hover:bg-green-200 dark:bg-green-900 dark:text-green-300">
                                        This Month
                                    </button>
                                    <button type="button" @click="date_from = '{{ $dates['last_month_start'] }}'; date_to = '{{ $dates['last_month_end'] }}'"
                                        class="px-2 py-0.5 text-xs font-semibold text-gray-700 bg-gray-100 rounded-full hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300">
                                        Last Month
                                    </button>
                                    <button type="button" @click="date_from = '{{ $dates['this_year_start'] }}'; date_to = '{{ $dates['this_year_end'] }}'"
                                        class="px-2 py-0.5 text-xs font-semibold text-gray-700 bg-gray-100 rounded-full hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300">
                                        This Year
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-2 mt-2">
                                    <div>
                                        <x-input-label for="date_from" :value="__('From')" class="text-xs" />
                                        <x-text-input id="date_from" class="block mt-1 w-full" type="date" name="date_from" x-model="date_from" />
                                    </div>
                                    <div>
                                        <x-input-label for="date_to" :value="__('To')" class="text-xs" />
                                        <x-text-input id="date_to" class="block mt-1 w-full" type="date" name="date_to" x-model="date_to" />
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col justify-end gap-4">
                                <x-primary-button class="w-full justify-center h-10">
                                    <x-heroicon-o-funnel class="w-4 h-4 me-2" />
                                    {{ __('Filter') }}
                                </x-primary-button>
                                <a href="{{ route('transactions.index') }}"
                                    class="inline-flex items-center justify-center w-full h-10 px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    <x-heroicon-o-arrow-path class="w-4 h-4 me-2" />
                                    {{ __('Reset') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-end mb-4">
                        <a href="{{ route('transactions.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            <x-heroicon-o-plus class="w-4 h-4 me-2"/>
                            {{ __('Add Transaction') }}
                        </a>
                    </div>

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center">Date</th>
                                    <th scope="col" class="px-6 py-3 text-center">Category</th>
                                    <th scope="col" class="px-6 py-3 text-center">Description</th>
                                    <th scope="col" class="px-6 py-3 text-center">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4 text-center">
                                            {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">
                                            {{ $transaction->category->name }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            {{ $transaction->description ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center font-medium
                                            @if ($transaction->category->type == 'income')
                                                text-green-600 dark:text-green-400
                                            @else
                                                text-red-600 dark:text-red-400
                                            @endif
                                        ">
                                            Rp. {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 flex items-center justify-center space-x-3">
                                            <a href="{{ route('transactions.show', $transaction->id) }}" class="font-medium text-yellow-600 dark:text-yellow-400 hover:underline">
                                                <x-heroicon-o-eye class="w-5 h-5"/>
                                            </a>
                                            <a href="{{ route('transactions.edit', $transaction->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                                <x-heroicon-o-pencil-square class="w-5 h-5"/>
                                            </a>
                                            <form method="POST" action="{{ route('transactions.destroy', $transaction->id) }}" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline">
                                                    <x-heroicon-o-trash class="w-5 h-5" />
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="5" class="px-6 py-4 text-center">
                                            No transactions found matching your filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $transactions->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
