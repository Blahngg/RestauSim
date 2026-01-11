<div class="m-4 flex">
    <div id="menu">
      <ul class="flex flex-wrap justify-center text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">
            <li class="me-2">
                <div 
                    class="inline-block p-4 rounded-t-lg  cursor-pointer
                            {{ (!$active) ? 'text-blue-600 bg-gray-100 active dark:bg-gray-800 dark:text-blue-500':'hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300' }}"
                    wire:click="switchTabs('')">
                        All
                </div>
            </li>
            @foreach ($categories as $category)
                <li class="me-2">
                    <div 
                        class="inline-block p-4 rounded-t-lg  cursor-pointer
                            {{ ($active == $category->id) ? 'text-blue-600 bg-gray-100 active dark:bg-gray-800 dark:text-blue-500':'hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300' }}"
                        wire:click="switchTabs({{ $category->id }})">
                            {{ $category->name }}
                    </div>
                </li>
            @endforeach
        </ul>
      <div id="data" class=" flex mb-2 mt-10">
        @foreach ($menuItems as $item)
            <div class="mr-3" wire:click="openCustomizationModal({{ $item->id }})">
                <div class="w-40 bg-white border border-gray-200 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-700 flex flex-col overflow-hidden">
                    <div>
                        <img class="h-28 w-full object-cover" src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" />
                    </div>
                    <div class="relative p-2 flex-1 flex flex-col">
                        <h5 class="mb-1 text-sm font-semibold tracking-tight text-gray-900 dark:text-white truncate">
                            {{ $item->name }}
                        </h5>
                    </div>
                </div>
            </div>
        @endforeach
      </div>
    </div>
    <aside id="separator-sidebar" class="fixed top-0 mt-16 right-0 z-40 w-1/3 h-screen transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">
        <div class="h-full px-3 py-4 overflow-y-auto bg-gray-50 dark:bg-gray-800">
            <ul class="space-y-2 font-medium text-white">
                @foreach ($orders as $index => $order)
                    <li class="flex justify-between">
                        <ul>
                            <li class=" font-bold ml-3 text-xl">{{ $order['menu_name']}}</li>
                            <li>
                                <ul>
                                    @foreach ($order['customizations'] as $custom)
                                        <li class="font-semibold ml-6">
                                            {{ $custom['name'] }}
                                            @if($custom['quantity'] > 0)
                                                {{ $custom['quantity'] }}x
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                        <div>{{ $order['quantity'] }}</div>
                        <div class="flex justify-center">
                            <button 
                                type="button" 
                                class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                                wire:click="removeOrders({{ $index }})">
                                    Remove
                            </button>
                        </div>
                    </li>
                @endforeach
            </ul>
            <ul class="pt-4 mt-4 space-y-2 font-medium border-t border-gray-200 dark:border-gray-700">
                <li>
                    <button 
                        type="button" 
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        wire:click="saveOrders">
                        Save
                    </button>
                </li>
            </ul>
        </div>
    </aside>

    {{-- Customization Modal --}}
    @if ($showCustomizationModal)
        <div id="default-modal" tabindex="-1" aria-hidden="true" class=" overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
                <!-- Modal content -->
                <form wire:submit.prevent="addToOrders" class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ $menu->name }}
                        </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal" wire:click="closeCustomizationModal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4">
                        @foreach ($menu->ingredients as $ingredient)
                            <div>
                                <ul class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                                        <div class="flex items-center ps-3">
                                            <input 
                                                id="{{ $ingredient->id }}" 
                                                type="radio" 
                                                value="default" 
                                                name="{{ $ingredient->id }}" 
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" 
                                                wire:model="ordersToAdd.ingredients.{{ $ingredient->id }}">
                                            <label for="{{ $ingredient->id }}" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                {{ $ingredient->inventory->name }}
                                            </label>
                                        </div>
                                    </li>
                                    @foreach ($menu->customizations as $customization)
                                        @if($customization->ingredient_id == $ingredient->id)
                                            <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                                                <div class="flex items-center ps-3">
                                                    <input 
                                                        id="{{ $customization->id }}" 
                                                        type="radio" 
                                                        value="{{ $customization->id }}" 
                                                        name="{{ $ingredient->id }}" 
                                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                        wire:model="ordersToAdd.ingredients.{{ $ingredient->id }}">
                                                    <label 
                                                        for="{{ $customization->id }}" 
                                                        class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                            {{ ($customization->action == 'remove') ? 'No ' . $customization->ingredient->inventory->name : $customization->inventory->name }} 
                                                    </label>
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                        <div>
                            <ul class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @foreach ($menu->customizations as $customization)
                                    @if ($customization->action == 'add')
                                        <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                                            <div class="flex items-center p-3">
                                                <div class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Extra {{ $customization->inventory->name }}
                                                </div>
                                                <div class="relative flex items-center max-w-[8rem]">
                                                    <button 
                                                        type="button"  
                                                        id="decrement-button"
                                                        class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16"/>
                                                            </svg>
                                                    </button>
                                                    <input 
                                                        type="text" 
                                                        id="{{ $customization->id }}" 
                                                        data-input-counter aria-describedby="helper-text-explanation" 
                                                        class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                        wire:model="ordersToAdd.additionalIngredients.{{ $customization->id }}"/>
                                                    <button 
                                                        type="button" 
                                                        id="increment-button" 
                                                        class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
                                                            </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                        <div>
                            <div class="flex items-center p-3">
                                <div class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                    Quantity
                                </div>
                                <div class="relative flex items-center max-w-[8rem]">
                                    <button 
                                        type="button"  
                                        id="decrement-button"
                                        class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16"/>
                                            </svg>
                                    </button>
                                    <input 
                                        type="text" 
                                        id="item-quantity" 
                                        data-input-counter aria-describedby="helper-text-explanation" 
                                        class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        wire:model="ordersToAdd.quantity"/>
                                    <button 
                                        type="button" 
                                        id="increment-button" 
                                        class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
                                            </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button data-modal-hide="default-modal" type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Save</button>
                        <button data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" wire:click="closeCustomizationModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
