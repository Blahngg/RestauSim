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
        Schema::create('item_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('menu_item_id')->constrained('menu_items');
            $table->enum('status', ['pending', 'preparing', 'completed', 'served', 'cancelled'])->default('pending');
            $table->enum('customer_type', ['regular', 'senior', 'pwd'])->default('regular');
            $table->integer('quantity');
            $table->decimal('unit_price', 8, 2);
            $table->enum('discount_type', ['percent', 'fixed'])->nullable();
            $table->decimal('discount_value', 8, 2);
            $table->decimal('discount_amount', 8, 2);
            $table->decimal('vat_rate', 8, 2);
            $table->decimal('subtotal', 8, 2);
            $table->decimal('total', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_orders');
    }
};
