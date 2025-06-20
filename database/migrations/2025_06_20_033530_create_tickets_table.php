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
        Schema::create('tickets', function (Blueprint $table) {
           $table->char('id', 26)->primary();
            $table->string('subject');
            $table->text('body');
            $table->enum('status', ['open', 'in_progress', 'resolved'])->default('open');
            $table->string('category')->nullable(); 
            $table->text('note')->nullable();
            $table->text('explanation')->nullable(); 
            $table->float('confidence')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
