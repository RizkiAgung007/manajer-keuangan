<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Transaction Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 md:p-8 text-center border-b border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        @if ($transaction->category->type == 'income')
                            Income Received
                        @else
                            Expense Paid
                        @endif
                    </p>
                    <h1 class="mt-2 text-5xl font-extrabold tracking-tight
                        @if ($transaction->category->type == 'income')
                            text-green-600 dark:text-green-400
                        @else
                            text-red-600 dark:text-red-400
                        @endif
                    ">
                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                    </h1>
                    <p class="mt-4 text-2xl font-medium text-gray-900 dark:text-white">
                        {{ $transaction->description ?? 'Transaction' }}
                    </p>
                    <p class="mt-2 text-base text-gray-500 dark:text-gray-400">
                        on {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('l, d F Y') }}
                    </p>
                </div>

                <div class="p-6 md:p-8 grid grid-cols-3 gap-6">
                    <div class="text-center">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Category
                        </dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ $transaction->category->name }}
                        </dd>
                    </div>

                    @if ($transaction->tags->isNotEmpty())
                        <div class="text-center">
                            <dt class="text-lg font-semibold text-gray-900 dark:text-white">
                                Tags
                            </dt>
                            <dd class="mt-4 flex flex-wrap gap-2 justify-center">
                                @foreach ($transaction->tags as $tag)
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 text-center">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </dd>
                        </div>
                    @endif

                    <div class="text-center">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Type
                        </dt>
                        <dd class="mt-1">
                            @if ($transaction->category->type == 'income')
                                <span class="px-3 py-1 text-sm font-medium text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-200">
                                    Income
                                </span>
                            @else
                                <span class="px-3 py-1 text-sm font-medium text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-200">
                                    Expense
                                </span>
                            @endif
                        </dd>
                    </div>
                </div>

                @if ($transaction->attachment_path)
                <div class="p-6 md:p-8 border-t border-gray-200 dark:border-gray-700">
                    <dt class="text-lg font-semibold text-gray-900 dark:text-white">
                        Attachment
                    </dt>
                    <dd class="mt-4">
                        <a href="{{ asset('storage/' . $transaction->attachment_path) }}" target="_blank">
                            <img src="{{ asset('storage/' . $transaction->attachment_path) }}"
                                 alt="Receipt attachment"
                                 class="w-full rounded-lg border border-gray-200 dark:border-gray-700 hover:opacity-90 transition-opacity">
                        </a>
                    </dd>
                </div>
                @endif
            </div>

            <div class="mt-6 text-center">
                   <a href="{{ route('transactions.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                    <x-heroicon-o-arrow-left class="w-4 h-4 inline-block me-1 align-text-bottom"/>
                    Back to all transactions
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
