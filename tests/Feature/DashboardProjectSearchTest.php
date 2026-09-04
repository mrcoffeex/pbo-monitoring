<?php

use App\Filament\Pages\StatsDashboard;
use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\ProjectResource\Pages\ListProjects;
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

function makeProjectSearchUser(): User
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

it('shows a project search above the dashboard widgets', function () {
    $this->actingAs(makeProjectSearchUser());

    Livewire::test(StatsDashboard::class)
        ->assertOk()
        ->assertSee('Search projects by name, code, or status', false)
        ->assertSee('Search', false)
        ->assertSee('id="projectSearch"', false)
        ->assertSee('id="selectedYear"', false)
        ->assertSee('All Years')
        ->assertSee('Year');
});

it('redirects a dashboard search to the projects list', function () {
    $this->actingAs(makeProjectSearchUser());

    Livewire::test(StatsDashboard::class)
        ->set('projectSearch', 'Barangay Road')
        ->call('searchProjects')
        ->assertRedirect(ProjectResource::getUrl('index', ['tableSearch' => 'Barangay Road']));
});

it('redirects an empty dashboard search to the projects list', function () {
    $this->actingAs(makeProjectSearchUser());

    Livewire::test(StatsDashboard::class)
        ->set('projectSearch', '   ')
        ->call('searchProjects')
        ->assertRedirect(ProjectResource::getUrl('index'));
});

it('applies the dashboard search on the projects table', function () {
    $user = makeProjectSearchUser();

    $matchingProject = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'Unique Barangay Bridge Rehabilitation',
        'code' => 'RC2601999',
    ]);

    $otherProject = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'Municipal Hall Painting Works',
        'code' => 'RC2601888',
    ]);

    $this->actingAs($user);

    Livewire::withQueryParams(['tableSearch' => 'Unique Barangay Bridge'])
        ->test(ListProjects::class)
        ->assertOk()
        ->assertSet('tableSearch', 'Unique Barangay Bridge')
        ->assertCanSeeTableRecords([$matchingProject])
        ->assertCanNotSeeTableRecords([$otherProject]);
});
