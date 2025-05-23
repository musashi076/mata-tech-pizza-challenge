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
        Schema::create('pizzas', function (Blueprint $table) {
            $table->string('pizza_id')->primary();
            $table->string('pizza_type_id');
            
            $table->string('size', 10);
            $table->decimal('price', 6, 2);
            
            $table->foreign('pizza_type_id')
                ->references('pizza_type_id')
                ->on('pizza_types')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pizzas');
    }
};
