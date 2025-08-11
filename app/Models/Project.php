<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'center_id',
        'year',
        'funds',
        'appropriation',
        'allotment',
        'status',
        'user_id',
    ];

    protected $casts = [
        'funds' => 'array',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchase_requests()
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function technical_working_groups()
    {
        return $this->hasMany(TechnicalWorkingGroup::class);
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
}
