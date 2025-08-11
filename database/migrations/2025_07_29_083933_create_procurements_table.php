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
        Schema::create('procurements', function (Blueprint $table) {
            $table->id();
            $table->string('ib_number')->nullable();
            $table->date('pre_procurement_conference')->nullable();
            $table->date('pre_bid_conference')->nullable();
            $table->date('bid_opening')->nullable();
            $table->string('ber')->nullable();
            $table->date('post_qua_date')->nullable();
            $table->text('remarks')->nullable();
            $table->date('noa_date_received')->nullable();
            $table->decimal('contract_amount', 15, 2)->nullable();
            $table->string('contractor')->nullable();
            $table->string('ntp_number')->nullable();
            $table->date('ntp_date')->nullable();
            $table->string('contract_duration')->nullable();
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
        Schema::dropIfExists('procurements');
    }
};
