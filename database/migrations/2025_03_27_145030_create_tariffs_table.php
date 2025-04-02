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
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('businesses');
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->unsignedBigInteger('tariff_type_id');
            $table->foreign('tariff_type_id')->references('id')->on('tariff_types');
            $table->unsignedBigInteger('period_type_id');
            $table->foreign('period_type_id')->references('id')->on('period_types');
            $table->string('name');
            $table->integer('percentage');
            $table->integer('grace_period_month');
            $table->integer('min_amount');
            $table->integer('max_amount');
            $table->integer('max_period_month');
            $table->integer('min_period_month');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariffs');
    }
};
