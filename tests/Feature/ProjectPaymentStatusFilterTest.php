<?php

use App\Filament\Resources\ProjectResource\Pages\ListProjects;
use App\Models\Implementation;
use App\Models\Payment;
use App\Models\Procurement;
use App\Models\Project;
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

function makeProjectPaymentFilterUser(): User
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

it('filters projects with implementations and no payments', function () {
    $user = makeProjectPaymentFilterUser();

    $unpaidImplementation = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'Unpaid Implementation Roadworks',
    ]);

    Implementation::factory()->create([
        'project_id' => $unpaidImplementation->id,
        'user_id' => $user->id,
        'percentage' => 40,
    ]);

    $paidImplementation = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'Paid Implementation Bridge',
    ]);

    Implementation::factory()->create([
        'project_id' => $paidImplementation->id,
        'user_id' => $user->id,
        'percentage' => 80,
    ]);

    Payment::factory()->create([
        'project_id' => $paidImplementation->id,
        'user_id' => $user->id,
        'amount' => '150000.00',
    ]);

    $noImplementation = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'No Implementation Yet',
    ]);

    $this->actingAs($user);

    Livewire::test(ListProjects::class)
        ->assertOk()
        ->assertSee('Unpaid Implementation')
        ->filterTable('payment_status', 'unpaid_implementation')
        ->assertCanSeeTableRecords([$unpaidImplementation])
        ->assertCanNotSeeTableRecords([$paidImplementation, $noImplementation]);
});

it('keeps paid unpaid and no-contract payment filters working', function () {
    $user = makeProjectPaymentFilterUser();

    $paid = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'Fully Paid Contract Project',
    ]);

    Procurement::factory()->create([
        'project_id' => $paid->id,
        'user_id' => $user->id,
        'contract_amount' => '200000.00',
    ]);

    Payment::factory()->create([
        'project_id' => $paid->id,
        'user_id' => $user->id,
        'amount' => '200000.00',
    ]);

    $unpaid = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'Unpaid Contract Project',
    ]);

    Procurement::factory()->create([
        'project_id' => $unpaid->id,
        'user_id' => $user->id,
        'contract_amount' => '300000.00',
    ]);

    $noContract = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'No Contract Amount Project',
    ]);

    $this->actingAs($user);

    Livewire::test(ListProjects::class)
        ->filterTable('payment_status', 'paid')
        ->assertCanSeeTableRecords([$paid])
        ->assertCanNotSeeTableRecords([$unpaid, $noContract]);

    Livewire::test(ListProjects::class)
        ->filterTable('payment_status', 'unpaid')
        ->assertCanSeeTableRecords([$unpaid])
        ->assertCanNotSeeTableRecords([$paid, $noContract]);

    Livewire::test(ListProjects::class)
        ->filterTable('payment_status', 'no_contract')
        ->assertCanSeeTableRecords([$noContract])
        ->assertCanNotSeeTableRecords([$paid, $unpaid]);
});
