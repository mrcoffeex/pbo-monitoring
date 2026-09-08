<?php

namespace App\Models;

use App\Enums\ProcessStage;
use App\Models\Concerns\EnforcesProjectWorkflow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Implementation extends Model
{
    use EnforcesProjectWorkflow;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'start_date',
        'end_date',
        'date',
        'percentage',
        'remarks',
        'coordinates',
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

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return array<int, string>
     */
    public static function optionsForProject(?int $projectId): array
    {
        if ($projectId === null) {
            return [];
        }

        return static::query()
            ->where('project_id', $projectId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get()
            ->mapWithKeys(fn (Implementation $implementation): array => [
                $implementation->id => $implementation->paymentOptionLabel(),
            ])
            ->all();
    }

    public function paymentOptionLabel(): string
    {
        $date = filled($this->date)
            ? Carbon::parse($this->date)->format('M d, Y')
            : 'No date';

        $percentage = is_numeric($this->percentage)
            ? rtrim(rtrim(number_format((float) $this->percentage, 2, '.', ''), '0'), '.')
            : '0';

        $remarks = filled($this->remarks)
            ? ' · '.Str::limit((string) $this->remarks, 40)
            : '';

        return "{$date} · {$percentage}%{$remarks}";
    }

    public static function workflowStage(): ProcessStage
    {
        return ProcessStage::Implementation;
    }
}
