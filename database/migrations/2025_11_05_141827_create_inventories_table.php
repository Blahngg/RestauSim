<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('image');
            $table->decimal('opening_quantity' , 10,3);
            $table->decimal('quantity_on_hand' , 10,3);
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('par_level', 10, 3);
            $table->foreignId('inventory_category_id')->constrained('inventory_categories');
            $table->foreignId('inventory_unit_id')->constrained('unit_of_measurements');
            $table->foreignId('cost_unit_id')->constrained('unit_of_measurements');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
