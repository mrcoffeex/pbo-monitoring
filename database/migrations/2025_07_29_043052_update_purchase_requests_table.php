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
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dateTime('received_date')->nullable()->after('id');
            $table->string('pr_number')->nullable()->after('received_date');
            $table->dateTime('forward_twg_date')->nullable()->after('pr_number');
            $table->unsignedBigInteger('user_id')->nullable()->after('forward_twg_date');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('project_id')->nullable()->after('user_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['project_id']);
            $table->dropColumn(['received_date', 'pr_number', 'forward_twg_date', 'user_id', 'project_id']);
        });
    }
};
