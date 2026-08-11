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
        Schema::create('occupation_employee',function(Blueprint $table){
            $table->id();
            $table->foreignId('employee_id')->references('id')->on('employees');
            $table->foreignId('occupation_id')->references('id')->on('occupations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occupation_employee');
    }
};
