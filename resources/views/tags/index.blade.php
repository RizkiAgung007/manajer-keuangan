<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tags') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100" x-data="sortableTags()">

                    <div class="flex justify-end mb-4">
                        <a href="{{ route('tags.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            <x-heroicon-o-plus class="w-4 h-4 me-2"/>
                            {{ __('Add New Tag') }}
                        </a>
                    </div>

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3 w-12"></th> <th scope="col" class="px-6 py-3 text-left">Tag Name</th>
                                    <th scope="col" class="px-6 py-3 text-center">Times Used</th>
                                    <th scope="col" class="px-6 py-3 text-center">Spent This Month</th>
                                    <th scope="col" class="px-6 py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody x-ref="sortableList">
                                @forelse ($tags as $tag)
                                    <tr data-id="{{ $tag->id }}" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4 cursor-move text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                            <x-heroicon-o-bars-3 class="w-5 h-5" />
                                        </td>

                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-left">
                                            {{ $tag->name }}
                                        </th>

                                        <td class="px-6 py-4 text-center">
                                            {{ $tag->transactions_count }}
                                        </td>
                                        <td class="px-6 py-4 text-center font-medium text-red-600 dark:text-red-400">
                                            Rp {{ number_format($tag->spent_this_month ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 flex items-center justify-center space-x-3">
                                            <a href="{{ route('tags.edit', $tag->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                                <x-heroicon-o-pencil-square class="w-5 h-5"/>
                                            </a>
                                            <form method="POST" action="{{ route('tags.destroy', $tag->id) }}" onsubmit="return confirm('Are you sure? This will not delete transactions, only remove the tag from them.');">
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
                                        <td colspan="5" class="px-6 py-4 text-center"> No tags found.
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

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        function sortableTags() {
            return {
                init() {
                    if (!this.$refs.sortableList) return;

                    Sortable.create(this.$refs.sortableList, {
                        animation: 150,
                        handle: '.cursor-move',

                        onEnd: (evt) => {
                            const rows = evt.target.children;
                            let newOrder = Array.from(rows).map(row => {
                                return row.dataset.id;
                            });
                            this.updateOrder(newOrder);
                        }
                    });
                },

                updateOrder(order) {
                    fetch('{{ route("tags.updateOrder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order: order })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            console.log('Tag order updated!');
                        }
                    })
                    .catch(error => {
                        console.error('Error updating tag order:', error);
                    });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
