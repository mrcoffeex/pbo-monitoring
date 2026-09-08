<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Activity;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'code',
        'name',
        'year',
        'funds',
        'appropriation',
        'allotment',
        'status',
        'user_id',
    ];

    protected $casts = [
        'type' => 'array',
        'funds' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pre_procurements()
    {
        return $this->hasMany(PreProcurement::class);
    }

    public function purchase_requests()
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function technical_working_groups()
    {
        return $this->hasMany(TechnicalWorkingGroup::class);
    }

    public function procurement_controls()
    {
        return $this->hasMany(ProcurementControl::class);
    }

    public function purchase_request_controls()
    {
        return $this->hasMany(PurchaseRequestControl::class);
    }

    public function procurements()
    {
        return $this->hasMany(Procurement::class);
    }

    public function obligation_requests()
    {
        return $this->hasMany(ObligationRequest::class);
    }

    public function implementations()
    {
        return $this->hasMany(Implementation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getBalanceAttribute(): float
    {
        $totalDisbursed = $this->payments()->sum('amount') ?? 0;
        $contractAmount = $this->procurements()->sum('contract_amount') ?? 0;
        $balance = ($contractAmount > 0) ? $contractAmount - $totalDisbursed : $this->allotment;

        return (float) $balance;
    }

    public function paymentCeiling(): float
    {
        $contractAmount = (float) $this->procurements()->sum('contract_amount');

        if ($contractAmount > 0) {
            return $contractAmount;
        }

        return (float) $this->allotment;
    }

    public function paidPaymentTotal(?Payment $except = null): float
    {
        return (float) $this->payments()
            ->when(
                $except instanceof Payment && $except->exists,
                fn (Builder $query): Builder => $query->whereKeyNot($except->getKey()),
            )
            ->sum('amount');
    }

    public function remainingPaymentBalance(?Payment $except = null): float
    {
        return round(max(0, $this->paymentCeiling() - $this->paidPaymentTotal($except)), 2);
    }

    /**
     * @return array<class-string<Model>, \Illuminate\Support\Collection<int, int|string>>
     */
    public function activitySubjectMap(): array
    {
        return [
            self::class => collect([$this->id]),
            PreProcurement::class => $this->pre_procurements()->pluck('id'),
            PurchaseRequest::class => $this->purchase_requests()->pluck('id'),
            TechnicalWorkingGroup::class => $this->technical_working_groups()->pluck('id'),
            ProcurementControl::class => $this->procurement_controls()->pluck('id'),
            PurchaseRequestControl::class => $this->purchase_request_controls()->pluck('id'),
            Procurement::class => $this->procurements()->pluck('id'),
            ObligationRequest::class => $this->obligation_requests()->pluck('id'),
            Implementation::class => $this->implementations()->pluck('id'),
            Payment::class => $this->payments()->pluck('id'),
        ];
    }

    public function projectActivitiesQuery(?CarbonInterface $startDate = null, ?CarbonInterface $endDate = null): Builder
    {
        return Activity::query()
            ->with('causer')
            ->where(function (Builder $query): void {
                foreach ($this->activitySubjectMap() as $subjectType => $subjectIds) {
                    $subjectIds = collect($subjectIds)->filter()->values();

                    if ($subjectIds->isEmpty()) {
                        continue;
                    }

                    $query->orWhere(function (Builder $nested) use ($subjectType, $subjectIds): void {
                        $nested->where('subject_type', $subjectType)
                            ->whereIn('subject_id', $subjectIds);
                    });
                }
            })
            ->when($startDate, fn (Builder $query): Builder => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query): Builder => $query->where('created_at', '<=', $endDate))
            ->latest();
    }
}
