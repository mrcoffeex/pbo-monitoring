<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Implementation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'start_date',
        'end_date',
        'date',
        'percentage',
        'remarks',
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
