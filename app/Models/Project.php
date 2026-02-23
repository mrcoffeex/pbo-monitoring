<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
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
        return  (float) $balance;
    }
}
