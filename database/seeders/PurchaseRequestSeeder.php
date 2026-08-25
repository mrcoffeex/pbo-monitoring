<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\PurchaseRequest;
use Database\Seeders\Concerns\SeedsProjectProcess;
use Database\Seeders\Support\ProjectSeedData;
use Illuminate\Database\Seeder;

class PurchaseRequestSeeder extends Seeder
{
    use SeedsProjectProcess;

    public function run(): void
    {
        $this->forEachProject(function (Project $project, int $userId, array $timeline): void {
            if ($project->purchase_requests()->exists()) {
                return;
            }

            $record = PurchaseRequest::factory()->create([
                'project_id' => $project->id,
                'user_id' => $userId,
                'received_date' => $timeline['pr_received']->format('Y-m-d H:i:s'),
                'pr_number' => ProjectSeedData::reference('PR', $project),
                'remarks' => 'Purchase request received with complete supporting documents.',
                'forward_twg_date' => $timeline['pr_forward_twg']->format('Y-m-d H:i:s'),
            ]);

            $this->stamp($record, $timeline['pr_received']);
        });
    }
}
