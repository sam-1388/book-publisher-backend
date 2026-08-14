

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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->date('deadline');
            $table->boolean('finished')->default(true);
            $table->foreignId('book_id')->references('id')->on('books')->cascadeOnDelete();
            $table->foreignId('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->integer('page_start')->nullable();
            $table->integer('page_end')->nullable();
            $table->text('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
