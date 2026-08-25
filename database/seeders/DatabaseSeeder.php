<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            ProjectSeeder::class,
            PreProcurementSeeder::class,
            PurchaseRequestSeeder::class,
            TechnicalWorkingGroupSeeder::class,
            ProcurementControlSeeder::class,
            PurchaseRequestControlSeeder::class,
            ProcurementSeeder::class,
            ObligationRequestSeeder::class,
            ImplementationSeeder::class,
            PaymentSeeder::class,
            ProjectActivitySeeder::class,
        ]);
    }
}
