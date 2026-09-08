<?php

use App\Models\ProjectTypeWorkflow;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_type_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('project_type')->unique();
            $table->json('stages');
            $table->timestamps();
        });

        foreach (ProjectTypeWorkflow::defaultStagesByType() as $type => $stages) {
            DB::table('project_type_workflows')->insert([
                'project_type' => $type,
                'stages' => json_encode($stages),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_type_workflows');
    }
};
