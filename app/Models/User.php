<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{

    use HasFactory, Notifiable, HasRoles, HasPanelShield, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
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
}
