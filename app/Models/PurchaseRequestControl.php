<?php

namespace App\Models;

use App\Enums\ProcessStage;
use App\Models\Concerns\EnforcesProjectWorkflow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequestControl extends Model
{
    use EnforcesProjectWorkflow;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'controlled_date',
        'control_number',
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

    public static function workflowStage(): ProcessStage
    {
        return ProcessStage::PurchaseRequestControl;
    }
}
