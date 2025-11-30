<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Menu') }}
        </h2>
    </x-slot>

    <form wire:submit.prevent="save" class="p-8">
        {{-- MENU INFO --}}
        <div>
            <div class=" p-5 bg-white border border-gray-200 rounded-lg shadow-sm md:flex-row md:max-full dark:border-gray-700 dark:bg-gray-800" wire:key="">
                <h3 class="text-3xl font-bold text-heading dark:text-white text-center">Menu Info</h3>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-96">
                        <label for="image" class="flex flex-col items-center justify-center w-full h-72 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 p-3">
                            @if($image)
                                <img src="{{ $image->temporaryURL() }}" alt="">
                            @else
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                                </div>
                            @endif
                            <input id="image" type="file" class="hidden" wire:model="image"/>
                        </label>
                    </div>
                    <div class="flex flex-col justify-between p-4 leading-normal w-full">
                        <div class="relative z-0 w-1/2 mb-5 group">
                            <x-form.floating-input
                                id="name"
                                label="Name"
                                type="text"
                                wire:model="name"
                            />
                        </div>
                        <div class="relative z-0 w-1/2 mb-5 group">
                            <label for="message" class="block mb-2.5 text-sm font-medium text-heading text-gray-500 dark:text-gray-500">Description</label>
                            <textarea id="message" rows="4" class="dark:bg-gray-800 border border-default-medium text-heading text-sm rounded-xl focus:ring-brand focus:border-brand block w-full p-3.5 shadow-xs placeholder:text-body dark:bg-gray-800 dark:text-white" placeholder="Write your thoughts here..." wire:model="description"></textarea>
                        </div>
                        <div class="relative z-0 w-1/2 mb-5 group">
                            <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category</label>
                            <select id="countries" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model="menu_item_category_id">

                                <option value="">Select Menu Item Category</option>
                                @foreach ($menuItemCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>                                    
                                @endforeach
                            </select>
                        </div>
                        <div class="relative z-0 w-1/2 mb-5 group">
                            <x-form.floating-input
                                id="price"
                                label="Price"
                                type="number"
                                wire:model="price"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- INGREDIENTS --}}
        <div class=" mt-3 p-5 bg-white border border-gray-200 rounded-lg shadow-sm md:flex-row md:max-full dark:border-gray-700 dark:bg-gray-800" wire:key="">
            <h3 class="text-3xl font-bold dark:text-white my-5 text-center">Ingredients</h3>
            <button type="button" 
                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                wire:click="openInventoryModal('base')">
                    Add Ingridient
            </button>
            <ul class="w-full divide-y-8 divide-double">
                @foreach ($ingredients as $index => $ingredient)
                    <li class="pb-3 sm:pb-4">
                        <div>
                            <h4 class="text-2xl font-bold text-heading dark:text-white my-3">Ingridient {{ $index + 1 }}</h4>
                            <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                <div class="shrink-0">
                                    <img class="w-20 h-20 rounded-lg" src="{{ asset('storage/' . $ingredient['image']) }}" alt="Neil image">
                                </div>
                                <div class="flex-1 min-w-0 text-white">
                                    <p class="text-lg font-medium text-heading truncate">
                                        {{ $ingredient['name'] }}
                                    </p>
                                    <p class="text-sm text-gray-400 text-body truncate">
                                        {{ $ingredient['code'] }}
                                    </p>
                                </div>
                                <div class="inline-flex items-center align-middle text-base font-semibold text-heading">
                                    <label for="removable-{{ $index }}" class="inline-flex items-center mb-5 cursor-pointer">
                                        <input id="removable-{{ $index }}" type="checkbox" wire:model="removableIngredients" value="{{ $ingredient['uid'] }}" class="sr-only peer">
                                        <div class="relative w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-soft dark:peer-focus:ring-brand-soft rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-buffer after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-900"></div>
                                        <span class="select-none ms-3 text-sm font-medium text-heading">Large toggle</span>
                                    </label>
                                </div>
                                <div class="inline-flex items-center align-middle text-base font-semibold text-heading">
                                    <div class="flex flex-col items-center align-middle text-gray-400">
                                        <label for="quantity-input" class="block mb-2.5 text-sm font-medium text-heading">Choose quantity:</label>
                                        <div class="relative flex items-center max-w-[9rem] shadow-xs rounded-xl">
                                            <button type="button" 
                                                id="decrement-button" 
                                                class="text-body dark:bg-gray-800 box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-s-xl text-sm px-3 focus:outline-none h-10"
                                                wire:click="decrementQuantity({{ $index }}, 'ingredient')">
                                                    <svg class="w-4 h-4 text-heading" 
                                                        aria-hidden="true" 
                                                        xmlns="http://www.w3.org/2000/svg" 
                                                        width="24" 
                                                        height="24" 
                                                        fill="none" 
                                                        viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/>
                                                    </svg>
                                            </button>
                                            <input type="text" 
                                                id="quantity-input" 
                                                data-input-counter aria-describedby="helper-text-explanation" 
                                                class="border-x-0 h-10 placeholder:text-heading text-center w-full dark:bg-gray-800 border-white border-default-medium py-2.5 placeholder:text-body text-white" 
                                                placeholder=""
                                                wire:model="ingredients.{{ $index }}.quantity" />
                                            <button type="button" 
                                                id="increment-button" 
                                                class="text-body dark:bg-gray-800 box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-e-xl text-sm px-3 focus:outline-none h-10"
                                                wire:click="incrementQuantity({{ $index }}, 'ingredient')">
                                                    <svg class="w-4 h-4 text-heading" 
                                                        aria-hidden="true" 
                                                        xmlns="http://www.w3.org/2000/svg" 
                                                        width="24" 
                                                        height="24" 
                                                        fill="none" 
                                                        viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                                                    </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="relative z-0 group">
                                    <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unit Of Measurement</label>
                                    <select id="countries" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model="ingredients.{{ $index }}.unit_of_measurement_id">
                                            
                                        @foreach ($units as $unitIndex => $unit)
                                            @if ($ingredient['unit_category'] == $unitIndex)
                                                @foreach ($unit as $unitData)
                                                    <option value="{{ $unitData->id }}">{{ Str::ucfirst($unitData->name) }}</option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex flex-col">
                                    <button type="button" 
                                        class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                                        wire:click="openInventoryModal('base', '{{ $ingredient['uid'] }}')">
                                            Add Alternative
                                    </button>
                                    <button type="button" 
                                        class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                                        wire:click="removeIngredient('base', '{{ $ingredient['uid'] }}')">
                                            Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class=" border-t-2">
                            <h4 class="text-2xl font-bold dark:text-white my-4">Alternative Ingredients</h4>
                            <ul class="w-full divide-y divide-default">
                                @foreach ($alternativeIngredients as $alternativeIndex => $alternativeIngredient)
                                    @if($ingredient['uid'] == $alternativeIngredient['ingredient_uid'])
                                        <li class="pb-3 sm:pb-4 ml-10">
                                            <div class="mt-3">
                                                <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                                    <div class="shrink-0">
                                                        <img class="w-14 h-14 rounded-lg" src="{{ asset('storage/'. $alternativeIngredient['image']) }}" alt="Neil image">
                                                    </div>
                                                    <div class="flex-1 min-w-0 text-white">
                                                        <p class="text-sm font-medium text-heading truncate">
                                                            {{ $alternativeIngredient['name'] }}
                                                        </p>
                                                        <p class="text-xs text-gray-400 text-body truncate">
                                                            {{ $alternativeIngredient['code'] }}
                                                        </p>
                                                    </div>
                                                    <div class="inline-flex items-center align-middle text-base font-semibold text-heading">
                                                        <div class="flex flex-col items-center align-middle text-gray-400">
                                                            <label for="quantity-input" class="block mb-2.5 text-sm font-medium text-heading">Choose quantity:</label>
                                                            <div class="relative flex items-center max-w-[9rem] shadow-xs rounded-xl">
                                                                <button type="button" 
                                                                    id="decrement-button" 
                                                                    class="text-body dark:bg-gray-800 box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-s-xl text-sm px-3 focus:outline-none h-10"
                                                                    wire:click="decrementQuantity({{ $alternativeIndex }}, 'alternative')">
                                                                        <svg class="w-4 h-4 text-heading" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">\
                                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/>
                                                                        </svg>
                                                                </button>
                                                                <input type="text" 
                                                                    id="quantity-input" 
                                                                    aria-describedby="helper-text-explanation" 
                                                                    class="border-x-0 h-10 placeholder:text-heading text-center w-full dark:bg-gray-800 border-white border-default-medium py-2.5 placeholder:text-body text-white" 
                                                                    wire:model="alternativeIngredients.{{ $alternativeIndex }}.quantity"/>
                                                                <button type="button" 
                                                                    id="increment-button" 
                                                                    class="text-body dark:bg-gray-800 box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-e-xl text-sm px-3 focus:outline-none h-10"
                                                                    wire:click="incrementQuantity({{ $alternativeIndex }}, 'alternative')">
                                                                        <svg class="w-4 h-4 text-heading" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/></svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="relative z-0 group">
                                                        <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unit Of Measurement</label>
                                                        <select id="countries" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model="alternativeIngredients.{{ $alternativeIndex }}.unit_of_measurement_id">

                                                            @foreach ($units as $unitIndex => $unit)
                                                                @if ($alternativeIngredient['unit_category'] == $unitIndex)
                                                                    @foreach ($unit as $unitData)
                                                                        <option value="{{ $unitData->id }}">{{ Str::ucfirst($unitData->name) }}</option>
                                                                    @endforeach
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <button type="button" 
                                                            class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                                                            wire:click="removeIngredient('base', '{{ $ingredient['uid'] }}', '{{ $alternativeIngredient['uid'] }}')">
                                                                Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        {{-- ADDITIONAL INGREDIENTS --}}
        <div class=" mt-3 p-5 bg-white border border-gray-200 rounded-lg shadow-sm md:flex-row md:max-full dark:border-gray-700 dark:bg-gray-800" wire:key="">
            <h3 class="text-3xl font-bold dark:text-white my-5 text-center">Additional Ingredients</h3>
            <button type="button" 
                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                wire:click="openInventoryModal('additional')">
                    Add Ingridient
            </button>
            <ul class="w-full divide-y-8 divide-double">
                @foreach ($additionalIngredients as $additionalIndex => $additionalIngredient)
                    <li class="pb-3 sm:pb-4">
                        <div>
                            <h4 class="text-2xl font-bold text-heading dark:text-white my-3">Ingridient {{ $additionalIndex + 1 }}</h4>
                            <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                <div class="shrink-0">
                                    <img class="w-20 h-20 rounded-lg" src="{{ asset('storage/' . $additionalIngredient['image']) }}" alt="Neil image">
                                </div>
                                <div class="flex-1 min-w-0 text-white">
                                    <p class="text-lg font-medium text-heading truncate">
                                        {{ $additionalIngredient['name'] }}
                                    </p>
                                    <p class="text-sm text-gray-400 text-body truncate">
                                        {{ $additionalIngredient['code'] }}
                                    </p>
                                </div>
                                <div class="inline-flex items-center align-middle text-base font-semibold text-heading">
                                    <div class="flex flex-col items-center align-middle text-gray-400">
                                        <label for="quantity-input" class="block mb-2.5 text-sm font-medium text-heading">Choose quantity:</label>
                                        <div class="relative flex items-center max-w-[9rem] shadow-xs rounded-xl">
                                            <button type="button" 
                                                id="decrement-button" 
                                                class="text-body dark:bg-gray-800 box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-s-xl text-sm px-3 focus:outline-none h-10"
                                                wire:click="decrementQuantity({{ $additionalIndex }}, 'additional')">
                                                    <svg class="w-4 h-4 text-heading" 
                                                        aria-hidden="true" 
                                                        xmlns="http://www.w3.org/2000/svg" 
                                                        width="24" 
                                                        height="24" 
                                                        fill="none" 
                                                        viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/>
                                                    </svg>
                                            </button>
                                            <input type="text" 
                                                id="quantity-input" 
                                                data-input-counter aria-describedby="helper-text-explanation" 
                                                class="border-x-0 h-10 placeholder:text-heading text-center w-full dark:bg-gray-800 border-white border-default-medium py-2.5 placeholder:text-body text-white" 
                                                placeholder=""
                                                wire:model="additionalIngredients.{{ $additionalIndex }}.quantity" />
                                            <button type="button" 
                                                id="increment-button" 
                                                class="text-body dark:bg-gray-800 box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-e-xl text-sm px-3 focus:outline-none h-10"
                                                wire:click="incrementQuantity({{ $additionalIndex }}, 'additional')">
                                                    <svg class="w-4 h-4 text-heading" 
                                                        aria-hidden="true" 
                                                        xmlns="http://www.w3.org/2000/svg" 
                                                        width="24" 
                                                        height="24" 
                                                        fill="none" 
                                                        viewBox="0 0 24 24">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                                                    </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="relative z-0 group">
                                    <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unit Of Measurement</label>
                                    <select id="countries" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model="additionalIngredients.{{ $additionalIndex }}.unit_of_measurement_id">
                                            
                                        @foreach ($units as $unitIndex => $unit)
                                            @if ($additionalIngredient['unit_category'] == $unitIndex)
                                                @foreach ($unit as $unitData)
                                                    <option value="{{ $unitData->id }}">{{ Str::ucfirst($unitData->name) }}</option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex flex-col">
                                    <button type="button" 
                                        class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                                        wire:click="removeIngredient('additional', '{{ $additionalIngredient['uid'] }}')">
                                            Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        <button type="submit" class="w-full mt-5 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
    </form>
    @if($errors->any())
        <ul class="text-white">
            @foreach ($errors->all() as $message)
                <li>{{ $message }}</li>            
            @endforeach
        </ul>
    @endif

    @if ($showInventoryModal)
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
                            wire:click="closeInventoryModal">
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
                                    wire:click="addIngredient({{ $inventory->id }})">
                                        <div class="w-28 bg-white rounded-md shadow-sm dark:bg-gray-800 flex flex-col overflow-hidden border-gray-200 dark:border-gray-700 border">
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
</div>

