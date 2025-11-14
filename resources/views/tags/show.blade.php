<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Tag Detail: ') }} {{ $tag->name }}
            </h2>
            <a href="{{ route('tags.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <x-heroicon-o-arrow-left class="w-4 h-4 me-2"/>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Total Transactions
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                            {{ $tag->transactions_count }}
                        </dd>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Total Spent
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-red-600 dark:text-red-400">
                            Rp {{ number_format($tag->total_spent, 0, ',', '.') }}
                        </dd>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">All Transactions with this Tag</h3>
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
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="5" class="px-6 py-4 text-center">
                                            No transactions found with this tag.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
