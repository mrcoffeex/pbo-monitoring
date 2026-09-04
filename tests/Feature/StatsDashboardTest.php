<?php

use App\Filament\Pages\StatsDashboard;
use App\Filament\Resources\ProjectResource;
use App\Models\ObligationRequest;
use App\Models\Payment;
use App\Models\Procurement;
use App\Models\Project;
use App\Models\PurchaseRequest;
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
        ->assertSee('Insights')
        ->assertSee('dashboard-toolbar', false)
        ->assertSee('dashboard-top-layout', false)
        ->assertSee('dashboard-top-insights', false)
        ->assertSee('id="projectSearch"', false)
        ->assertSee('id="selectedYear"', false)
        ->assertSee('All Years')
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

it('builds period insights and an attention watchlist', function () {
    $user = makeDashboardUser();
    $year = (string) now()->year;

    $unreleased = Project::factory()->create([
        'year' => $year,
        'status' => 'unreleased',
        'name' => 'Unreleased Farm to Market Road',
        'code' => 'RC2601001',
        'allotment' => '500000.00',
        'user_id' => $user->id,
    ]);

    $releasedWithoutPr = Project::factory()->create([
        'year' => $year,
        'status' => 'released',
        'name' => 'Released School Building',
        'code' => 'RC2601002',
        'user_id' => $user->id,
    ]);

    $prOnly = Project::factory()->create([
        'year' => $year,
        'status' => 'released',
        'name' => 'PR Waiting for Award',
        'code' => 'RC2601003',
        'user_id' => $user->id,
    ]);

    PurchaseRequest::factory()->create([
        'project_id' => $prOnly->id,
        'user_id' => $user->id,
    ]);

    $ntpOnly = Project::factory()->create([
        'year' => $year,
        'status' => 'released',
        'name' => 'NTP Without Progress',
        'code' => 'RC2601004',
        'user_id' => $user->id,
    ]);

    Procurement::factory()->create([
        'project_id' => $ntpOnly->id,
        'user_id' => $user->id,
        'ntp_number' => 'NTP-999001',
        'contract_amount' => '400000.00',
    ]);

    $otherYear = Project::factory()->create([
        'year' => (string) (now()->year - 1),
        'status' => 'unreleased',
        'name' => 'Old Year Unreleased',
        'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $dashboard = Livewire::test(StatsDashboard::class)
        ->set('selectedYear', $year)
        ->assertSee('Still unreleased')
        ->assertSee('Released without PR')
        ->assertSee('PR awaiting award')
        ->assertSee('NTP with no progress')
        ->assertSee('Needs attention')
        ->assertSee('Unreleased Farm to Market Road')
        ->assertDontSee('Old Year Unreleased')
        ->instance()
        ->getDashboardData();

    $titles = collect($dashboard['insights'])->pluck('title');
    $watchlistNames = collect($dashboard['watchlist'])->pluck('name');

    expect($titles->all())->toContain('Still unreleased', 'Released without PR', 'PR awaiting award', 'NTP with no progress')
        ->and($watchlistNames->all())->toContain(
            $unreleased->name,
            $releasedWithoutPr->name,
            $prOnly->name,
            $ntpOnly->name,
        )
        ->and($watchlistNames->all())->not->toContain($otherYear->name)
        ->and($dashboard['watchlist'][0]['url'])->toBe(ProjectResource::getUrl('monitoring', ['record' => $dashboard['watchlist'][0]['id']]));
});

it('reports fund utilization from obligated and disbursed amounts', function () {
    $user = makeDashboardUser();
    $year = (string) now()->year;

    $project = Project::factory()->create([
        'year' => $year,
        'status' => 'released',
        'allotment' => '1000000.00',
        'user_id' => $user->id,
    ]);

    ObligationRequest::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'amount' => '800000.00',
    ]);

    Payment::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'amount' => '200000.00',
        'date' => now()->toDateString(),
    ]);

    $this->actingAs($user);

    $insights = Livewire::test(StatsDashboard::class)
        ->set('selectedYear', $year)
        ->instance()
        ->getDashboardData()['insights'];

    $utilization = collect($insights)->firstWhere('title', 'Fund utilization');

    expect($utilization['value'])->toBe('25%')
        ->and($utilization['tone'])->toBe('rose');
});
