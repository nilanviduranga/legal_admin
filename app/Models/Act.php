<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Act extends Model
{
    protected $primaryKey = 'act_id';

    protected $fillable = [
        'gazette_id',
        'act_no',
        'act_year',
        'title',
        'short_title',
        'commencement_date',
        'status',
        'replaced_by_act_id',
        'replaces_act_id',
        'current_version',
    ];

    protected $casts = [
        'commencement_date' => 'date',
        'current_version' => 'boolean',
    ];

    public function gazette(): BelongsTo
    {
        return $this->belongsTo(Gazette::class, 'gazette_id', 'gazette_id');
    }

    public function legalNodes(): HasMany
    {
        return $this->hasMany(LegalNode::class, 'act_id', 'act_id');
    }

    public function replacedBy(): BelongsTo
    {
        return $this->belongsTo(Act::class, 'replaced_by_act_id', 'act_id');
    }

    public function replaces(): BelongsTo
    {
        return $this->belongsTo(Act::class, 'replaces_act_id', 'act_id');
    }

    public function topLevelNodes(): HasMany
    {
        return $this->hasMany(LegalNode::class, 'act_id', 'act_id')
            ->whereNull('parent_node_id')
            ->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRepealed($query)
    {
        return $query->where('status', 'repealed');
    }
}
