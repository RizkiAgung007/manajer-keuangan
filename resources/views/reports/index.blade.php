<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Monthly Report') }} - {{ $monthName }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="GET" action="{{ route('reports.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="year" :value="__('Year')" />
                                <select name="year" id="year" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm">
                                    @php
                                        $currentYear = date('Y');
                                        $statYear = $currentYear - 5;
                                    @endphp
                                    @for ($year = $currentYear; $year >= $statYear; $year--)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <x-input-label for="month" :value="__('Month')" />
                                <select name="month" id="month" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm">
                                    @for ($month = 1; $month <= 12; $month++)
                                        <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="flex flex-col justify-end gap-4">
                                <x-primary-button class="w-full justify-center h-10">
                                    <x-heroicon-o-funnel class="w-4 h-4 me-2" />
                                    {{ __('Filter') }}
                                </x-primary-button>
                                <a href="{{ route('reports.index') }}"
                                    class="inline-flex items-center justify-center w-full h-10 px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    <x-heroicon-o-arrow-path class="w-4 h-4 me-2" />
                                    {{ __('Reset') }}
                                </a>
                            </div>
                        </div>
                    </form>
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
                    <h3 class="text-lg font-medium mb-4">Budget vs. Actual Spending</h3>
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Category</th>
                                    <th scope="col" class="px-6 py-3 text-center">Budget</th>
                                    <th scope="col" class="px-6 py-3 text-center">Spent</th>
                                    <th scope="col" class="px-6 py-3 text-center">Remaining</th>
                                    <th scope="col" class="px-6 py-3 text-center">Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($budgetComparison as $item)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $item['category_name'] }}
                                        </th>
                                        <td class="px-6 py-4 text-center">
                                            Rp {{ number_format($item['budget_amount'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            Rp {{ number_format($item['actual_amount'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center font-medium
                                            @if ($item['difference'] < 0)
                                                text-red-600 dark:text-red-400
                                            @else
                                                text-green-600 dark:text-green-400
                                            @endif
                                        ">
                                            Rp {{ number_format($item['difference'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($item['budget_amount'] > 0)
                                                @php
                                                    $percentage = $item['percentage'];
                                                    $badgeColor = '';
                                                    if ($percentage > 100) {
                                                        $badgeColor = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
                                                    } elseif ($percentage >= 80) {
                                                        $badgeColor = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
                                                    } else {
                                                        $badgeColor = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
                                                    }
                                                @endphp
                                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $badgeColor }}">
                                                    {{ $item['percentage'] }}% Used
                                                </span>
                                            @else
                                                <span class="text-xs italic text-gray-500">No Budget Set</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="5" class="px-6 py-4 text-center">
                                            No budgets set or expenses made for this period.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">All Transactions ({{ $monthName }})</h3>

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Date</th>
                                    <th scope="col" class="px-6 py-3">Category</th>
                                    <th scope="col" class="px-6 py-3">Description</th>
                                    <th scope="col" class="px-6 py-3 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4">
                                            {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $transaction->category->name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $transaction->description ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium
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
                                        <td colspan="4" class="px-6 py-4 text-center">
                                            No transactions found for this period.
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
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Expense',
                                    formatter: (w) => 'Rp ' + new Intl.NumberFormat('id-ID').format(w.globals.seriesTotals.reduce((a, b) => a + b, 0))
                                }
                            }
                        }
                    }
                },
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
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Income',
                                    formatter: (w) => 'Rp ' + new Intl.NumberFormat('id-ID').format(w.globals.seriesTotals.reduce((a, b) => a + b, 0))
                                }
                            }
                        }
                    }
                },
                tooltip: { y: { formatter: (val) => "Rp " + new Intl.NumberFormat('id-ID').format(val) }},
                responsive: [{ breakpoint: 480, options: { chart: { width: '100%' }, legend: { position: 'bottom' }}}]
            };
            incomeChartInstance = new ApexCharts(document.querySelector("#incomeChart"), options);
            incomeChartInstance.render();
        }

        window.addEventListener('dark-mode-toggled', (e) => {
            const newMode = e.detail.isDarkMode ? 'dark' : 'light';
            if (expenseChartInstance) {
                expenseChartInstance.updateOptions({ theme: { mode: newMode } });
            }
            if (incomeChartInstance) {
                incomeChartInstance.updateOptions({ theme: { mode: newMode } });
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts" onload="initCharts()"></script>

    @endpush
</x-app-layout>
