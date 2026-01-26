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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('table_id')->constrained('tables')->nullable();
            $table->enum('type', ['standard', 'priority', 'take-out'])->default('standard');
            $table->enum('status', ['preparing', 'completed', 'cancelled'])->default('preparing');
            $table->decimal('subtotal', 8,2); // 
            $table->decimal('service_charge', 8,2); //
            $table->decimal('discount_total', 8,2); //
            $table->decimal('vat_total', 8,2); //
            $table->decimal('grand_total', 8,2); //
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
