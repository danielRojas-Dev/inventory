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
            $table->string('customer_id');
            $table->string('order_date');
            $table->string('order_status');
            $table->integer('total_products');
            $table->string('invoice_no')->nullable();
            $table->string('total')->nullable();
            $table->enum('payment_method', ['EFECTIVO', 'TRANSFERENCIA', 'DEBITO', 'CUOTAS'])->nullable();
            $table->string('pay')->nullable();
            $table->string('quotas')->nullable();
            $table->string('employee_id');

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