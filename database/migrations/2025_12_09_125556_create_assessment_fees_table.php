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
        Schema::create('assessment_fees', function (Blueprint $table) {
            $table->id();
            $table->string('charge_description');
            $table->string('course')->nullable(); // Course field (optional)
            $table->decimal('amount', 10, 2);
            $table->integer('order')->default(0); // For ordering the fees
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_fees');
    }
};
