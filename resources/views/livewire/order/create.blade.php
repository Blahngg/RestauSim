<div class="m-4 flex">
    <div class=" w-full h-[85vh] grid grid-cols-[6fr_4fr] gap-5 ">
        {{-- MENU ITEMS --}}
        <div id="menu" class="dark:bg-gray-800 p-5">
            {{-- CATEGORIES --}}
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
            {{-- MENU ITEMS --}}
            <div id="data" class=" flex mb-2 mt-10">
                @foreach ($menuItems as $item)
                    <div class="mr-3 cursor-pointer" wire:click="openCustomizationModal({{ $item->id }})">
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
        {{-- ORDERS --}}
        <aside id="separator-sidebar" class="transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">
            <div class="h-full px-3 py-4 overflow-y-auto bg-gray-50 dark:bg-gray-800 grid grid-rows-[1fr_5fr_3fr]">
                {{-- HEAD --}}
                <div class="pt-3 pl-3 border-b border-gray-200 dark:border-gray-700">
                    <h5 class="text-xl font-bold dark:text-white">Table 1</h5>
                </div>
                {{-- CURRENT ORDERS --}}
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <tbody>
                            @foreach ($orders as $index => $order)
                                <tr 
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $order['quantity'] }}
                                    </th>
                                    <td class="px-6 py-4">
                                        <h6 class="text-lg font-bold dark:text-white">{{ $order['menu_name']}}</h6>
                                        <ul>
                                            @foreach ($order['customizations'] as $custom)
                                                <li class="font-semibold ml-3 list-disc">
                                                    {{ $custom['name'] }}
                                                    @if($custom['quantity'] > 0)
                                                        {{ $custom['quantity'] }}x
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4">
                                        ₱{{ $order['price'] }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <button 
                                            type="button" 
                                            class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-2.5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                                            wire:click="removeOrders({{ $index }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- TOTAL PRICE --}}
                <div class="pt-4 mt-4 space-y-2 grid grid-rows-[4fr_3fr] font-medium border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between px-5">
                        <h3 class="text-3xl font-bold dark:text-white">Total:</h3>
                        <h3 class="text-3xl font-bold dark:text-white">12312</h3>
                    </div>
                    <div class="flex justify-between">
                        <button 
                            type="button" 
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            wire:click="saveOrders">
                                Send to Kitchen
                        </button>

                        <button 
                            type="button" 
                            class="text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                Checkout
                        </button>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    {{-- Customization Modal --}}
    @if ($showCustomizationModal)
        @teleport('body')
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
                    <div class="p-4 md:p-5 space-y-4 h-96 overflow-auto">
                        <div class="space-y-4">
                            <h6 class="text-lg font-bold dark:text-white">Ingredients: </h6>
                            @foreach ($menu->ingredients as $ingredient)
                                <div class="space-y-4">
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
                        </div>
                        <div class="space-y-4">
                            <h6 class="text-lg font-bold dark:text-white">Add-ons</h6>
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
                                                        class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none"
                                                        wire:click="decrementAdditionalIngredient({{ $customization->id }})">
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
                                                        class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none"
                                                        wire:click="incrementAdditionalIngredient({{ $customization->id }})">
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
                    </div>
                    <!-- Modal footer -->
                    <div class="flex justify-between items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <div>
                            <div class="flex items-center gap-3 p-3">
                                <div class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                    Quantity:
                                </div>
                                <div class="relative flex items-center max-w-[8rem]">
                                    <button 
                                        type="button"  
                                        id="decrement-button"
                                        class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none"
                                        wire:click="decrementOrderQuantity()">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16"/>
                                            </svg>
                                    </button>
                                    <input 
                                        type="text" 
                                        id="item-quantity" 
                                        data-input-counter aria-describedby="helper-text-explanation" 
                                        class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-16 py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        wire:model="ordersToAdd.quantity"/>
                                    <button 
                                        type="button" 
                                        id="increment-button" 
                                        class="bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 dark:focus:ring-gray-700 focus:ring-2 focus:outline-none"
                                        wire:click="incrementOrderQuantity()">
                                            <svg class="w-3 h-3 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
                                            </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button data-modal-hide="default-modal" type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Save</button>
                            <button data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" wire:click="closeCustomizationModal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endteleport
    @endif

</div>

{{-- DISCOUNT MODAL
@if ($showDiscountModal)
    @teleport('body')
    <div id="default-modal" tabindex="-1" aria-hidden="true" class=" fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <form wire:submit.prevent="saveDiscountData" class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Discounts
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal" wire:click="closeDiscountModal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4 overflow-y-scroll h-96">
                    ITEM DATA
                    <div>
                         <h3 class="mb-1 text-xl font-bold text-gray-900 dark:text-white">Item: {{ $itemDiscount['name'] }}</h3>
                         <p class="text-gray-500 dark:text-gray-400 mb-6">Base Price: {{ $itemDiscount['base_price'] }}</p>
                    </div>
                    <div class="relative z-0 mb-5 group grid grid-cols-2 gap-5">
                        <div >
                            <label for="number-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Discount Amount</label>
                            <input type="number" 
                                id="number-input" 
                                aria-describedby="helper-text-explanation" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                />
                        </div>
                        <div >
                            <label for="number-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Discount Percentage</label>
                            <input type="number" 
                                id="number-input" 
                                aria-describedby="helper-text-explanation" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                />
                        </div>
                    </div>
                    <div class="grid grid-cols-4">
                        <button type="button" class="text-yellow-400 hover:text-white border border-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-yellow-300 dark:text-yellow-300 dark:hover:text-white dark:hover:bg-yellow-400 dark:focus:ring-yellow-900">SNR</button>

                        <button type="button" class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">PWD</button>

                        <button type="button" class="text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">Promo</button>

                        <button type="button" class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900 flex justify-center content-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </div>
                    SUMMARY
                    <div class="d border-t-2 pt-5 text-white">
                        <div>
                            <div>
                                Discount: 
                            </div>
                            <div>
                                VAT Removed:     -₱16.07 
                            </div>
                            <div>
                                Final Price:     ₱107.14
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="default-modal" type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Save</button>
                    <button data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" wire:click="closeDiscountModal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endteleport
@endif --}}