<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TechnicalWorkingGroup extends Model
{
    use HasFactory;
    use SoftDeletes;

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
