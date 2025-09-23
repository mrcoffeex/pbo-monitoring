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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payable_reference')->nullable()->after('amount');
            $table->string('payment_reference')->nullable()->after('payable_reference');
            $table->string('check_number')->nullable()->after('payment_reference');
            $table->date('check_date')->nullable()->after('check_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payable_reference', 'payment_reference', 'check_number', 'check_date']);
        });
    }
};
