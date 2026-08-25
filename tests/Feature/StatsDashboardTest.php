<?php

use App\Filament\Pages\StatsDashboard;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

function makeDashboardUser(): User
{
    Role::findOrCreate('super_admin');

    $user = User::factory()->create();
    $user->assignRole('super_admin');

    return $user;
}

it('redirects guests away from the dashboard', function () {
    $this->get('/admin')->assertRedirect();
});

it('registers the dashboard as the admin home page', function () {
    expect(route('filament.admin.pages.stats-dashboard', absolute: false))->toBe('/admin');
});

it('renders compact widgets and a chart mount point', function () {
    $this->actingAs(makeDashboardUser());

    Livewire::test(StatsDashboard::class)
        ->assertOk()
        ->assertSee('Online Users')
        ->assertSee('Released Projects')
        ->assertSee('Appropriation')
        ->assertSee('Obligated')
        ->assertSee('Disbursed')
        ->assertSee('Procurements')
        ->assertSee('With NTP')
        ->assertSee('PR Coverage')
        ->assertSee('id="dashboard-charts-root"', false)
        ->assertSee('financialOverview', false)
        ->assertSee('monthlyPayments', false)
        ->assertSee('procurementPipeline', false);
});

it('includes selected-year payment totals in the chart payload', function () {
    $user = makeDashboardUser();
    $year = (string) now()->year;

    $project = Project::factory()->create([
        'year' => $year,
        'status' => 'released',
        'user_id' => $user->id,
        'appropriation' => '1000000.00',
        'allotment' => '800000.00',
        'type' => ['infrastructure_project'],
    ]);

    Payment::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'amount' => '250000.00',
        'date' => now()->startOfMonth()->toDateString(),
        'type' => 'mobilization',
    ]);

    $this->actingAs($user);

    $charts = Livewire::test(StatsDashboard::class)
        ->set('selectedYear', $year)
        ->instance()
        ->getDashboardData()['charts'];

    expect($charts['monthlyPayments'])->toHaveCount(12)
        ->and(collect($charts['monthlyPayments'])->sum('amount'))->toBe(250000.0)
        ->and($charts['financialOverview'][0]['name'])->toBe('Appropriation')
        ->and($charts['financialOverview'][0]['amount'])->toBe(1000000.0)
        ->and($charts['financialOverview'][3]['name'])->toBe('Disbursed')
        ->and($charts['financialOverview'][3]['amount'])->toBe(250000.0)
        ->and($charts['projectStatus'][0]['name'])->toBe('Released')
        ->and($charts['projectStatus'][0]['value'])->toBe(1)
        ->and($charts['paymentTypes'][0]['name'])->toBe('Mobilization Payment')
        ->and($charts['projectTypes'][0]['name'])->toBe('Infrastructure Project');
});
