<x-app-layout>
    <x-slot name="header">
        <div x-data class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }} ({{ \Carbon\Carbon::now()->format('F Y') }})
            </h2>

            <x-primary-button x-on:click.prevent="$dispatch('open-modal', 'quick-add-transaction')">
                <x-heroicon-o-plus class="w-4 h-4 me-2"/>
                {{ __('Quick Add') }}
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Total Income
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600 dark:text-green-400">
                            Rp {{ number_format($totalIncome, 0, ',', '.') }}
                        </dd>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Total Expense
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-red-600 dark:text-red-400">
                            Rp {{ number_format($totalExpense, 0, ',', '.') }}
                        </dd>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Net Profit
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                            Rp {{ number_format($netProfit, 0, ',', '.') }}
                        </dd>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium mb-4">Income by Category</h3>
                        <div id="incomeChart" class="w-full"></div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium mb-4">Expense by Category</h3>
                        <div id="expenseChart" class="w-full"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Latest Transactions</h3>
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Date</th>
                                    <th scope="col" class="px-6 py-3">Category</th>
                                    <th scope="col" class="px-6 py-3">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestTransactions as $transaction)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4">
                                            {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $transaction->category->name }}
                                        </td>
                                        <td class="px-6 py-4 font-medium
                                            @if ($transaction->category->type == 'income')
                                                text-green-600 dark:text-green-400
                                            @else
                                                text-red-600 dark:text-red-400
                                            @endif
                                        ">
                                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="3" class="px-6 py-4 text-center">
                                            No transactions found for this month.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <x-modal name="quick-add-transaction" :show="false" focusable>
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Add New Transaction') }}
                    </h2>

                    <form method="POST" action="{{ route('transactions.store') }}" class="mt-6 space-y-6" x-data="{ type: 'expense' }">
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

                        <div>
                            <x-input-label for="category_id" :value="__('Category')" />
                            <select name="category_id" id="category_id" x-show="type === 'expense'" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm">
                                <option value="">{{ __('Select an expense category') }}</option>
                                @foreach ($expenseCategories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="category_id_income" x-show="type === 'income'" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm">
                                <option value="">{{ __('Select an income category') }}</option>
                                @foreach ($incomeCategories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="amount" :value="__('Amount')" />
                            <x-text-input id="amount" class="block mt-1 w-full" type="number" name="amount" :value="old('amount')" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <x-text-input id="description" class="block mt-1 w-full" type="text" name="description" :value="old('description')" />
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="transaction_date" :value="__('Transaction Date')" />
                            <x-text-input id="transaction_date" class="block mt-1 w-full" type="date" name="transaction_date" :value="old('transaction_date', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('transaction_date')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <x-secondary-button x-on:click="$dispatch('close')">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button class="ms-4">
                                <x-heroicon-o-check-circle class="w-4 h-4 me-2"/>
                                {{ __('Save') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </x-modal>

        </div>
    </div>

    @push('scripts')
        <script>
            let expenseChartInstance = null;
            let incomeChartInstance = null;
            function initCharts() {
                const expenseData = @json($expenseChartData);
                const incomeData = @json($incomeChartData);
                const isDarkMode = document.documentElement.classList.contains('dark');
                renderExpenseChart(expenseData, isDarkMode);
                renderIncomeChart(incomeData, isDarkMode);
            }
            function renderExpenseChart(chartData, isDarkMode) {
                const options = {
                    chart: { type: 'donut', height: 350, background: 'transparent' },
                    series: chartData.series,
                    labels: chartData.labels,
                    noData: { text: 'No expense data found for this month.' },
                    theme: {
                        mode: isDarkMode ? 'dark' : 'light',
                        monochrome: {
                            enabled: true,
                            color: '#ef4444',
                            shadeIntensity: 0.65
                        }
                    },
                    plotOptions: { pie: { donut: { labels: { show: true, total: { show: true, label: 'Total Expense', formatter: (w) => 'Rp ' + new Intl.NumberFormat('id-ID').format(w.globals.seriesTotals.reduce((a, b) => a + b, 0)) }}}}},
                    tooltip: { y: { formatter: (val) => "Rp " + new Intl.NumberFormat('id-ID').format(val) }},
                    responsive: [{ breakpoint: 480, options: { chart: { width: '100%' }, legend: { position: 'bottom' }}}]
                };
                expenseChartInstance = new ApexCharts(document.querySelector("#expenseChart"), options);
                expenseChartInstance.render();
            }
            function renderIncomeChart(chartData, isDarkMode) {
                const options = {
                    chart: { type: 'donut', height: 350, background: 'transparent' },
                    series: chartData.series,
                    labels: chartData.labels,
                    noData: { text: 'No income data found for this month.' },
                    theme: {
                        mode: isDarkMode ? 'dark' : 'light',
                        monochrome: {
                            enabled: true,
                            color: '#16a34a',
                            shadeIntensity: 0.65
                        }
                    },
                    plotOptions: { pie: { donut: { labels: { show: true, total: { show: true, label: 'Total Income', formatter: (w) => 'Rp ' + new Intl.NumberFormat('id-ID').format(w.globals.seriesTotals.reduce((a, b) => a + b, 0)) }}}}},
                    tooltip: { y: { formatter: (val) => "Rp " + new Intl.NumberFormat('id-ID').format(val) }},
                    responsive: [{ breakpoint: 480, options: { chart: { width: '100%' }, legend: { position: 'bottom' }}}]
                };
                incomeChartInstance = new ApexCharts(document.querySelector("#incomeChart"), options);
                incomeChartInstance.render();
            }
            window.addEventListener('dark-mode-toggled', (e) => {
                const newMode = e.detail.isDarkMode ? 'dark' : 'light';
                if (expenseChartInstance) { expenseChartInstance.updateOptions({ theme: { mode: newMode } }); }
                if (incomeChartInstance) { incomeChartInstance.updateOptions({ theme: { mode: newMode } }); }
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts" onload="initCharts()"></script>
    @endpush
</x-app-layout>
