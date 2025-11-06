<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Transaction Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8"> <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 md:p-8 text-center
                    @if ($transaction->category->type == 'income')
                        bg-green-50 dark:bg-green-900/50 border-b border-green-200 dark:border-green-800
                    @else
                        bg-red-50 dark:bg-red-900/50 border-b border-red-200 dark:border-red-800
                    @endif
                ">
                    <dt class="text-sm font-medium uppercase tracking-wider
                        @if ($transaction->category->type == 'income')
                            text-green-700 dark:text-green-400
                        @else
                            text-red-700 dark:text-red-400
                        @endif
                    ">
                        {{ $transaction->category->type }}
                    </dt>

                    <dd class="mt-2 text-5xl font-extrabold tracking-tight
                        @if ($transaction->category->type == 'income')
                            text-green-600 dark:text-green-300
                        @else
                            text-red-600 dark:text-red-300
                        @endif
                    ">
                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                    </dd>

                    <p class="mt-4 text-lg font-medium text-gray-700 dark:text-gray-300">
                       Untuk: <strong class="text-gray-900 dark:text-gray-100">{{ $transaction->category->name }}</strong>
                    </p>
                </div>

                <div class="p-6 md:p-8 space-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Date
                        </dt>
                        <dd class="mt-1 text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d F Y') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Description
                        </dt>
                        <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">
                            {{ $transaction->description ?? '-' }}
                        </dd>
                    </div>
                </div>

            </div> <div class="mt-6 text-center">
                <a href="{{ route('transactions.index') }}" class="text-sm font-medium text-gray-200 dark:text-gray-700 bg-gray-700 dark:bg-gray-200 p-4 rounded-lg">
                    <x-heroicon-o-arrow-left class="w-4 h-4 inline-block me-1 align-text-bottom"/>
                    Back to all transactions
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
