<?php

use App\Filament\Pages\ChartDashboard;
use App\Filament\Widgets\FinancialOverviewBarChart;
use App\Filament\Widgets\FinancialStatusPieChart;
use App\Filament\Widgets\ImplementationsWithNtpLineChart;
use App\Filament\Widgets\MonthlyPaymentsLineChart;
use App\Filament\Widgets\ProjectStatusPieChart;
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

function makeChartDashboardUser(): User
{
    Role::findOrCreate('super_admin');

    $user = User::factory()->create();
    $user->assignRole('super_admin');

    return $user;
}

it('registers the chart statistics page widgets in a balanced order', function () {
    $this->actingAs(makeChartDashboardUser());

    $page = Livewire::test(ChartDashboard::class)
        ->assertOk()
        ->instance();

    expect($page->getWidgets())->toBe([
        FinancialOverviewBarChart::class,
        FinancialStatusPieChart::class,
        MonthlyPaymentsLineChart::class,
        ProjectStatusPieChart::class,
        ImplementationsWithNtpLineChart::class,
    ])->and($page->getColumns())->toMatchArray([
        'default' => 1,
        'md' => 2,
        'xl' => 2,
    ]);
});

it('builds financial overview amounts for the selected year', function () {
    $user = makeChartDashboardUser();
    $year = (string) now()->year;

    Project::factory()->create([
        'year' => $year,
        'user_id' => $user->id,
        'appropriation' => '1000000.00',
        'allotment' => '800000.00',
    ]);

    Payment::factory()->create([
        'user_id' => $user->id,
        'project_id' => Project::query()->first()->id,
        'amount' => '250000.00',
        'date' => now()->toDateString(),
    ]);

    $this->actingAs($user);

    $widget = Livewire::test(FinancialOverviewBarChart::class)
        ->set('filter', $year)
        ->instance();

    $method = new ReflectionMethod($widget, 'getData');
    $data = $method->invoke($widget);

    expect($data['labels'])->toBe([
        'Appropriation',
        'Allotment',
        'Obligated',
        'Disbursed',
    ])
        ->and($data['datasets'][0]['data'][0])->toBe(1000000.0)
        ->and($data['datasets'][0]['data'][1])->toBe(800000.0)
        ->and($data['datasets'][0]['data'][3])->toBe(250000.0);
});

it('renders doughnut widgets with compact labels', function () {
    $this->actingAs(makeChartDashboardUser());

    Livewire::test(FinancialStatusPieChart::class)
        ->assertOk()
        ->assertSee('Obligation vs Allotment');

    Livewire::test(ProjectStatusPieChart::class)
        ->assertOk()
        ->assertSee('Project Progress');
});
