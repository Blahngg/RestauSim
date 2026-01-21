<?php

use App\Http\Controllers\FloorPlanController;
use App\Livewire\FloorPlan\ViewFloorPlan;
use App\Livewire\Inventory\Create;
use App\Livewire\Inventory\InventoryEdit;
use App\Livewire\Inventory\InventoryList;
use App\Livewire\Inventory\InventoryRestock;
use App\Livewire\Kitchen\KitchenDashboard;
use App\Livewire\Menu\MenuCreate;
use App\Livewire\Menu\MenuEdit;
use App\Livewire\Menu\MenuList;
use App\Livewire\Order\Create as OrderCreate;
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

        Route::get('/{menu}/edit', MenuEdit::class)
            ->name('menu.edit');
    });

Route::prefix('floorplan')
    ->middleware(['auth'])
    ->group(function(){

        Route::get('/', ViewFloorPlan::class)
            ->name('floorplan.index');

        Route::get('create', [FloorPlanController::class, 'create'])
            ->name('floorplan.create');
        
        Route::post('store', [FloorPlanController::class, 'store'])
            ->name('floorplan.store');
    });

Route::prefix('order')
    ->middleware(['auth', 'web'])
    ->group(function(){

        Route::get('/{table}/create', OrderCreate::class)
            ->name('order.create');
    });

Route::get('kitchen', KitchenDashboard::class)
    ->middleware(['auth', 'web'])
    ->name('kitchen.dashboard');

require __DIR__.'/auth.php';
