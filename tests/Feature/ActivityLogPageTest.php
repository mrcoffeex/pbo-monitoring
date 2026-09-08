<?php

use App\Filament\Pages\Activities;
use App\Models\Project;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

function makeActivityLogActor(string $role = 'super_admin'): User
{
    Role::findOrCreate('super_admin');
    Role::findOrCreate('panel_user');

    $user = User::factory()->create();
    $user->assignRole($role);

    return $user;
}

function makeActivityLog(User $causer, Project $subject, string $event, string $description): Activity
{
    $activity = new Activity;
    $activity->log_name = 'default';
    $activity->description = $description;
    $activity->event = $event;
    $activity->subject()->associate($subject);
    $activity->causer()->associate($causer);
    $activity->properties = [
        'attributes' => [
            'name' => $subject->name,
            'code' => $subject->code,
        ],
    ];
    $activity->save();

    return $activity->fresh(['causer', 'subject']);
}

it('lists each activity log as its own table row', function () {
    $user = makeActivityLogActor();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $created = makeActivityLog($user, $project, 'created', 'unique-created-log');
    $updated = makeActivityLog($user, $project, 'updated', 'unique-updated-log');

    $this->actingAs($user);

    Livewire::test(Activities::class)
        ->assertCanSeeTableRecords([$created, $updated])
        ->assertCountTableRecords(2);
});

it('searches activity logs by description', function () {
    $user = makeActivityLogActor();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $matching = makeActivityLog($user, $project, 'created', 'alpha-search-token');
    $other = makeActivityLog($user, $project, 'created', 'unrelated-description');

    $this->actingAs($user);

    Livewire::test(Activities::class)
        ->searchTable('alpha-search-token')
        ->assertCanSeeTableRecords([$matching])
        ->assertCanNotSeeTableRecords([$other]);
});

it('filters activity logs by event', function () {
    $user = makeActivityLogActor();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $created = makeActivityLog($user, $project, 'created', 'created-entry');
    $updated = makeActivityLog($user, $project, 'updated', 'updated-entry');

    $this->actingAs($user);

    Livewire::test(Activities::class)
        ->filterTable('event', 'updated')
        ->assertCanSeeTableRecords([$updated])
        ->assertCanNotSeeTableRecords([$created]);
});

it('hides other users logs from staff who are not super admins', function () {
    $staff = makeActivityLogActor('panel_user');
    $other = makeActivityLogActor('panel_user');
    $project = Project::factory()->create(['user_id' => $staff->id]);

    $own = makeActivityLog($staff, $project, 'created', 'own-staff-log');
    $foreign = makeActivityLog($other, $project, 'created', 'foreign-staff-log');

    $this->actingAs($staff);

    Livewire::test(Activities::class)
        ->assertCanSeeTableRecords([$own])
        ->assertCanNotSeeTableRecords([$foreign]);
});
