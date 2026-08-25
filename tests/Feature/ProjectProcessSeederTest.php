<?php

use App\Models\Implementation;
use App\Models\ObligationRequest;
use App\Models\Payment;
use App\Models\PreProcurement;
use App\Models\Procurement;
use App\Models\ProcurementControl;
use App\Models\Project;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestControl;
use App\Models\TechnicalWorkingGroup;
use App\Models\User;
use Database\Seeders\ImplementationSeeder;
use Database\Seeders\ObligationRequestSeeder;
use Database\Seeders\PaymentSeeder;
use Database\Seeders\PreProcurementSeeder;
use Database\Seeders\ProcurementControlSeeder;
use Database\Seeders\ProcurementSeeder;
use Database\Seeders\ProjectActivitySeeder;
use Database\Seeders\PurchaseRequestControlSeeder;
use Database\Seeders\PurchaseRequestSeeder;
use Database\Seeders\TechnicalWorkingGroupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;

uses(RefreshDatabase::class);

function seedProjectProcesses(): Project
{
    $user = User::factory()->create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
    ]);

    $project = Project::factory()->create([
        'user_id' => $user->id,
        'status' => 'released',
        'year' => (string) now()->year,
        'appropriation' => '2000000.00',
        'allotment' => '1800000.00',
    ]);

    test()->seed([
        PreProcurementSeeder::class,
        PurchaseRequestSeeder::class,
        TechnicalWorkingGroupSeeder::class,
        ProcurementControlSeeder::class,
        PurchaseRequestControlSeeder::class,
        ProcurementSeeder::class,
        ObligationRequestSeeder::class,
        ImplementationSeeder::class,
        PaymentSeeder::class,
        ProjectActivitySeeder::class,
    ]);

    return $project->fresh();
}

it('seeds every project process for existing projects', function () {
    $project = seedProjectProcesses();

    expect($project->pre_procurements)->toHaveCount(1)
        ->and($project->purchase_requests)->toHaveCount(1)
        ->and($project->technical_working_groups)->toHaveCount(1)
        ->and($project->procurement_controls)->toHaveCount(1)
        ->and($project->purchase_request_controls)->toHaveCount(1)
        ->and($project->procurements)->toHaveCount(1)
        ->and($project->obligation_requests)->toHaveCount(1)
        ->and($project->implementations->count())->toBeGreaterThanOrEqual(2)
        ->and($project->payments->count())->toBeGreaterThanOrEqual(2);

    expect(PreProcurement::query()->where('project_id', $project->id)->exists())->toBeTrue()
        ->and(PurchaseRequest::query()->where('project_id', $project->id)->exists())->toBeTrue()
        ->and(TechnicalWorkingGroup::query()->where('project_id', $project->id)->exists())->toBeTrue()
        ->and(ProcurementControl::query()->where('project_id', $project->id)->exists())->toBeTrue()
        ->and(PurchaseRequestControl::query()->where('project_id', $project->id)->exists())->toBeTrue()
        ->and(Procurement::query()->where('project_id', $project->id)->exists())->toBeTrue()
        ->and(ObligationRequest::query()->where('project_id', $project->id)->exists())->toBeTrue()
        ->and(Implementation::query()->where('project_id', $project->id)->exists())->toBeTrue()
        ->and(Payment::query()->where('project_id', $project->id)->exists())->toBeTrue();
});

it('seeds project activity for the project and each process', function () {
    $project = seedProjectProcesses();

    $activities = $project->projectActivitiesQuery()->get();

    expect($activities->count())->toBeGreaterThanOrEqual(10)
        ->and($activities->where('subject_type', Project::class)->isNotEmpty())->toBeTrue()
        ->and($activities->where('subject_type', PreProcurement::class)->isNotEmpty())->toBeTrue()
        ->and($activities->where('subject_type', PurchaseRequest::class)->isNotEmpty())->toBeTrue()
        ->and($activities->where('subject_type', TechnicalWorkingGroup::class)->isNotEmpty())->toBeTrue()
        ->and($activities->where('subject_type', ProcurementControl::class)->isNotEmpty())->toBeTrue()
        ->and($activities->where('subject_type', PurchaseRequestControl::class)->isNotEmpty())->toBeTrue()
        ->and($activities->where('subject_type', Procurement::class)->isNotEmpty())->toBeTrue()
        ->and($activities->where('subject_type', ObligationRequest::class)->isNotEmpty())->toBeTrue()
        ->and($activities->where('subject_type', Implementation::class)->isNotEmpty())->toBeTrue()
        ->and($activities->where('subject_type', Payment::class)->isNotEmpty())->toBeTrue()
        ->and($activities->where('event', 'created')->isNotEmpty())->toBeTrue();

    expect(Activity::query()->where('causer_type', User::class)->exists())->toBeTrue();
});

it('does not duplicate process records when seeders run again', function () {
    $project = seedProjectProcesses();

    $this->seed([
        PreProcurementSeeder::class,
        PurchaseRequestSeeder::class,
        TechnicalWorkingGroupSeeder::class,
        ProcurementControlSeeder::class,
        PurchaseRequestControlSeeder::class,
        ProcurementSeeder::class,
        ObligationRequestSeeder::class,
        ImplementationSeeder::class,
        PaymentSeeder::class,
        ProjectActivitySeeder::class,
    ]);

    expect($project->fresh()->pre_procurements)->toHaveCount(1)
        ->and($project->fresh()->procurements)->toHaveCount(1)
        ->and($project->projectActivitiesQuery()->where('subject_type', Project::class)->where('event', 'created')->count())->toBe(1);
});
