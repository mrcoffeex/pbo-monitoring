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
        Schema::create('procurement_controls', function (Blueprint $table) {
            $table->id();
            $table->date('controlled_date')->nullable();
            $table->decimal('abc', 15, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('project_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_controls');
    }
};
