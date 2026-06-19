<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LegalNode extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'node_id';

    protected $fillable = [
        'act_id',
        'parent_node_id',
        'node_type',
        'node_no',
        'heading',
        'marginal_note',
        'text_content',
        'sort_order',
        'level',
        'has_children',
        'status',
        'effective_date',
        'repealed_date',
        'replaced_by_node_id',
        'replaces_node_id',
        'version_no',
    ];

    protected $casts = [
        'has_children' => 'boolean',
        'effective_date' => 'date',
        'repealed_date' => 'date',
    ];

    public function act(): BelongsTo
    {
        return $this->belongsTo(Act::class, 'act_id', 'act_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(LegalNode::class, 'parent_node_id', 'node_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(LegalNode::class, 'parent_node_id', 'node_id')
            ->orderBy('sort_order');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function replacedByNode(): BelongsTo
    {
        return $this->belongsTo(LegalNode::class, 'replaced_by_node_id', 'node_id');
    }

    public function replacesNode(): BelongsTo
    {
        return $this->belongsTo(LegalNode::class, 'replaces_node_id', 'node_id');
    }

    public function outgoingReferences(): HasMany
    {
        return $this->hasMany(CrossReference::class, 'source_node_id', 'node_id');
    }

    public function incomingReferences(): HasMany
    {
        return $this->hasMany(CrossReference::class, 'target_node_id', 'node_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRepealed($query)
    {
        return $query->where('status', 'repealed');
    }

    public function getFullPath(): string
    {
        $parts = [];
        $node = $this;

        while ($node) {
            $label = $node->node_type;
            if ($node->node_no) {
                $label .= ' ' . $node->node_no;
            }
            array_unshift($parts, $label);
            $node = $node->parent_node_id ? $node->parent : null;
        }

        return implode(' > ', $parts);
    }
}
