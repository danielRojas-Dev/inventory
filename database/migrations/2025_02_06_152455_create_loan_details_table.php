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
        Schema::create('loan_details', function (Blueprint $table) {
            $table->id();
            $table->string('loan_id');
            $table->integer('number_quota')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('estimated_payment')->nullable();
            $table->string('interest_due')->nullable();
            $table->string('increment_due')->nullable();
            $table->string('total_payment')->nullable();
            $table->date('estimated_payment_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('status_payment')->default('pendiente');
            $table->enum('payment_method', ['EFECTIVO', 'TRANSFERENCIA', 'DEBITO'])->nullable();
            $table->enum('payment_currency', ['PESOS', 'DOLARES'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_details');
    }
};
