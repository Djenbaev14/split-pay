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
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->date('due_date'); // To‘lov muddati
            $table->decimal('principal_amount',11,2); // Asosiy qarz
            $table->decimal('interest_amount',11,2); // Procent qarz
            $table->decimal('total_amount',11,2); // Umumiy to‘lov (asosiy qarz + foiz qarzi)
            $table->boolean('is_paid')->default(false); // To‘langan yoki yo‘q
            $table->date('paid_at')->nullable(); // To‘langan sana (agar bo‘lsa)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_schedules');
    }
};
