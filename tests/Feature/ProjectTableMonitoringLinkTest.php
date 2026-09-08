<?php

use App\Filament\Resources\ImplementationResource\Pages\ListImplementations;
use App\Filament\Resources\ObligationRequestResource\Pages\ListObligationRequests;
use App\Filament\Resources\PaymentResource\Pages\ListPayments;
use App\Filament\Resources\PreProcurementResource\Pages\ListPreProcurements;
use App\Filament\Resources\ProcurementControlResource\Pages\ListProcurementControls;
use App\Filament\Resources\ProcurementResource\Pages\ListProcurements;
use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\ProjectResource\Pages\ListProjects;
use App\Filament\Resources\PurchaseRequestControlResource\Pages\ListPurchaseRequestControls;
use App\Filament\Resources\PurchaseRequestResource\Pages\ListPurchaseRequests;
use App\Filament\Resources\TechnicalWorkingGroupResource\Pages\ListTechnicalWorkingGroups;
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

function makeTableLinkUser(): User
{
    Role::findOrCreate('super_admin');

    $user = User::factory()->create();
    $user->assignRole('super_admin');

    return $user;
}

it('does not build a monitoring url without a project', function () {
    expect(ProjectResource::monitoringUrl(null))->toBeNull();
});

it('links the project name on resource tables to the monitoring page', function (string $page, array $permissions) {
    $user = makeTableLinkUser();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
    }

    $user->givePermissionTo($permissions);

    $project = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'Clickable Monitoring Link Project',
    ]);

    $attributes = [
        'user_id' => $user->id,
        'project_id' => $project->id,
    ];

    match ($page) {
        ListProjects::class => null,
        ListPayments::class => Payment::factory()->create($attributes),
        ListImplementations::class => Implementation::factory()->create($attributes),
        ListPurchaseRequests::class => PurchaseRequest::factory()->create($attributes),
        ListObligationRequests::class => ObligationRequest::factory()->create($attributes),
        ListPurchaseRequestControls::class => PurchaseRequestControl::factory()->create($attributes),
        ListProcurementControls::class => ProcurementControl::factory()->create($attributes),
        ListTechnicalWorkingGroups::class => TechnicalWorkingGroup::factory()->create($attributes),
        ListProcurements::class => Procurement::factory()->create($attributes),
        ListPreProcurements::class => PreProcurement::factory()->create($attributes),
    };

    $this->actingAs($user);

    Livewire::test($page)
        ->assertOk()
        ->assertSee('Clickable Monitoring Link Project')
        ->assertSee(ProjectResource::monitoringUrl($project), false);
})->with([
    'projects' => [ListProjects::class, ['view_any_project', 'view_project']],
    'payments' => [ListPayments::class, ['view_any_payment', 'view_payment']],
    'implementations' => [ListImplementations::class, ['view_any_implementation', 'view_implementation']],
    'purchase requests' => [ListPurchaseRequests::class, ['view_any_purchase::request', 'view_purchase::request']],
    'obligation requests' => [ListObligationRequests::class, ['view_any_obligation::request', 'view_obligation::request']],
    'purchase request controls' => [ListPurchaseRequestControls::class, ['view_any_purchase::request::control', 'view_purchase::request::control']],
    'procurement controls' => [ListProcurementControls::class, ['view_any_procurement::control', 'view_procurement::control']],
    'technical working groups' => [ListTechnicalWorkingGroups::class, ['view_any_technical::working::group', 'view_technical::working::group']],
    'procurements' => [ListProcurements::class, ['view_any_procurement', 'view_procurement']],
    'pre-procurements' => [ListPreProcurements::class, ['view_any_pre::procurement', 'view_pre::procurement']],
]);
