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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('patronymic');
            $table->enum('gender',['female','male']);
            $table->string('birthplace');
            $table->date('birthday');
            $table->string('passport_series');
            $table->string('passport_number');
            $table->string('inn')->nullable();
            $table->string('pinfl');
            $table->date('passport_date_issue');
            $table->date('passport_date_expiration');
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
