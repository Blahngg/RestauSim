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
            $table->integer('quantity_ordered');
            $table->decimal('price_at_sale', 10, 2);
            $table->decimal('vat_rate', 5, 2);
            $table->decimal('vat_removed_amount', 10, 2);
            $table->enum('discount_type', ['none', 'senior', 'pwd', 'promo']);
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->boolean('vat_exempt_due_to_discount')->default(false);
            $table->decimal('net_amount', 10, 2);
            $table->text('notes')->nullable();
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
