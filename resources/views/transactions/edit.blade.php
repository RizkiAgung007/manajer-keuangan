<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('transactions.update', $transaction->id) }}" x-data="{ type: '{{ $transaction->category->type }}' }" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

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

                        <div class="mt-4">
                            <x-input-label for="category_id" :value="__('Category')" />
                            <select name="category_id" id="category_id_edit" x-show="type === 'expense'" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm">
                                <option value="">{{ __('Select an expense category') }}</option>
                                @foreach ($expenseCategories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="category_id_income" id="category_id_income_edit" x-show="type === 'income'" style="display: none;" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm">
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
                            <x-text-input id="description" class="block mt-1 w-full" type="text" name="description" :value="old('description', $transaction->description)" />
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="transaction_date" :value="__('Transaction Date')" />
                            <x-text-input id="transaction_date" class="block mt-1 w-full" type="date" name="transaction_date" :value="old('transaction_date', $transaction->transaction_date)" required />
                            <x-input-error :messages="$errors->get('transaction_date')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label :value="__('Tags (Optional)')" />
                            <div class="mt-2 p-4 border border-gray-300 dark:border-gray-700 rounded-md">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-h-40 overflow-y-auto">
                                    @forelse ($allTags as $tag)
                                        <label for="tag_{{ $tag->id }}" class="inline-flex items-center">
                                            <input id="tag_{{ $tag->id }}"
                                                type="checkbox"
                                                name="tags[]"
                                                value="{{ $tag->id }}"
                                                @if(is_array(old('tags')) && in_array($tag->id, old('tags')))
                                                    checked
                                                @elseif(old('tags') === null && $transactionTags->contains($tag->id))
                                                    checked
                                                @endif
                                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-green-600 shadow-sm focus:ring-green-500 dark:focus:ring-green-600 dark:focus:ring-offset-gray-800">
                                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ $tag->name }}</span>
                                        </label>
                                    @empty
                                        <div class="col-span-full text-sm text-gray-500 italic">
                                            You haven't created any tags yet.
                                            <a href="{{ route('tags.create') }}" class="underline text-green-600">Create one now</a>.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="attachment" :value="__('Attachment')" />

                            @if ($transaction->attachment_path)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $transaction->attachment_path) }}" target="_blank" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                                        View current attachment
                                    </a>
                                    <div class="mt-2">
                                        <label for="delete_attachment" class="inline-flex items-center">
                                            <input id="delete_attachment" name="delete_attachment" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-red-600 shadow-sm focus:ring-red-500 dark:focus:ring-red-600 dark:focus:ring-offset-gray-800">
                                            <span class="ms-2 text-sm text-red-600 dark:text-red-400">{{ __('Delete current attachment') }}</span>
                                        </label>
                                    </div>
                                </div>
                            @endif

                            <x-text-input id="attachment" name="attachment" type="file" class="block mt-2 w-full file:border-0 file:bg-gray-100 file:dark:bg-gray-700 file:text-gray-700 file:dark:text-gray-300 file:px-4 file:py-2 file:rounded-lg file:mr-4 hover:file:bg-gray-200 dark:hover:file:bg-gray-600 cursor-pointer" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Upload new file to replace the old one (Max 2MB).
                            </p>
                            <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('transactions.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800">
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
