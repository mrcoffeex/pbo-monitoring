<?php

use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

function makeUserResourceActor(): User
{
    Role::findOrCreate('super_admin');

    foreach (['view_any_user', 'view_user', 'create_user', 'update_user'] as $permission) {
        Permission::findOrCreate($permission);
    }

    $user = User::factory()->create();
    $user->assignRole('super_admin');
    $user->givePermissionTo(['view_any_user', 'view_user', 'create_user', 'update_user']);

    return $user;
}

it('creates a user from the list slideover', function () {
    $actor = makeUserResourceActor();
    $role = Role::findOrCreate('panel_user');

    $this->actingAs($actor);

    Livewire::test(ListUsers::class)
        ->callAction('create', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'roles' => [$role->id],
            'password' => 'secret-password',
            'passwordConfirmation' => 'secret-password',
        ])
        ->assertHasNoActionErrors()
        ->assertNotified();

    $created = User::query()->where('email', 'ada@example.com')->first();

    expect($created)->not->toBeNull()
        ->and($created->name)->toBe('Ada Lovelace')
        ->and($created->hasRole('panel_user'))->toBeTrue()
        ->and($created->email_verified_at)->not->toBeNull()
        ->and(Hash::check('secret-password', $created->password))->toBeTrue();
});

it('requires a password when creating a user', function () {
    $actor = makeUserResourceActor();
    $role = Role::findOrCreate('panel_user');

    $this->actingAs($actor);

    Livewire::test(ListUsers::class)
        ->callAction('create', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'roles' => [$role->id],
            'password' => null,
            'passwordConfirmation' => null,
        ])
        ->assertHasActionErrors(['password']);

    expect(User::query()->where('email', 'ada@example.com')->exists())->toBeFalse();
});

it('rejects a new user email that is already taken', function () {
    $actor = makeUserResourceActor();
    $role = Role::findOrCreate('panel_user');

    User::factory()->create([
        'email' => 'taken@example.com',
    ]);

    $this->actingAs($actor);

    Livewire::test(ListUsers::class)
        ->callAction('create', [
            'name' => 'Ada Lovelace',
            'email' => 'taken@example.com',
            'roles' => [$role->id],
            'password' => 'secret-password',
            'passwordConfirmation' => 'secret-password',
        ])
        ->assertHasActionErrors(['email']);
});

it('updates a user from the table slideover without changing the password', function () {
    $actor = makeUserResourceActor();
    $role = Role::findOrCreate('panel_user');

    $user = User::factory()->create([
        'name' => 'Old Name Here',
        'email' => 'old@example.com',
    ]);
    $originalPassword = $user->password;
    $user->assignRole($role);

    $this->actingAs($actor);

    Livewire::test(ListUsers::class)
        ->callTableAction('edit', $user, [
            'name' => 'New Name Here',
            'email' => 'new@example.com',
            'roles' => [$role->id],
        ])
        ->assertHasNoTableActionErrors()
        ->assertNotified();

    $user->refresh();

    expect($user->name)->toBe('New Name Here')
        ->and($user->email)->toBe('new@example.com')
        ->and($user->password)->toBe($originalPassword);
});

it('rejects an edited email that belongs to another user', function () {
    $actor = makeUserResourceActor();
    $role = Role::findOrCreate('panel_user');

    User::factory()->create([
        'email' => 'taken@example.com',
    ]);

    $user = User::factory()->create([
        'name' => 'Editable User',
        'email' => 'editable@example.com',
    ]);
    $user->assignRole($role);

    $this->actingAs($actor);

    Livewire::test(ListUsers::class)
        ->callTableAction('edit', $user, [
            'name' => 'Editable User',
            'email' => 'taken@example.com',
            'roles' => [$role->id],
        ])
        ->assertHasTableActionErrors(['email']);

    expect($user->refresh()->email)->toBe('editable@example.com');
});
