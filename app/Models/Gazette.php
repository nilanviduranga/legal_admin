<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gazette extends Model
{
    protected $primaryKey = 'gazette_id';

    protected $fillable = [
        'gazette_no',
        'gazette_date',
        'title',
        'status',
    ];

    protected $casts = [
        'gazette_date' => 'date',
    ];

    public function acts(): HasMany
    {
        return $this->hasMany(Act::class, 'gazette_id', 'gazette_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
