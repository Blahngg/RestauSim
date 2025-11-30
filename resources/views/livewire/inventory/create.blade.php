<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Inventory') }}
        </h2>
    </x-slot>

    <button 
        type="button" 
        class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-5 mt-5 ml-5 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
        wire:click="add()">
            Add
    </button>
    <form wire:submit.prevent="store" enctype="multipart/form-data" class="p-5">
        @foreach ($inventories as $index => $inventory)
            {{-- <div class="w-full max-w-sm p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700" wire:key="input-{{ $index }}" >
                <div class="grid md:grid-cols-2 md:gap-6">
                    
                    <x-form.floating-input
                        id="name"
                        label="Name"
                        type="text"
                        wire:model="inventories.{{ $index }}.name"
                    />
                    <x-form.floating-input
                        id="code"
                        label="Code"
                        type="text"
                        wire:model="inventories.{{ $index }}.code"
                    />
                </div>
                <div class="grid md:grid-cols-2 md:gap-6">
                    <x-form.floating-input
                        id="quantity"
                        label="Quantity"
                        type="number"
                        wire:model="inventories.{{ $index }}.quantity"
                    />
                    <div class="relative z-0 w-full mb-5 group">
                        <select id="countries" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model="inventories.{{ $index }}.unit">

                            <option>Unit</option>
                            @foreach ($units as $unitIndex => $unit)
                                <optgroup label="{{ Str::ucfirst($unitIndex) }}">
                                    @foreach ($unit as $unitData)
                                        <option value="{{ $unitData->id }}">{{ Str::ucfirst($unitData->name) }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="relative z-0 w-full mb-5 group">
                    <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category</label>
                    <select id="countries" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model="inventories.{{ $index }}.category">

                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid md:grid-cols-2 md:gap-6">
                    <x-form.floating-input
                        id="price"
                        label="Price"
                        type="number"
                        wire:model="inventories.{{ $index }}.price"
                    />
                    <x-form.floating-input
                        id="par_level"
                        label="Par Level"
                        type="number"
                        wire:model="inventories.{{ $index }}.par_level"
                    />
                </div>
                <button 
                    type="button" 
                    wire:click="remove({{ $index }})"
                    class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                        Remove
                </button>
            </div> --}}
            <div class="flex flex-col items-center p-5 bg-white border border-gray-200 rounded-lg shadow-sm md:flex-row md:max-full dark:border-gray-700 dark:bg-gray-800" wire:key="input-{{ $index }}">
                <div class="flex items-center justify-center w-80">
                    <label for="image-{{ $index }}" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                        @if($inventories[$index]['image'])
                            <img src="{{ $inventories[$index]['image']->temporaryURL() }}" alt="">
                        @else
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                </svg>
                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                            </div>
                        @endif
                        <input id="image-{{ $index }}" type="file" class="hidden"  wire:model="inventories.{{ $index }}.image"/>
                    </label>
                </div> 
                <div class="flex flex-col justify-between p-4 leading-normal w-full">
                    <div class="grid md:grid-cols-2 md:gap-6">
                        
                        <x-form.floating-input
                            id="name"
                            label="Name"
                            type="text"
                            wire:model="inventories.{{ $index }}.name"
                        />
                        <x-form.floating-input
                            id="code"
                            label="Code"
                            type="text"
                            wire:model="inventories.{{ $index }}.code"
                        />
                    </div>
                    <div class="grid md:grid-cols-2 md:gap-6">
                        <x-form.floating-input
                            id="quantity"
                            label="Quantity"
                            type="number"
                            wire:model="inventories.{{ $index }}.quantity"
                        />
                        <div class="relative z-0 w-full mb-5 group">
                            <select id="countries" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model="inventories.{{ $index }}.unit_of_measurement_id">
    
                                <option>Unit</option>
                                @foreach ($units as $unitIndex => $unit)
                                    <optgroup label="{{ Str::ucfirst($unitIndex) }}">
                                        @foreach ($unit as $unitData)
                                            <option value="{{ $unitData->id }}">{{ Str::ucfirst($unitData->name) }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="relative z-0 w-full mb-5 group">
                        <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category</label>
                        <select id="countries" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model="inventories.{{ $index }}.inventory_category_id">
    
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid md:grid-cols-2 md:gap-6">
                        <x-form.floating-input
                            id="price"
                            label="Price"
                            type="number"
                            wire:model="inventories.{{ $index }}.price"
                        />
                        <x-form.floating-input
                            id="par_level"
                            label="Par Level"
                            type="number"
                            wire:model="inventories.{{ $index }}.par_level"
                        />
                    </div>
                </div>
                <button 
                    type="button" 
                    wire:click="remove({{ $index }})"
                    class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                        Remove
                </button>
            </div>
        @endforeach
        

        <button type="submit" class="w-full mt-5 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
    </form>
</div>
