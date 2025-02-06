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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id');
            $table->string('loan_date');
            $table->string('loan_status');
            $table->string('invoice_no')->nullable();
            $table->string('total')->nullable();
            $table->enum('payment_method', ['CUOTAS'])->nullable();
            $table->string('pay')->nullable();
            $table->string('quotas')->nullable();
            $table->string('interest_plan')->nullable();
            $table->string('employee_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
