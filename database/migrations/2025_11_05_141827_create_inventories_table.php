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
            $table->string('code');
            $table->string('image');
            $table->float('quantity');
            $table->float('price');
            $table->float('par_level');
            $table->foreignId('inventory_category_id')->constrained('inventory_categories');
            $table->foreignId('unit_of_measurement_id')->constrained('unit_of_measurements');
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
