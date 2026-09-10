<?php

use App\Filament\Resources\ProjectResource\Pages\ProjectMonitoring;
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
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

function makeMonitoringUser(): User
{
    Role::findOrCreate('super_admin');

    foreach (['view_any_project', 'view_project'] as $permission) {
        Permission::findOrCreate($permission);
    }

    $user = User::factory()->create();
    $user->assignRole('super_admin');
    $user->givePermissionTo(['view_any_project', 'view_project']);

    return $user;
}

it('renders the improved monitoring overview and activity drawer', function () {
    $user = makeMonitoringUser();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'Barangay Road Rehabilitation Monitoring Sample',
        'status' => 'released',
        'year' => (string) now()->year,
        'appropriation' => '1500000.00',
        'allotment' => '1200000.00',
    ]);

    $this->actingAs($user);

    Livewire::test(ProjectMonitoring::class, ['record' => $project->getKey()])
        ->assertOk()
        ->assertSee('Process stages')
        ->assertSee('Barangay Road Rehabilitation Monitoring Sample')
        ->assertSee('Activity')
        ->assertActionExists('activityLog')
        ->mountAction('activityLog')
        ->assertSee('Project activity')
        ->assertSee('No activity yet')
        ->assertSee('Open full activity log')
        ->assertSee('Pre-Procurement')
        ->assertSee('Purchase Requests')
        ->assertSee('Payments')
        ->assertSee('data-stage="pr"', false)
        ->assertSee('data-overview-grid', false)
        ->assertSee('process-span-33', false)
        ->assertSee('process-span-40', false)
        ->assertSee('process-span-60', false)
        ->assertSee('process-span-100', false)
        ->assertSee('No pre-procurement records yet')
        ->assertSee('No purchase requests yet')
        ->assertSee('No payments yet')
        ->assertDontSee('Open stage');
});

it('shows grouped timeline entries in the activity slideover', function () {
    $user = makeMonitoringUser();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'Coastal Drainage Improvement',
        'status' => 'released',
        'year' => (string) now()->year,
    ]);

    activity()
        ->causedBy($user)
        ->performedOn($project)
        ->event('created')
        ->withProperties([
            'attributes' => [
                'name' => 'Coastal Drainage Improvement',
                'status' => 'released',
            ],
        ])
        ->log('created');

    $this->actingAs($user);

    Livewire::test(ProjectMonitoring::class, ['record' => $project->getKey()])
        ->mountAction('activityLog')
        ->assertSee('id="project-activity-drawer"', false)
        ->assertSee('activity-card', false)
        ->assertSee('Today')
        ->assertSee('Created')
        ->assertSee('Project')
        ->assertSee('Name: Coastal Drainage Improvement')
        ->assertSee($user->name)
        ->assertSee('Open full activity log');
});

it('builds summary and stage counts for the monitored project', function () {
    $user = makeMonitoringUser();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'status' => 'released',
        'appropriation' => '2000000.00',
        'allotment' => '1800000.00',
        'type' => ['infrastructure_project'],
        'funds' => ['GEN_FUND'],
    ]);

    $this->actingAs($user);

    $page = Livewire::test(ProjectMonitoring::class, ['record' => $project->getKey()])
        ->instance();

    expect($page->summary['statusLabel'])->toBe('Released')
        ->and($page->summary['appropriation'])->toBe(2000000.0)
        ->and($page->summary['typeLabels'])->toContain('Infrastructure Project')
        ->and($page->summary['fundLabels'])->toContain('General Funds')
        ->and($page->stages)->toHaveCount(9)
        ->and($page->stages[0]['key'])->toBe('pre')
        ->and($page->defaultStage())->toBe('overview')
        ->and($page->activitiesUrl())->toContain('/activities');
});

it('shows actual related record counts on monitoring stage tabs', function () {
    $user = makeMonitoringUser();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'status' => 'released',
        'appropriation' => '2000000.00',
        'allotment' => '1800000.00',
    ]);

    $attributes = [
        'user_id' => $user->id,
        'project_id' => $project->id,
    ];

    PreProcurement::factory()->count(2)->create($attributes);
    PurchaseRequest::factory()->create($attributes);
    TechnicalWorkingGroup::factory()->count(3)->create($attributes);
    ProcurementControl::factory()->create($attributes);
    PurchaseRequestControl::factory()->count(4)->create($attributes);
    Procurement::factory()->create($attributes);
    ObligationRequest::factory()->count(2)->create($attributes);
    Implementation::factory()->count(5)->create($attributes);
    Payment::factory()->count(6)->create($attributes);

    $this->actingAs($user);

    $component = Livewire::test(ProjectMonitoring::class, ['record' => $project->getKey()])
        ->assertOk()
        ->assertSee('data-stage="pre"', false)
        ->assertSee('data-count="2"', false)
        ->assertSee('data-stage="pr"', false)
        ->assertSee('data-count="1"', false)
        ->assertSee('data-stage="twg"', false)
        ->assertSee('data-count="3"', false)
        ->assertSee('data-stage="pmo"', false)
        ->assertSee('data-count="1"', false)
        ->assertSee('data-stage="prc"', false)
        ->assertSee('data-count="4"', false)
        ->assertSee('data-stage="proc"', false)
        ->assertSee('data-stage="obr"', false)
        ->assertSee('data-count="5"', false)
        ->assertSee('data-stage="impl"', false)
        ->assertSee('data-stage="pay"', false)
        ->assertSee('data-count="6"', false)
        ->assertSee('monitor-card', false)
        ->assertSee('stage-count', false);

    $counts = collect($component->instance()->stages)
        ->mapWithKeys(fn (array $stage): array => [$stage['key'] => $stage['count']]);

    expect($counts['pre'])->toBe(2)
        ->and($counts['pr'])->toBe(1)
        ->and($counts['twg'])->toBe(3)
        ->and($counts['pmo'])->toBe(1)
        ->and($counts['prc'])->toBe(4)
        ->and($counts['proc'])->toBe(1)
        ->and($counts['obr'])->toBe(2)
        ->and($counts['impl'])->toBe(5)
        ->and($counts['pay'])->toBe(6);
});
