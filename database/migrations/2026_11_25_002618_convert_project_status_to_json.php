<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('projects')
            ->select(['id', 'status'])
            ->orderBy('id')
            ->chunkById(100, function ($projects): void {
                foreach ($projects as $project) {
                    if ($project->status === null) {
                        continue;
                    }

                    $decodedStatus = json_decode((string) $project->status, true);
                    $statuses = match (true) {
                        json_last_error() !== JSON_ERROR_NONE => [(string) $project->status],
                        is_array($decodedStatus) => array_values(array_filter($decodedStatus, 'is_string')),
                        is_string($decodedStatus) && filled($decodedStatus) => [$decodedStatus],
                        default => [],
                    };

                    DB::table('projects')
                        ->where('id', $project->id)
                        ->update(['status' => json_encode($statuses, JSON_THROW_ON_ERROR)]);
                }
            });

        Schema::table('projects', function (Blueprint $table) {
            $table->json('status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('status')->nullable()->change();
        });

        DB::table('projects')
            ->select(['id', 'status'])
            ->orderBy('id')
            ->chunkById(100, function ($projects): void {
                foreach ($projects as $project) {
                    if ($project->status === null) {
                        continue;
                    }

                    $decodedStatus = json_decode((string) $project->status, true);
                    $status = is_array($decodedStatus)
                        ? ($decodedStatus[0] ?? null)
                        : (is_string($decodedStatus) ? $decodedStatus : null);

                    DB::table('projects')
                        ->where('id', $project->id)
                        ->update(['status' => $status]);
                }
            });
    }
};
