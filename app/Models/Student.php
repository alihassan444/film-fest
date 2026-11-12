<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'name',
        'department',
        'semester',
        'designation',
        'head_id',
    ];

    public function head(): BelongsTo
    {
        return $this->belongsTo(self::class, 'head_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(self::class, 'head_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
