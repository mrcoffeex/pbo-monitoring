<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicalWorkingGroup extends Model
{
    protected $fillable = [
        'review_date',
        'review_remarks',
        'controlled_date',
        'abc',
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
