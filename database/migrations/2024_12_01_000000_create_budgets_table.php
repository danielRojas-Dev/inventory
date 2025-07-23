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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id');
            $table->string('budget_date');
            $table->enum('budget_status', ['Pendiente', 'Convertido', 'Vencido', 'Cancelado'])->default('Pendiente');
            $table->integer('total_products');
            $table->string('budget_no')->unique();
            $table->string('total')->nullable();
            $table->enum('payment_method', ['EFECTIVO', 'TRANSFERENCIA', 'DEBITO', 'CUOTAS'])->nullable();
            $table->string('quotas')->nullable();
            $table->string('interest_plan')->nullable();
            $table->date('valid_until')->nullable(); // Fecha de validez del presupuesto
            $table->string('employee_id');
            $table->string('converted_order_id')->nullable(); // ID de la orden cuando se convierte
            $table->timestamp('converted_at')->nullable(); // Fecha de conversión
            $table->text('notes')->nullable(); // Notas adicionales del presupuesto
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
