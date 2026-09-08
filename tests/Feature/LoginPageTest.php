<?php

use App\Filament\Pages\Auth\Login;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

function makeLoginUser(): User
{
    Role::findOrCreate('super_admin');

    $user = User::factory()->create();
    $user->assignRole('super_admin');

    return $user;
}

it('renders a branded sign-in page for visitors', function () {
    $response = $this->get(route('filament.admin.auth.login'));

    $response->assertSuccessful();
    $response->assertSee('Skip to sign in form', false);
    $response->assertSee('Provincial Budget Office · Davao del Sur', false);
    $response->assertSee('Follow every project from budget to payment', false);
    $response->assertSee('Sign in to the dashboard', false);
    $response->assertSee('Use your office account. Access follows your role.', false);
    $response->assertSee('Back to website', false);
    $response->assertSee('An official website of the Provincial Government of Davao del Sur', false);
    $response->assertSee('images/hero/kapatagan-mt-apo.jpg', false);
    $response->assertSee(url('/'), false);
});

it('signs in an authorized user from the login form', function () {
    $user = makeLoginUser();

    Livewire::test(Login::class)
        ->fillForm([
            'email' => $user->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect(Filament::getUrl());

    $this->assertAuthenticatedAs($user);
});

it('rejects invalid credentials', function () {
    $user = makeLoginUser();

    Livewire::test(Login::class)
        ->fillForm([
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
        ->call('authenticate')
        ->assertHasFormErrors(['email']);

    $this->assertGuest();
});
