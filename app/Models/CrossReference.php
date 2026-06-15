<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrossReference extends Model
{
    protected $primaryKey = 'reference_id';

    protected $fillable = [
        'source_node_id',
        'target_node_id',
        'reference_text',
        'reference_type',
    ];

    public function sourceNode(): BelongsTo
    {
        return $this->belongsTo(LegalNode::class, 'source_node_id', 'node_id');
    }

    public function targetNode(): BelongsTo
    {
        return $this->belongsTo(LegalNode::class, 'target_node_id', 'node_id');
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('reference_type', $type);
    }
}
