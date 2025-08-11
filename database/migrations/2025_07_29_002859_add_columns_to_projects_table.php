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
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('center_id')->after('id');
            $table->string('name')->after('center_id');
            $table->string('source')->nullable()->after('name');
            $table->string('appropriation')->nullable()->after('source');
            $table->string('allotment')->nullable()->after('appropriation');
            $table->string('contractor')->nullable()->after('allotment');
            $table->decimal('contract_amount', 15, 2)->nullable()->after('contractor');
            $table->foreign('center_id')->references('id')->on('centers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['center_id']);
            $table->dropColumn([
                'center_id',
                'name',
                'source',
                'appropriation',
                'allotment',
                'contractor',
                'contract_amount',
            ]);
        });
    }
};
