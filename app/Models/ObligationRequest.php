<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObligationRequest extends Model
{
    protected $fillable = [
        'controlled_date',
        'number',
        'amount',
        'user_id',
        'project_id',
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
