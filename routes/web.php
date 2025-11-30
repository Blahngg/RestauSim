<?php

use App\Livewire\Inventory\Create;
use App\Livewire\Inventory\InventoryEdit;
use App\Livewire\Inventory\InventoryList;
use App\Livewire\Inventory\InventoryRestock;
use App\Livewire\Menu\MenuCreate;
use App\Livewire\Menu\MenuList;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::prefix('inventory')
    ->middleware(['auth'])
    ->group(function(){

        Route::get('create', Create::class)
            ->name('inventory.create');

        Route::get('/', InventoryList::class)
            ->name('inventory.index');

        Route::get('/{inventory}/edit', InventoryEdit::class)
            ->name('inventory.edit');

        Route::get('/restock', InventoryRestock::class)
            ->name('inventory.restock');
});

Route::prefix('menu')
    ->middleware(['auth'])
    ->group(function() {

        Route::get('create', MenuCreate::class)
            ->name('menu.create');

        Route::get('/', MenuList::class)
            ->name('menu.index');
    });

require __DIR__.'/auth.php';
