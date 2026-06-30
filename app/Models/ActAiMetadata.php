<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActAiMetadata extends Model
{
    protected $table = 'act_ai_metadata';

    protected $fillable = [
        'act_id',
        'summary',
        'keywords',
        'legal_domains',
        'regulated_activities',
        'subjects',
        'generated_at',
    ];

    protected $casts = [
        'keywords'             => 'array',
        'legal_domains'        => 'array',
        'regulated_activities' => 'array',
        'subjects'             => 'array',
        'generated_at'         => 'datetime',
    ];

    public function act(): BelongsTo
    {
        return $this->belongsTo(Act::class, 'act_id', 'act_id');
    }
}
