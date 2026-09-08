<?php

use App\Filament\Resources\PaymentResource\Pages\CreatePayment;
use App\Filament\Resources\PaymentResource\Pages\EditPayment;
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

function makePaymentUser(): User
{
    Role::findOrCreate('super_admin');

    foreach (['view_any_payment', 'view_payment', 'create_payment', 'update_payment'] as $permission) {
        Permission::findOrCreate($permission);
    }

    $user = User::factory()->create();
    $user->assignRole('super_admin');
    $user->givePermissionTo(['view_any_payment', 'view_payment', 'create_payment', 'update_payment']);

    return $user;
}

/**
 * @return array{0: User, 1: Project}
 */
function makeProjectWithContract(string $contractAmount, string $allotment = '1000000.00'): array
{
    $user = makePaymentUser();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'appropriation' => '1500000.00',
        'allotment' => $allotment,
    ]);

    Procurement::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'contract_amount' => $contractAmount,
    ]);

    return [$user, $project];
}

it('calculates remaining payment balance from the contract minus other payments', function () {
    [$user, $project] = makeProjectWithContract('500000.00');

    $existingPayment = Payment::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'amount' => '200000.00',
    ]);

    expect($project->paymentCeiling())->toBe(500000.0)
        ->and($project->paidPaymentTotal())->toBe(200000.0)
        ->and($project->remainingPaymentBalance())->toBe(300000.0)
        ->and($project->remainingPaymentBalance($existingPayment))->toBe(500000.0);
});

it('uses allotment as the payment ceiling when there is no contract', function () {
    $user = makePaymentUser();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'appropriation' => '900000.00',
        'allotment' => '400000.00',
    ]);

    Payment::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'amount' => '150000.00',
    ]);

    expect($project->paymentCeiling())->toBe(400000.0)
        ->and($project->remainingPaymentBalance())->toBe(250000.0);
});

it('rejects a new payment that exceeds the remaining balance', function () {
    [$user, $project] = makeProjectWithContract('500000.00');

    Payment::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'amount' => '400000.00',
    ]);

    $this->actingAs($user);

    Livewire::test(CreatePayment::class)
        ->fillForm([
            'project_id' => $project->id,
            'date' => now()->toDateString(),
            'type' => 'mobilization',
            'amount' => '150000.00',
        ])
        ->call('create')
        ->assertHasFormErrors(['amount']);

    expect(Payment::query()->where('project_id', $project->id)->count())->toBe(1);
});

it('allows a new payment that uses the remaining balance', function () {
    [$user, $project] = makeProjectWithContract('500000.00');

    Payment::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'amount' => '400000.00',
    ]);

    $this->actingAs($user);

    Livewire::test(CreatePayment::class)
        ->fillForm([
            'project_id' => $project->id,
            'date' => now()->toDateString(),
            'type' => 'final_payment',
            'amount' => '100000.00',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    expect((float) Payment::query()->where('project_id', $project->id)->sum('amount'))->toBe(500000.0);
});

it('rejects an edited payment that would exceed the remaining balance', function () {
    [$user, $project] = makeProjectWithContract('500000.00');

    Payment::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'amount' => '300000.00',
    ]);

    $payment = Payment::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'amount' => '100000.00',
    ]);

    $this->actingAs($user);

    Livewire::test(EditPayment::class, ['record' => $payment->getKey()])
        ->fillForm([
            'amount' => '250000.00',
        ])
        ->call('save')
        ->assertHasFormErrors(['amount']);

    expect((float) $payment->refresh()->amount)->toBe(100000.0);
});

it('suggests a payment amount from the selected implementation percentage', function () {
    [$user, $project] = makeProjectWithContract('500000.00');

    $implementation = Implementation::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'percentage' => 40,
    ]);

    Payment::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'amount' => '50000.00',
    ]);

    expect($project->suggestedPaymentAmount($implementation))->toBe(150000.0);
});

it('caps the suggested implementation amount at the remaining balance', function () {
    [$user, $project] = makeProjectWithContract('500000.00');

    $implementation = Implementation::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'percentage' => 100,
    ]);

    Payment::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'amount' => '400000.00',
    ]);

    expect($project->suggestedPaymentAmount($implementation))->toBe(100000.0);
});

it('creates a payment using an implementation and a flexible amount within the balance', function () {
    [$user, $project] = makeProjectWithContract('500000.00');

    $implementation = Implementation::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'percentage' => 40,
        'date' => now()->toDateString(),
    ]);

    $this->actingAs($user);

    Livewire::test(CreatePayment::class)
        ->fillForm([
            'project_id' => $project->id,
            'implementation_id' => $implementation->id,
            'date' => now()->toDateString(),
            'type' => 'first_partial',
            'amount' => '180000.00',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $payment = Payment::query()->where('project_id', $project->id)->first();

    expect($payment)->not->toBeNull()
        ->and((int) $payment->implementation_id)->toBe($implementation->id)
        ->and((float) $payment->amount)->toBe(180000.0);
});

it('requires an implementation when the project already has implementation records', function () {
    [$user, $project] = makeProjectWithContract('500000.00');

    Implementation::factory()->create([
        'project_id' => $project->id,
        'user_id' => $user->id,
        'percentage' => 25,
    ]);

    $this->actingAs($user);

    Livewire::test(CreatePayment::class)
        ->fillForm([
            'project_id' => $project->id,
            'date' => now()->toDateString(),
            'type' => 'mobilization',
            'amount' => '50000.00',
        ])
        ->call('create')
        ->assertHasFormErrors(['implementation_id']);
});