{{-- <div class="flex flex-col items-center p-5 bg-white border border-gray-200 rounded-lg shadow-sm md:flex-row md:max-full dark:border-gray-700 dark:bg-gray-800" wire:key="">
    <div class="flex flex-col justify-between p-4 leading-normal w-full">
        <div class="grid md:grid-cols-2 md:gap-6">
            
            
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="flex flex-col items-center align-middle text-white">
                <label for="quantity-input" class="block mb-2.5 text-sm font-medium text-heading">Choose quantity:</label>
                <div class="relative flex items-center max-w-[9rem] shadow-xs rounded-xl">
                    <button type="button" id="decrement-button" data-input-counter-decrement="quantity-input" class="text-body dark:bg-gray-800 box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-s-xl text-sm px-3 focus:outline-none h-10">
                        <svg class="w-4 h-4 text-heading" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                    </button>
                    <input type="text" id="quantity-input" data-input-counter aria-describedby="helper-text-explanation" class="border-x-0 h-10 placeholder:text-heading text-center w-full dark:bg-gray-800 border-white border-default-medium py-2.5 placeholder:text-body" placeholder="" required />
                    <button type="button" id="increment-button" data-input-counter-increment="quantity-input" class="text-body dark:bg-gray-800 box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-e-xl text-sm px-3 focus:outline-none h-10">
                        <svg class="w-4 h-4 text-heading" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/></svg>
                    </button>
                </div>
            </div>

            <x-form.floating-input
                id="code"
                label="Code"
                type="text"
            />
        </div>
    </div>
</div> --}}
