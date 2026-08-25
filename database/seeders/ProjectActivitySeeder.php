<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\CarbonInterface;
use Database\Seeders\Support\ProjectSeedData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

class ProjectActivitySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->find(ProjectSeedData::userId());

        foreach (ProjectSeedData::projects() as $project) {
            $timeline = ProjectSeedData::timeline($project);
            $project->load([
                'pre_procurements',
                'purchase_requests',
                'technical_working_groups',
                'procurement_controls',
                'purchase_request_controls',
                'procurements',
                'obligation_requests',
                'implementations',
                'payments',
            ]);

            $this->logCreated($project, $user, $timeline['project_created'], [
                'code' => $project->code,
                'name' => $project->name,
                'year' => $project->year,
                'status' => $project->status,
                'type' => $project->type,
                'funds' => $project->funds,
                'appropriation' => $project->appropriation,
                'allotment' => $project->allotment,
            ]);

            $this->logCollection($project->pre_procurements, $user, $timeline['pre_procurement']);
            $this->logCollection($project->purchase_requests, $user, $timeline['pr_received']);
            $this->logCollection($project->technical_working_groups, $user, $timeline['twg_review']);
            $this->logCollection($project->procurement_controls, $user, $timeline['pmo_controlled']);
            $this->logCollection($project->purchase_request_controls, $user, $timeline['prc_controlled']);
            $this->logCollection($project->procurements, $user, $timeline['pre_proc_conference']);
            $this->logCollection($project->obligation_requests, $user, $timeline['obr']);

            foreach ($project->implementations as $index => $implementation) {
                $occurredAt = $implementation->created_at ?? $timeline['impl_start'];

                $this->logCreated($implementation, $user, $occurredAt);

                if ($index > 0) {
                    $previous = $project->implementations[$index - 1];

                    $this->logUpdated($implementation, $user, $occurredAt, [
                        'percentage' => $previous->percentage,
                    ], [
                        'percentage' => $implementation->percentage,
                    ]);
                }
            }

            foreach ($project->payments as $index => $payment) {
                $occurredAt = $timeline['payments'][$index] ?? $payment->created_at ?? $timeline['impl_start'];
                $this->logCreated($payment, $user, $occurredAt);
            }

            if ($project->status === 'released') {
                $this->logUpdated($project, $user, $timeline['ntp'], [
                    'status' => 'unreleased',
                ], [
                    'status' => 'released',
                ]);
            }
        }
    }

    /**
     * @param  Collection<int, Model>  $records
     */
    protected function logCollection(Collection $records, User $user, CarbonInterface $occurredAt): void
    {
        foreach ($records as $record) {
            $this->logCreated($record, $user, $record->created_at ?? $occurredAt);
        }
    }

    /**
     * @param  array<string, mixed>|null  $attributes
     */
    protected function logCreated(Model $subject, User $user, CarbonInterface $occurredAt, ?array $attributes = null): void
    {
        if ($this->alreadyLogged($subject, 'created')) {
            return;
        }

        $this->store($subject, $user, 'created', $occurredAt, [
            'attributes' => $attributes ?? $this->visibleAttributes($subject),
        ]);
    }

    /**
     * @param  array<string, mixed>  $old
     * @param  array<string, mixed>  $attributes
     */
    protected function logUpdated(Model $subject, User $user, CarbonInterface $occurredAt, array $old, array $attributes): void
    {
        if ($this->alreadyLogged($subject, 'updated')) {
            return;
        }

        $this->store($subject, $user, 'updated', $occurredAt, [
            'old' => $old,
            'attributes' => $attributes,
        ]);
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    protected function store(Model $subject, User $user, string $event, CarbonInterface $occurredAt, array $properties): void
    {
        $activity = new Activity;
        $activity->log_name = config('activitylog.default_log_name');
        $activity->description = $event;
        $activity->event = $event;
        $activity->subject()->associate($subject);
        $activity->causer()->associate($user);
        $activity->properties = $properties;
        $activity->created_at = $occurredAt;
        $activity->updated_at = $occurredAt;
        $activity->save();
    }

    protected function alreadyLogged(Model $subject, string $event): bool
    {
        return Activity::query()
            ->where('subject_type', $subject::class)
            ->where('subject_id', $subject->getKey())
            ->where('event', $event)
            ->exists();
    }

    /**
     * @return array<string, mixed>
     */
    protected function visibleAttributes(Model $subject): array
    {
        return collect($subject->toArray())
            ->except(['id', 'user_id', 'project_id', 'created_at', 'updated_at', 'deleted_at', 'user', 'project'])
            ->all();
    }
}
