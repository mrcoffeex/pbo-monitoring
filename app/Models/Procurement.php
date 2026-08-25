<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Procurement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'ib_number',
        'pre_procurement_conference',
        'pre_bid_conference',
        'bid_opening',
        'ber',
        'post_qua_date',
        'remarks',
        'noa_date_received',
        'contract_amount',
        'contractor',
        'ntp_number',
        'ntp_date',
        'contract_duration',
        'user_id',
        'project_id',
    ];

    protected $casts = [
        'bid_opening' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
