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
        Schema::create('books', function (Blueprint $table) 
        {
            $table->id();
            $table->string('title');
            $table->integer('page_count');
            $table->unsignedSmallInteger('publishing_year');
            $table->string('author');
            $table->string('edition');
            $table->integer('number_of_copies');
            $table->string('image');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
