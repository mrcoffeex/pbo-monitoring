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
        Schema::create('obligation_requests', function (Blueprint $table) {
            $table->id();
            $table->date('controlled_date')->nullable();
            $table->string('number')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('project_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obligation_requests');
    }
};
