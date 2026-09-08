<?php

namespace App\Models;

use App\Enums\ProcessStage;
use App\Models\Concerns\EnforcesProjectWorkflow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use EnforcesProjectWorkflow;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'date',
        'amount',
        'payable_reference',
        'payment_reference',
        'check_number',
        'check_date',
        'user_id',
        'project_id',
        'implementation_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function implementation()
    {
        return $this->belongsTo(Implementation::class);
    }

    public static function workflowStage(): ProcessStage
    {
        return ProcessStage::Payment;
    }
}
