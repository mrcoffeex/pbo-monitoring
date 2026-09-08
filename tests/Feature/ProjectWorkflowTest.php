<?php

use App\Enums\ProcessStage;
use App\Filament\Resources\ProjectResource\Pages\CreateProject;
use App\Filament\Resources\ProjectResource\Pages\ProjectMonitoring;
use App\Filament\Resources\ProjectTypeWorkflowResource\Pages\ManageProjectTypeWorkflows;
use App\Models\PreProcurement;
use App\Models\Project;
use App\Models\ProjectTypeWorkflow;
use App\Models\PurchaseRequest;
use App\Models\TechnicalWorkingGroup;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    $this->seed(\Database\Seeders\ProjectTypeWorkflowSeeder::class);
});

function makeWorkflowUser(): User
{
    Role::findOrCreate('super_admin');

    foreach (['view_any_project', 'view_project', 'create_project', 'update_project'] as $permission) {
        Permission::findOrCreate($permission);
    }

    $user = User::factory()->create();
    $user->assignRole('super_admin');
    $user->givePermissionTo(['view_any_project', 'view_project', 'create_project', 'update_project']);

    return $user;
}

function makeLandProject(?User $user = null): Project
{
    $user ??= makeWorkflowUser();

    return Project::factory()->create([
        'user_id' => $user->id,
        'type' => ['land_project'],
        'workflow_stages' => ProjectTypeWorkflow::stagesForType('land_project'),
    ]);
}

it('leaves existing projects unrestricted when they have no frozen workflow', function () {
    $user = makeWorkflowUser();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'type' => ['land_project'],
    ]);

    expect($project->hasFrozenWorkflow())->toBeFalse()
        ->and($project->canEnterStage(ProcessStage::PurchaseRequest))->toBeTrue()
        ->and($project->canEnterStage(ProcessStage::TechnicalWorkingGroup))->toBeTrue();

    PurchaseRequest::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    expect($project->purchase_requests()->count())->toBe(1);
});

it('snapshots the current type workflow onto newly created projects', function () {
    $user = makeWorkflowUser();
    $this->actingAs($user);

    Livewire::test(CreateProject::class)
        ->fillForm([
            'type' => 'land_project',
            'code' => 'RC9999001',
            'funds' => ['GEN_FUND'],
            'year' => (string) now()->year,
            'status' => 'released',
            'name' => 'Unique Land Workflow Project',
            'appropriation' => '1000000.00',
            'allotment' => '800000.00',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $project = Project::query()->where('name', 'Unique Land Workflow Project')->first();

    expect($project)->not->toBeNull()
        ->and($project->workflow_stages)->toBe(ProjectTypeWorkflow::defaultStagesByType()['land_project']);
});

it('does not change existing project workflows when the type workflow is updated', function () {
    $project = makeLandProject();
    $original = $project->workflow_stages;

    ProjectTypeWorkflow::query()->where('project_type', 'land_project')->update([
        'stages' => [
            ProcessStage::ObligationRequest->value,
            ProcessStage::Payment->value,
        ],
    ]);

    expect($project->fresh()->workflow_stages)->toBe($original)
        ->and(ProjectTypeWorkflow::stagesForType('land_project'))->toBe([
            ProcessStage::ObligationRequest->value,
            ProcessStage::Payment->value,
        ]);
});

it('requires pre-procurement before a purchase request on new land projects', function () {
    $user = makeWorkflowUser();
    $project = makeLandProject($user);

    expect($project->canEnterStage(ProcessStage::PreProcurement))->toBeTrue()
        ->and($project->canEnterStage(ProcessStage::PurchaseRequest))->toBeFalse();

    expect(fn () => PurchaseRequest::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]))->toThrow(ValidationException::class);

    PreProcurement::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    expect($project->fresh()->canEnterStage(ProcessStage::PurchaseRequest))->toBeTrue();

    PurchaseRequest::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    expect($project->purchase_requests()->count())->toBe(1);
});

it('blocks process stages that are not in the frozen workflow', function () {
    $user = makeWorkflowUser();
    $project = makeLandProject($user);

    expect($project->canEnterStage(ProcessStage::TechnicalWorkingGroup))->toBeFalse()
        ->and($project->stageBlockReason(ProcessStage::TechnicalWorkingGroup))
        ->toContain('not part of this project');

    expect(fn () => TechnicalWorkingGroup::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]))->toThrow(ValidationException::class);
});

it('shows only the frozen workflow stages on monitoring', function () {
    $user = makeWorkflowUser();
    $project = makeLandProject($user);
    $this->actingAs($user);

    Livewire::test(ProjectMonitoring::class, ['record' => $project->getKey()])
        ->assertOk()
        ->assertSee('Pre-Procurement')
        ->assertSee('Purchase Requests')
        ->assertSee('Procurement')
        ->assertSee('Obligation')
        ->assertSee('Implementation')
        ->assertSee('Payments')
        ->assertDontSee('PMO Control')
        ->assertDontSee('PR Control')
        ->assertSee('data-stage="pre"', false)
        ->assertDontSee('data-stage="twg"', false);
});

it('shows workflow stages as badges in the workflows table', function () {
    $user = makeWorkflowUser();
    $this->actingAs($user);

    Livewire::test(ManageProjectTypeWorkflows::class)
        ->assertOk()
        ->assertSee('Pre-Procurement')
        ->assertSee('Purchase Requests')
        ->assertSee('Payments')
        ->assertSee('fi-badge', false);
});
