<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcurementControl extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'controlled_date',
        'abc',
        'remarks',
        'user_id',
        'project_id',
    ];

    protected $casts = [
        'controlled_date' => 'date',
        'abc' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
