<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Restock Inventory') }}
        </h2>
    </x-slot>

    <button type="button" 
        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
        wire:click="openSelectModal">
            Select Inventory
    </button>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-8">
        <form wire:submit.prevent="restock">
            @if (!empty($selectedInventories))
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-16 py-3">
                                <span class="sr-only">Image</span>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Product
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Cost
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Current Stock
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Stock to Add
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($selectedInventories as $index => $selected)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-900">
                                <td class="p-4">
                                    <img src="{{ asset('storage/' . $selected['image']) }}" class="w-16 md:w-32 max-w-full max-h-full" alt="Apple Watch">
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                    {{ $selected['name'] }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                    {{ $selected['cost_per_unit'] }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $selected['quantity'] }} {{ $selected['unit_of_measurement'] }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="relative z-0 w-full mb-5 group grid grid-cols-2 gap-6">
                                        <x-form.floating-input
                                            id="quantity"
                                            label="Quantity"
                                            type="number"
                                            wire:model="selectedInventories.{{ $index }}.addQuantity"
                                        />
                                        
                                        <select id="countries" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model="selectedInventories.{{ $index }}.addUnitOfMeasurement">
                
                                            @foreach ($units as $unitIndex => $unit)
                                                @if ($selected['unit_category'] == $unitIndex)
                                                    @foreach ($unit as $unitData)
                                                        <option value="{{ $unitData->symbol }}">{{ Str::ucfirst($unitData->name) }}</option>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $inventories->links() }}
                <button type="submit" class="w-full mt-5 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
            @else
                <h2 class="text-4xl font-bold dark:text-white text-center">No Items Selected</h2>
            @endif
        </form>
        @if ($errors->any())
            <div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    @if ($showSelectModal)
        <div id="large-modal" tabindex="-1" class="fixed flex justify-center top-0 left-0 right-0 z-50 w-full p-20 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full cursor-grab select-none scroll-smooth">
            <div class="relative w-full max-w-4xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-800">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                            Inventory
                        </h3>
                        <button type="button" 
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="large-modal"
                            wire:click="closeSelectModal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4">
                        <ul class="dragscroll whitespace-nowrap flex overflow-x-scroll text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">
                            <li class="me-2">
                                <div 
                                    class="inline-block p-4 rounded-t-lg  cursor-pointer
                                            {{ (!$active) ? 'text-blue-600 bg-gray-100 active dark:bg-gray-700 dark:text-blue-500':'hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 dark:hover:text-gray-300' }}"
                                    wire:click="switchTabs('')">
                                        All
                                </div>
                            </li>
                            @foreach ($categories as $category)
                                <li class="me-2">
                                    <div 
                                        class="inline-block p-4 rounded-t-lg  cursor-pointer
                                            {{ ($active == $category->id) ? 'text-blue-600 bg-gray-100 active dark:bg-gray-700 dark:text-blue-500':'hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 dark:hover:text-gray-300' }}"
                                        wire:click="switchTabs({{ $category->id }})">
                                            {{ $category->name }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="flex mb-2">
                            @foreach ($inventories as $inventory)
                                <div class="mr-3 cursor-pointer" 
                                    wire:key="inventory-{{ $inventory->id }}"
                                    wire:click="addInventory({{ $inventory->id }})">
                                        <div class="w-28 bg-white rounded-md shadow-sm dark:bg-gray-800 flex flex-col overflow-hidden
                                        {{ (array_search($inventory->id, array_column($this->selectedInventories, 'id')) !== false) ? 'border-gray-700 dark:border-gray-200 border-2':'border-gray-200 dark:border-gray-700 border' }}">
                                            <div>
                                                <img class="h-24 w-full object-cover" src="{{ asset('storage/' . $inventory->image) }}" alt="" />
                                            </div>
                                            <div class="relative p-2 flex-1 flex flex-col">
                                                <h5 class="mb-1 text-sm font-semibold tracking-tight text-gray-900 dark:text-white truncate">
                                                    {{ $inventory->name }}
                                                </h5>
                                            </div>
                                        </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <script>
        .dragscroll::-webkit-scrollbar {
            display: none;
        }
        .dragscroll {
            -ms-overflow-style: none;  /* IE/Edge */
            scrollbar-width: none;     /* Firefox */
        }
    </script>
</div>
