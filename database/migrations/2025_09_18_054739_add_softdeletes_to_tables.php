<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tables = [
        'implementations',
        'obligation_requests',
        'payments',
        'procurements',
        'projects',
        'purchase_request_controls',
        'purchase_requests',
        'technical_working_groups',
        'users',
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->softDeletes()->before('created_at');
            });
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
