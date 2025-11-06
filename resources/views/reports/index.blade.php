<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Monthly Report') }} - {{ $monthName }}
        </h2>
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
                    chart: { type: 'donut', height: 350 },
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
                    chart: { type: 'donut', height: 350 },
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
