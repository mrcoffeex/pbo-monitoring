<?php

use App\Filament\Resources\ProjectResource\Pages\ListProjects;
use App\Models\Implementation;
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

    Role::findOrCreate('super_admin');

    foreach (['view_any_project', 'view_project'] as $permission) {
        Permission::findOrCreate($permission);
    }

    $this->user = User::factory()->create();
    $this->user->assignRole('super_admin');
    $this->user->givePermissionTo(['view_any_project', 'view_project']);
    $this->actingAs($this->user);
});

it('filters by multiple funds', function () {
    $gen = Project::factory()->create(['user_id' => $this->user->id, 'funds' => ['GEN_FUND']]);
    $se = Project::factory()->create(['user_id' => $this->user->id, 'funds' => ['SE_FUND']]);
    $trust = Project::factory()->create(['user_id' => $this->user->id, 'funds' => ['TRUST_FUND']]);

    Livewire::test(ListProjects::class)
        ->filterTable('funds', ['funds' => ['GEN_FUND', 'SE_FUND']])
        ->assertCanSeeTableRecords([$gen, $se])
        ->assertCanNotSeeTableRecords([$trust]);
});

it('filters by project type', function () {
    $infra = Project::factory()->create(['user_id' => $this->user->id, 'type' => ['infrastructure_project']]);
    $land = Project::factory()->create(['user_id' => $this->user->id, 'type' => ['land_project']]);

    Livewire::test(ListProjects::class)
        ->filterTable('type', ['type' => ['land_project']])
        ->assertCanSeeTableRecords([$land])
        ->assertCanNotSeeTableRecords([$infra]);
});

it('filters by NOA and NTP status', function () {
    $received = Project::factory()->create(['user_id' => $this->user->id]);
    Procurement::factory()->create([
        'project_id' => $received->id,
        'user_id' => $this->user->id,
        'noa_date_received' => now()->toDateString(),
        'ntp_number' => 'NTP-1',
    ]);

    $none = Project::factory()->create(['user_id' => $this->user->id]);

    Livewire::test(ListProjects::class)
        ->filterTable('noa_received', 'received')
        ->assertCanSeeTableRecords([$received])
        ->assertCanNotSeeTableRecords([$none]);

    Livewire::test(ListProjects::class)
        ->filterTable('noa_received', 'not_received')
        ->assertCanSeeTableRecords([$none])
        ->assertCanNotSeeTableRecords([$received]);

    Livewire::test(ListProjects::class)
        ->filterTable('ntp', 'issued')
        ->assertCanSeeTableRecords([$received])
        ->assertCanNotSeeTableRecords([$none]);

    Livewire::test(ListProjects::class)
        ->filterTable('ntp', 'not_issued')
        ->assertCanSeeTableRecords([$none])
        ->assertCanNotSeeTableRecords([$received]);
});

it('filters by latest implementation percentage', function () {
    $low = Project::factory()->create(['user_id' => $this->user->id]);
    Implementation::factory()->create(['project_id' => $low->id, 'user_id' => $this->user->id, 'percentage' => 10]);

    $high = Project::factory()->create(['user_id' => $this->user->id]);
    Implementation::factory()->create(['project_id' => $high->id, 'user_id' => $this->user->id, 'percentage' => 90]);

    Livewire::test(ListProjects::class)
        ->filterTable('latest_implementation_percentage', ['percentage_range' => '0-25'])
        ->assertCanSeeTableRecords([$low])
        ->assertCanNotSeeTableRecords([$high]);
});

it('filters by year and obligation status', function () {
    $current = Project::factory()->create(['user_id' => $this->user->id, 'year' => (string) now()->year]);
    $old = Project::factory()->create(['user_id' => $this->user->id, 'year' => (string) (now()->year - 2)]);

    Livewire::test(ListProjects::class)
        ->filterTable('year', (string) now()->year)
        ->assertCanSeeTableRecords([$current])
        ->assertCanNotSeeTableRecords([$old]);

    Livewire::test(ListProjects::class)
        ->filterTable('obligation', 'unobligated')
        ->assertCanSeeTableRecords([$current, $old]);

    Livewire::test(ListProjects::class)
        ->filterTable('purchase_requests', 'without')
        ->assertCanSeeTableRecords([$current, $old]);
});
