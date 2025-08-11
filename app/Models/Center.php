<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    protected $fillable = [
        'funds',
        'code',
        'name'
    ];

    protected $casts = [
        'funds' => 'array'
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
