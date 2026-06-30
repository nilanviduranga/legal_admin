<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActStatistic extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'act_id',
        'sections_count',
        'subsections_count',
        'parts_count',
        'chapters_count',
        'paragraphs_count',
        'items_count',
        'total_nodes_count',
        'computed_at',
    ];

    protected $casts = [
        'computed_at' => 'datetime',
    ];

    public function act(): BelongsTo
    {
        return $this->belongsTo(Act::class, 'act_id', 'act_id');
    }
}
