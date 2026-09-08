<?php

namespace App\Models;

use App\Enums\ProcessStage;
use App\Models\Concerns\EnforcesProjectWorkflow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequest extends Model
{
    use EnforcesProjectWorkflow;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'received_date',
        'pr_number',
        'remarks',
        'forward_twg_date',
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
        return ProcessStage::PurchaseRequest;
    }
}
