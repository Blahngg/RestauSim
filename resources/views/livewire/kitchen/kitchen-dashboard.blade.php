<div>
    This is the kitchen dashboard
    
    <div class="grid grid-cols-3 gap-3 px-3">
        @foreach ($orders as $table => $order)
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg h-80 dark:bg-gray-800">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                {{ $table }}
                            </th>
                            <th scope="col" class="px-6 py-3">
                                
                            </th>
                            <th scope="col" class="px-6 py-3">
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order as $index => $item)
                            @if($item['status'] !== 'completed')
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 h-full flex justify-center align-middle">
                                        {{ $item['quantity'] }}
                                    </td>
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">{{ $item['item_name'] }}</h2>
                                        <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400">
                                            @foreach ($item['customizations'] as $custom)
                                                <li>
                                                    {{ $custom['name'] }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </th>
                                    <td class="px-6 py-4 h-full flex justify-center align-middle">
                                        @if ($item['status'] === 'pending')
                                            <button type="button" 
                                                class="py-2.5 px-5 mr-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                                wire:click="updateStatus('{{ $item['item_id'] }}', '{{ $item['status'] }}', '{{ $table }}', '{{ $index }}')">
                                                    Start
                                            </button>
                                            <button 
                                                type="button" 
                                                class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                                                wire:click="cancelItem('{{ $item['item_id'] }}', '{{ $table }}')">
                                                    Cancle
                                            </button>
                                        @elseif ($item['status'] === 'preparing')
                                            <button type="button" 
                                                class="py-2.5 px-5 mr-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                                wire:click="updateStatus('{{ $item['item_id'] }}', '{{ $item['status'] }}', '{{ $table }}', '{{ $index }}')">
                                                    Finish
                                            </button>
                                            <button 
                                                type="button" 
                                                class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                                                wire:click="cancelItem('{{ $item['item_id'] }}', '{{ $table }}')">
                                                    Cancel
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>

</div>
