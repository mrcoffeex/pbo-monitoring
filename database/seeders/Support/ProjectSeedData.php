<?php

namespace Database\Seeders\Support;

use App\Enums\CustomOptions;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Support\Collection;

class ProjectSeedData
{
    /**
     * @return list<string>
     */
    public const CONTRACTORS = [
        'Apex Builders Corporation',
        'Northern Star Construction',
        'Metro Pacific Contractors',
        'Greenfield Development Corp.',
        'Summit Infrastructure Inc.',
        'Isla Construction and Supply',
        'Primeway Builders',
        'Horizon Engineering Works',
        'Lakbay Construction Group',
        'Valiant General Services',
    ];

    public static function userId(): int
    {
        return User::query()->value('id') ?? User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->id;
    }

    /**
     * @return Collection<int, Project>
     */
    public static function projects(): Collection
    {
        $projects = Project::query()->orderBy('id')->get();

        if ($projects->isEmpty()) {
            $userId = self::userId();

            $projects = Project::factory()
                ->count(20)
                ->create([
                    'user_id' => $userId,
                ]);
        }

        return $projects;
    }

    /**
     * @return array{
     *     project_created: CarbonInterface,
     *     pre_procurement: CarbonInterface,
     *     pr_received: CarbonInterface,
     *     pr_forward_twg: CarbonInterface,
     *     twg_review: CarbonInterface,
     *     twg_controlled: CarbonInterface,
     *     pmo_controlled: CarbonInterface,
     *     prc_controlled: CarbonInterface,
     *     pre_proc_conference: CarbonInterface,
     *     pre_bid: CarbonInterface,
     *     bid_opening: CarbonInterface,
     *     ber: CarbonInterface,
     *     post_qua: CarbonInterface,
     *     noa: CarbonInterface,
     *     ntp: CarbonInterface,
     *     obr: CarbonInterface,
     *     impl_start: CarbonInterface,
     *     impl_end: CarbonInterface,
     *     payments: list<CarbonInterface>
     * }
     */
    public static function timeline(Project $project): array
    {
        $faker = self::faker($project);
        $year = (int) $project->year;
        $now = now();
        $endBound = $year >= (int) $now->year
            ? $now->copy()->subDay()
            : Carbon::create($year, 12, 15)->endOfDay();

        $startMonth = $faker->numberBetween(1, 6);
        $cursor = Carbon::create($year, $startMonth, $faker->numberBetween(1, 20))->startOfDay();

        if ($cursor->greaterThan($endBound)) {
            $cursor = $endBound->copy()->subMonths(5)->startOfDay();
        }

        $advance = function (int $minDays, int $maxDays) use (&$cursor, $endBound, $faker): Carbon {
            $cursor = $cursor->copy()->addDays($faker->numberBetween($minDays, $maxDays));

            if ($cursor->greaterThan($endBound)) {
                $cursor = $endBound->copy();
            }

            return $cursor->copy();
        };

        $projectCreated = $cursor->copy();
        $preProcurement = $advance(3, 10);
        $prReceived = $advance(2, 8);
        $prForwardTwg = $advance(1, 6);
        $twgReview = $advance(3, 10);
        $twgControlled = $advance(2, 7);
        $pmoControlled = $advance(1, 5);
        $prcControlled = $advance(1, 5);
        $preProcConference = $advance(3, 10);
        $preBid = $advance(5, 12);
        $bidOpening = $advance(7, 14);
        $ber = $advance(3, 8);
        $postQua = $advance(2, 7);
        $noa = $advance(3, 10);
        $ntp = $advance(5, 14);
        $obr = $advance(1, 6);
        $implStart = $advance(3, 10);
        $implEnd = $implStart->copy()->addDays($faker->numberBetween(60, 180));

        if ($implEnd->greaterThan($endBound->copy()->addMonths(3))) {
            $implEnd = $endBound->copy()->addDays(30);
        }

        $payments = [];
        $paymentCursor = $implStart->copy();

        for ($index = 0; $index < 4; $index++) {
            $paymentCursor = $paymentCursor->copy()->addDays($faker->numberBetween(12, 28));

            if ($paymentCursor->greaterThan($endBound)) {
                $paymentCursor = $endBound->copy()->subDays(max(0, 3 - $index));
            }

            $payments[] = $paymentCursor->copy();
        }

        return [
            'project_created' => $projectCreated,
            'pre_procurement' => $preProcurement,
            'pr_received' => $prReceived,
            'pr_forward_twg' => $prForwardTwg,
            'twg_review' => $twgReview,
            'twg_controlled' => $twgControlled,
            'pmo_controlled' => $pmoControlled,
            'prc_controlled' => $prcControlled,
            'pre_proc_conference' => $preProcConference,
            'pre_bid' => $preBid,
            'bid_opening' => $bidOpening,
            'ber' => $ber,
            'post_qua' => $postQua,
            'noa' => $noa,
            'ntp' => $ntp,
            'obr' => $obr,
            'impl_start' => $implStart,
            'impl_end' => $implEnd,
            'payments' => $payments,
        ];
    }

    /**
     * @return array{allotment: float, abc: float, contract: float, obligated: float}
     */
    public static function amounts(Project $project): array
    {
        $faker = self::faker($project);
        $allotment = (float) $project->allotment;

        if ($allotment <= 0) {
            $allotment = (float) $project->appropriation;
        }

        if ($allotment <= 0) {
            $allotment = 1_000_000.0;
        }

        $abc = round($allotment * $faker->randomFloat(2, 0.88, 0.98), 2);
        $contract = round($abc * $faker->randomFloat(2, 0.90, 0.99), 2);

        return [
            'allotment' => $allotment,
            'abc' => $abc,
            'contract' => $contract,
            'obligated' => $contract,
        ];
    }

    public static function contractor(Project $project): string
    {
        return self::CONTRACTORS[$project->id % count(self::CONTRACTORS)];
    }

    /**
     * @return list<int>
     */
    public static function implementationPercentages(Project $project): array
    {
        return match ($project->status) {
            'canceled' => [15, 25],
            'unreleased' => [20, 40, 55],
            default => [25, 50, 75, 100],
        };
    }

    /**
     * @return list<string>
     */
    public static function paymentTypes(Project $project): array
    {
        $types = array_keys(CustomOptions::PAYMENTS);

        return match ($project->status) {
            'canceled' => array_slice($types, 0, 1),
            'unreleased' => array_slice($types, 0, 2),
            default => array_slice($types, 0, 4),
        };
    }

    public static function reference(string $prefix, Project $project, int $sequence = 1): string
    {
        return sprintf('%s-%s-%05d-%02d', $prefix, $project->year, $project->id, $sequence);
    }

    public static function faker(Project $project): Generator
    {
        $faker = FakerFactory::create();
        $faker->seed(((int) $project->id * 10_000) + (int) $project->year);

        return $faker;
    }
}
