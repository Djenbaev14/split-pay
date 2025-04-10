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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->unsignedBigInteger('payment_schedule_id'); 
            $table->foreign('payment_schedule_id')->references('id')->on('payment_schedules');
            $table->decimal('paid_principal_amount', 11, 2); // Asosiy qarz 
            $table->decimal('paid_interest_amount', 11, 2); // Procent qarz 
            $table->decimal('paid_total_amount', 11, 2); // Umumiy to'lov 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
