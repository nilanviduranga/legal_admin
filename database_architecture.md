# Database Architecture — Legal Admin

This document describes every table, column, relationship, and seeding convention used in the **legal_admin** Laravel application. It is intended to be fed to an LLM alongside raw Gazette / Act text so the LLM can generate correct, idiomatic seeders.

---

## Table of Contents

1. [Entity Relationship Overview](#1-entity-relationship-overview)
2. [Table Specifications](#2-table-specifications)
   - [gazettes](#21-gazettes)
   - [acts](#22-acts)
   - [legal_nodes](#23-legal_nodes)
   - [cross_references](#24-cross_references)
   - [act_ai_metadata](#25-act_ai_metadata)
   - [act_statistics](#26-act_statistics)
   - [case_laws](#27-case_laws)
3. [Node-Type Hierarchy](#3-node-type-hierarchy)
4. [Seeder Conventions](#4-seeder-conventions)
5. [Seeder Skeleton Template](#5-seeder-skeleton-template)
6. [Cross-Reference Guide](#6-cross-reference-guide)
7. [Checklist for New Act Seeders](#7-checklist-for-new-act-seeders)

---

## 1. Entity Relationship Overview

```
gazettes (1) ──────────────── (N) acts
acts     (1) ──────────────── (N) legal_nodes
acts     (1) ──────────────── (1) act_ai_metadata
acts     (1) ──────────────── (1) act_statistics
acts     self-references ──── replaced_by_act_id / replaces_act_id

legal_nodes (1) ──────── (N) legal_nodes  [parent_node_id — recursive tree]
legal_nodes self-refs ── replaced_by_node_id / replaces_node_id
legal_nodes (N) ──────── (N) legal_nodes  [via cross_references]
```

---

## 2. Table Specifications

### 2.1 `gazettes`

Stores the Gazette issue in which an Act was published.

| Column         | Type               | Constraints              | Notes                                        |
|----------------|--------------------|--------------------------|----------------------------------------------|
| `gazette_id`   | BIGINT UNSIGNED PK | AUTO_INCREMENT           | Primary key                                  |
| `gazette_no`   | VARCHAR(50)        | NOT NULL                 | e.g. `"Supplement to Part II"`, `"1234/18"`  |
| `gazette_date` | DATE               | NOT NULL                 | Publication date of the Gazette issue        |
| `title`        | VARCHAR(500)       | NOT NULL                 | Full descriptive title of the Gazette issue  |
| `status`       | ENUM               | `active` \| `inactive`   | Default `active`                             |
| `created_at`   | TIMESTAMP          |                          | Laravel managed                              |
| `updated_at`   | TIMESTAMP          |                          | Laravel managed                              |

**Seeding tip:** Use `Gazette::firstOrCreate(['gazette_no' => '...'], [...])` so re-runs are idempotent.

---

### 2.2 `acts`

One row per Act (or per version of an Act when amendments produce a new version).

| Column               | Type                | Constraints                         | Notes                                                    |
|----------------------|---------------------|-------------------------------------|----------------------------------------------------------|
| `act_id`             | BIGINT UNSIGNED PK  | AUTO_INCREMENT                      | Primary key                                              |
| `gazette_id`         | BIGINT UNSIGNED FK  | NOT NULL → `gazettes.gazette_id`    | CASCADE DELETE                                           |
| `act_no`             | SMALLINT UNSIGNED   | NOT NULL                            | The Act number in the given year (e.g. `9`)              |
| `act_year`           | SMALLINT UNSIGNED   | NOT NULL                            | Year the Act was enacted (e.g. `2003`)                   |
| `title`              | VARCHAR(500)        | NOT NULL                            | Full official title                                      |
| `short_title`        | VARCHAR(200)        | NULLABLE                            | e.g. `"Consumer Affairs Authority Act, No. 9 of 2003"`  |
| `commencement_date`  | DATE                | NULLABLE                            | Date the Act came into force (may differ from gazette)   |
| `status`             | ENUM                | `active` \| `repealed` \| `amended` | Default `active`                                         |
| `replaced_by_act_id` | BIGINT UNSIGNED FK  | NULLABLE → `acts.act_id`            | NULL on delete; points to the newer Act that replaces this one |
| `replaces_act_id`    | BIGINT UNSIGNED FK  | NULLABLE → `acts.act_id`            | NULL on delete; points to the older Act this one repeals |
| `current_version`    | BOOLEAN             | DEFAULT `true`                      | `false` for superseded versions                         |
| `created_at`         | TIMESTAMP           |                                     | Laravel managed                                          |
| `updated_at`         | TIMESTAMP           |                                     | Laravel managed                                          |

**Seeding tip:** Use `Act::firstOrCreate(['act_no' => X, 'act_year' => Y], [...])`.

---

### 2.3 `legal_nodes`

The core table. Stores every structural element of an Act as a **recursive tree**. Each node belongs to one Act and optionally to a parent node.

| Column                | Type                      | Constraints                           | Notes                                                                               |
|-----------------------|---------------------------|---------------------------------------|-------------------------------------------------------------------------------------|
| `node_id`             | BIGINT UNSIGNED PK        | AUTO_INCREMENT                        | Primary key                                                                         |
| `act_id`              | BIGINT UNSIGNED FK        | NOT NULL → `acts.act_id`              | CASCADE DELETE                                                                      |
| `parent_node_id`      | BIGINT UNSIGNED FK        | NULLABLE → `legal_nodes.node_id`      | NULL = root node; CASCADE DELETE                                                    |
| `node_type`           | ENUM                      | NOT NULL                              | See [Node-Type Hierarchy](#3-node-type-hierarchy)                                   |
| `node_no`             | VARCHAR(20)               | NULLABLE                              | Display number: `"I"`, `"1"`, `"(1)"`, `"(a)"`, `"(i)"` etc.                      |
| `heading`             | VARCHAR(500)              | NULLABLE                              | Section/Part heading (enacted text)                                                 |
| `marginal_note`       | VARCHAR(1500)             | NULLABLE                              | Side-note printed in the page margin (editorial, not enacted)                       |
| `text_content`        | LONGTEXT                  | NULLABLE                              | Full verbatim enacted text of this node (leaf text)                                 |
| `sort_order`          | INT UNSIGNED              | DEFAULT `0`                           | Position among siblings; auto-incremented per parent in seeders                    |
| `level`               | TINYINT UNSIGNED          | NULLABLE                              | Depth in the tree: root nodes = `1`, children = parent+1                            |
| `has_children`        | BOOLEAN                   | DEFAULT `false`                       | Updated after all nodes inserted via a single SQL `UPDATE`                          |
| `status`              | ENUM                      | `active` \| `repealed` \| `amended`   | Default `active`                                                                    |
| `effective_date`      | DATE                      | NULLABLE                              | When this node version became effective                                             |
| `repealed_date`       | DATE                      | NULLABLE                              | When this node was repealed                                                         |
| `replaced_by_node_id` | BIGINT UNSIGNED FK        | NULLABLE → `legal_nodes.node_id`      | NULL on delete                                                                      |
| `replaces_node_id`    | BIGINT UNSIGNED FK        | NULLABLE → `legal_nodes.node_id`      | NULL on delete                                                                      |
| `version_no`          | TINYINT UNSIGNED          | DEFAULT `1`                           | Incremented when node text is amended                                               |
| `deleted_at`          | TIMESTAMP (soft delete)   | NULLABLE                              | Soft-delete via `SoftDeletes` trait                                                 |
| `created_at`          | TIMESTAMP                 |                                       | Laravel managed                                                                     |
| `updated_at`          | TIMESTAMP                 |                                       | Laravel managed                                                                     |

**Indexes:**
- `(act_id, parent_node_id, sort_order)` — tree traversal
- `(act_id, node_type)` — filter by type within an Act
- FULLTEXT on `(text_content, heading)` — full-text search

---

### 2.4 `cross_references`

Records an explicit statutory cross-reference from one node to another (e.g. "as provided in section 12").

| Column           | Type               | Constraints                          | Notes                                                                   |
|------------------|--------------------|--------------------------------------|-------------------------------------------------------------------------|
| `reference_id`   | BIGINT UNSIGNED PK | AUTO_INCREMENT                       | Primary key                                                             |
| `source_node_id` | BIGINT UNSIGNED FK | NOT NULL → `legal_nodes.node_id`     | CASCADE DELETE; the node containing the reference                       |
| `target_node_id` | BIGINT UNSIGNED FK | NOT NULL → `legal_nodes.node_id`     | CASCADE DELETE; the node being referred to                              |
| `reference_text` | VARCHAR(500)       | NULLABLE                             | Quote from the Act explaining the reference                             |
| `reference_type` | ENUM               | NOT NULL                             | `refers_to` \| `subject_to` \| `as_amended_by` \| `repeals` \| `saves` \| `cross_applies` |
| `created_at`     | TIMESTAMP          |                                      | Laravel managed                                                         |
| `updated_at`     | TIMESTAMP          |                                      | Laravel managed                                                         |

**Unique constraint:** `(source_node_id, target_node_id)` — only one cross-reference record per directed pair.

**Seeding tip:** Collect SECTION `node_id` values in a `$sections` array (keyed by section number string) during node creation, then insert all cross-references in a final pass using `CrossReference::firstOrCreate(...)`.

---

### 2.5 `act_ai_metadata`

One row per Act, auto-generated by an LLM pipeline (populated separately, not by Act seeders).

| Column                 | Type             | Constraints                        | Notes                           |
|------------------------|------------------|------------------------------------|---------------------------------|
| `id`                   | BIGINT UNSIGNED PK | AUTO_INCREMENT                   | Primary key                     |
| `act_id`               | BIGINT UNSIGNED FK | UNIQUE → `acts.act_id`           | CASCADE DELETE                  |
| `summary`              | TEXT             | NULLABLE                           | Short plain-language summary    |
| `keywords`             | JSON             | NULLABLE                           | Array of keyword strings        |
| `legal_domains`        | JSON             | NULLABLE                           | e.g. `["consumer law", "trade"]`|
| `regulated_activities` | JSON             | NULLABLE                           | Activities regulated by the Act |
| `subjects`             | JSON             | NULLABLE                           | Subject entities/persons        |
| `generated_at`         | TIMESTAMP        | NULLABLE                           | When the LLM generated this     |
| `created_at`           | TIMESTAMP        |                                    | Laravel managed                 |
| `updated_at`           | TIMESTAMP        |                                    | Laravel managed                 |

**Note:** Act seeders do NOT populate this table. It is populated by a separate AI pipeline after the Act is seeded.

---

### 2.6 `act_statistics`

Denormalised node count cache; one row per Act, recomputed after seeding.

| Column              | Type               | Constraints                    | Notes                        |
|---------------------|--------------------|--------------------------------|------------------------------|
| `id`                | BIGINT UNSIGNED PK | AUTO_INCREMENT                 | Primary key                  |
| `act_id`            | BIGINT UNSIGNED FK | UNIQUE → `acts.act_id`         | CASCADE DELETE               |
| `sections_count`    | INT UNSIGNED       | DEFAULT `0`                    |                              |
| `subsections_count` | INT UNSIGNED       | DEFAULT `0`                    |                              |
| `parts_count`       | INT UNSIGNED       | DEFAULT `0`                    |                              |
| `chapters_count`    | INT UNSIGNED       | DEFAULT `0`                    |                              |
| `paragraphs_count`  | INT UNSIGNED       | DEFAULT `0`                    |                              |
| `items_count`       | INT UNSIGNED       | DEFAULT `0`                    |                              |
| `total_nodes_count` | INT UNSIGNED       | DEFAULT `0`                    |                              |
| `computed_at`       | TIMESTAMP          | NULLABLE                       | When counts were last computed|

**Note:** Populated by a separate compute job, not by Act seeders.

---

### 2.7 `case_laws`

Standalone case law records (not linked to specific legal_nodes in the current schema).

| Column      | Type               | Constraints                       | Notes                        |
|-------------|--------------------|-----------------------------------|------------------------------|
| `id`        | BIGINT UNSIGNED PK | AUTO_INCREMENT                    | Laravel default `id`         |
| `case_name` | VARCHAR(500)       | NOT NULL                          | Full case name/citation      |
| `content`   | LONGTEXT           | NOT NULL                          | Full judgment text           |
| `status`    | ENUM               | `active` \| `overruled`           | Default `active`             |
| `created_at`| TIMESTAMP          |                                   | Laravel managed              |
| `updated_at`| TIMESTAMP          |                                   | Laravel managed              |

---

## 3. Node-Type Hierarchy

The `legal_nodes.node_type` ENUM defines the structural element type. Typical tree depth ordering (parent → children):

```
PREAMBLE          (root, no parent)
PART              (root, no parent)
  CHAPTER         (child of PART, or root)
    SECTION       (child of PART / CHAPTER, or root)
      SUBSECTION  (child of SECTION)
        PARAGRAPH (child of SECTION or SUBSECTION)
          SUBPARAGRAPH (child of PARAGRAPH)
            ITEM  (child of SUBPARAGRAPH or PARAGRAPH)
SCHEDULE          (root, no parent)
  PARAGRAPH       (child of SCHEDULE)
    SUBPARAGRAPH  (child of PARAGRAPH in SCHEDULE)
DEFINITIONS       (root, special section for definitions)
```

### Numbering conventions

| node_type     | `node_no` format        | Examples                |
|---------------|-------------------------|-------------------------|
| PART          | Roman numeral           | `"I"`, `"II"`, `"III"` |
| CHAPTER       | Roman or Arabic         | `"I"`, `"1"`           |
| SECTION       | Arabic numeral          | `"1"`, `"42"`, `"75"`  |
| SUBSECTION    | Arabic in parens        | `"(1)"`, `"(2)"`       |
| PARAGRAPH     | Lowercase letter        | `"(a)"`, `"(b)"`       |
| SUBPARAGRAPH  | Roman in parens         | `"(i)"`, `"(ii)"`      |
| ITEM          | Letter/number variants  | `"A"`, `"1"`           |
| SCHEDULE      | Usually `null`          | Single schedule         |
| PREAMBLE      | Usually `null`          | Single preamble         |
| DEFINITIONS   | Usually `null`          | Single definitions      |

---

## 4. Seeder Conventions

### 4.1 Class structure

```php
class SomeActSeeder extends Seeder
{
    /** @var array<string, int>  section number string => node_id */
    private array $sections = [];

    /** @var array<int|string, int>  parent_node_id (or 0 for root) => running sort counter */
    private array $sortCounters = [];
```

### 4.2 `node()` helper

Every node is created through a `node()` helper that auto-increments `sort_order` per parent and computes `level`:

```php
private function node(
    Act $act,
    ?LegalNode $parent,
    string $type,
    ?string $number,
    ?string $marginalNote = null,
    ?string $text = null
): LegalNode {
    $parentId = $parent?->node_id;
    $level    = $parent ? ($parent->level + 1) : 1;

    $key = $parentId ?? 0;
    if (!isset($this->sortCounters[$key])) {
        $this->sortCounters[$key] = 0;
    }
    $this->sortCounters[$key]++;

    $node = LegalNode::create([
        'act_id'         => $act->act_id,
        'parent_node_id' => $parentId,
        'level'          => $level,
        'node_type'      => $type,
        'node_no'        => $number,
        'marginal_note'  => $marginalNote,
        'text_content'   => $text,
        'sort_order'     => $this->sortCounters[$key],
    ]);

    // Track section node_ids for cross-reference wiring
    if ($type === 'SECTION' && $number !== null) {
        $this->sections[$number] = $node->node_id;
    }

    return $node;
}
```

### 4.3 `section()` convenience wrapper

```php
private function section(
    Act $act,
    ?LegalNode $parent,
    string $number,
    ?string $text,
    ?string $marginalNote = null
): LegalNode {
    return $this->node($act, $parent, 'SECTION', $number, $marginalNote, $text);
}
```

### 4.4 Wipe-and-reseed pattern

Before inserting nodes, wipe any previous run of this Act (cross-refs first due to FK):

```php
$nodeIds = LegalNode::withTrashed()->where('act_id', $act->act_id)->pluck('node_id');
if ($nodeIds->isNotEmpty()) {
    CrossReference::whereIn('source_node_id', $nodeIds)
        ->orWhereIn('target_node_id', $nodeIds)
        ->delete();
    LegalNode::withTrashed()->where('act_id', $act->act_id)->forceDelete();
}
$this->sections     = [];
$this->sortCounters = [];
```

### 4.5 `has_children` update

After all nodes are inserted, run one SQL statement to mark parents:

```php
DB::statement("
    UPDATE legal_nodes
    SET has_children = 1
    WHERE act_id = ?
      AND node_id IN (
          SELECT parent_id FROM (
              SELECT DISTINCT parent_node_id AS parent_id
              FROM legal_nodes
              WHERE act_id = ?
                AND parent_node_id IS NOT NULL
                AND deleted_at IS NULL
          ) AS parents
      )
", [$act->act_id, $act->act_id]);
```

### 4.6 Wrapping in a transaction

All seeding must be wrapped in `DB::transaction(function () { ... })` to ensure atomicity.

### 4.7 `text_content` vs `heading` vs `marginal_note`

| Field          | What goes here                                                                                     |
|----------------|----------------------------------------------------------------------------------------------------|
| `heading`      | Official heading printed as part of the enacted text (e.g. a PART heading or CHAPTER title)       |
| `marginal_note`| Side-note printed in the page margin — editorial, not enacted. Sits on SECTION or SCHEDULE nodes  |
| `text_content` | The actual body text of the node (may be `null` for container nodes like PART, CHAPTER, SECTION with subsections) |

A SECTION that has subsections typically has `text_content = null` and a `marginal_note` describing it. The text is split into child SUBSECTION nodes.

A SECTION with no subsections carries its full text in `text_content` directly.

---

## 5. Seeder Skeleton Template

Use this skeleton when generating a new Act seeder:

```php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Act;
use App\Models\CrossReference;
use App\Models\Gazette;
use App\Models\LegalNode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * [Act Name], No. [X] of [YYYY].
 * Source: Gazette [gazette_no], [gazette_date].
 */
class [ActName]Seeder extends Seeder
{
    /** @var array<string, int> */
    private array $sections = [];

    /** @var array<int|string, int> */
    private array $sortCounters = [];

    public function run(): void
    {
        DB::transaction(function () {
            $gazette = Gazette::firstOrCreate(
                ['gazette_no' => '[GAZETTE_NO]'],
                [
                    'gazette_date' => '[YYYY-MM-DD]',
                    'title'        => '[Full Gazette Title]',
                    'status'       => 'active',
                ]
            );

            $act = Act::firstOrCreate(
                ['act_no' => [ACT_NO], 'act_year' => [YEAR]],
                [
                    'gazette_id'        => $gazette->gazette_id,
                    'title'             => '[Full Act Title]',
                    'short_title'       => '[Short Title, No. X of YYYY]',
                    'commencement_date' => '[YYYY-MM-DD]',
                    'status'            => 'active',
                    'current_version'   => true,
                ]
            );

            // Wipe previous seed
            $nodeIds = LegalNode::withTrashed()->where('act_id', $act->act_id)->pluck('node_id');
            if ($nodeIds->isNotEmpty()) {
                CrossReference::whereIn('source_node_id', $nodeIds)
                    ->orWhereIn('target_node_id', $nodeIds)
                    ->delete();
                LegalNode::withTrashed()->where('act_id', $act->act_id)->forceDelete();
            }

            $this->sections     = [];
            $this->sortCounters = [];

            // ── Seed structural parts in document order ──
            $this->seedPreamble($act);
            $this->seedPartI($act);
            // ... seedPartII, seedPartIII, seedSchedule, etc.

            $this->seedCrossReferences();

            DB::statement("
                UPDATE legal_nodes
                SET has_children = 1
                WHERE act_id = ?
                  AND node_id IN (
                      SELECT parent_id FROM (
                          SELECT DISTINCT parent_node_id AS parent_id
                          FROM legal_nodes
                          WHERE act_id = ?
                            AND parent_node_id IS NOT NULL
                            AND deleted_at IS NULL
                      ) AS parents
                  )
            ", [$act->act_id, $act->act_id]);
        });
    }

    // ── PREAMBLE ──────────────────────────────────────────────────────────────

    private function seedPreamble(Act $act): void
    {
        $preamble = $this->node(
            $act, null, 'PREAMBLE', null, null,
            '[Full preamble text verbatim]',
            'Preamble.'
        );
        $this->sections['PREAMBLE'] = $preamble->node_id;

        // Section 1 (short title) lives under preamble
        $this->section($act, $preamble, '1',
            'This Act may be cited as the [Title], No. [X] of [YYYY].',
            'Short title.'
        );
    }

    // ── PART I ───────────────────────────────────────────────────────────────

    private function seedPartI(Act $act): void
    {
        $part = $this->node($act, null, 'PART', 'I', '[PART I HEADING]');

        // Section 2
        $s2 = $this->section($act, $part, '2', null, '[Marginal note for s2].');
        $this->node($act, $s2, 'SUBSECTION', '(1)', null, '[text of s 2(1)]');
        $this->node($act, $s2, 'SUBSECTION', '(2)', null, '[text of s 2(2)]');

        // Section 3 — with paragraphs
        $s3 = $this->section($act, $part, '3', '[Full section text]—', '[Marginal note].');
        $this->node($act, $s3, 'PARAGRAPH', '(a)', null, '[paragraph (a) text]');
        $this->node($act, $s3, 'PARAGRAPH', '(b)', null, '[paragraph (b) text]');
    }

    // ── SCHEDULE ─────────────────────────────────────────────────────────────

    private function seedSchedule(Act $act): void
    {
        $schedule = $this->node($act, null, 'SCHEDULE', null, '[Schedule heading]');
        $this->sections['SCHEDULE'] = $schedule->node_id;

        $p1 = $this->node($act, $schedule, 'PARAGRAPH', '1', '[Para 1 heading]', '[Para 1 text]');
        // ... further paragraphs
    }

    // ── CROSS-REFERENCES ─────────────────────────────────────────────────────

    private function seedCrossReferences(): void
    {
        // [source_section_key, target_section_key, reference_type, reference_text]
        $refs = [
            ['2', '3', 'refers_to', 's 2(1): "members under section 3"'],
            // ... more
        ];

        foreach ($refs as [$srcKey, $tgtKey, $type, $text]) {
            if (!isset($this->sections[$srcKey]) || !isset($this->sections[$tgtKey])) {
                continue;
            }
            CrossReference::firstOrCreate(
                [
                    'source_node_id' => $this->sections[$srcKey],
                    'target_node_id' => $this->sections[$tgtKey],
                ],
                [
                    'reference_type' => $type,
                    'reference_text' => $text,
                ]
            );
        }
    }

    // ── HELPERS ───────────────────────────────────────────────────────────────

    private function section(Act $act, ?LegalNode $parent, string $number, ?string $text, ?string $marginalNote = null): LegalNode
    {
        return $this->node($act, $parent, 'SECTION', $number, $marginalNote, $text);
    }

    private function node(Act $act, ?LegalNode $parent, string $type, ?string $number, ?string $marginalNote = null, ?string $text = null): LegalNode
    {
        $parentId = $parent?->node_id;
        $level    = $parent ? ($parent->level + 1) : 1;

        $key = $parentId ?? 0;
        if (!isset($this->sortCounters[$key])) {
            $this->sortCounters[$key] = 0;
        }
        $this->sortCounters[$key]++;

        $node = LegalNode::create([
            'act_id'         => $act->act_id,
            'parent_node_id' => $parentId,
            'level'          => $level,
            'node_type'      => $type,
            'node_no'        => $number,
            'marginal_note'  => $marginalNote,
            'text_content'   => $text,
            'sort_order'     => $this->sortCounters[$key],
        ]);

        if ($type === 'SECTION' && $number !== null) {
            $this->sections[$number] = $node->node_id;
        }

        return $node;
    }
}
```

---

## 6. Cross-Reference Guide

### `reference_type` values

| Value           | When to use                                                                    |
|-----------------|--------------------------------------------------------------------------------|
| `refers_to`     | General "as provided in section X" or "for the purposes of section X"         |
| `subject_to`    | "subject to the provisions of section X"                                       |
| `as_amended_by` | This node's text has been amended by the target node                           |
| `repeals`       | This node expressly repeals the target node                                    |
| `saves`         | This node saves / preserves the target node from repeal                        |
| `cross_applies` | This node applies mutatis mutandis to the target node                          |

### Source key naming

- Sections: use the section number as a string: `"1"`, `"42"`, `"75A"`
- PREAMBLE: use the string `"PREAMBLE"`
- SCHEDULE: use the string `"SCHEDULE"` (or `"SCHEDULE_1"`, `"SCHEDULE_2"` for multiple schedules)
- DEFINITIONS: use the string `"DEFINITIONS"`

The `$this->sections` map is populated automatically for SECTION nodes. For PREAMBLE and SCHEDULE nodes, assign manually after creation:
```php
$this->sections['PREAMBLE'] = $preamble->node_id;
$this->sections['SCHEDULE'] = $schedule->node_id;
```

---

## 7. Checklist for New Act Seeders

When generating a seeder for a new Act, verify the following:

- [ ] `Gazette::firstOrCreate` uses `gazette_no` as the unique key
- [ ] `Act::firstOrCreate` uses `['act_no' => X, 'act_year' => Y]` as the unique key
- [ ] All text is reproduced **verbatim** from the certified Act (original spelling/typos preserved)
- [ ] `marginal_note` is used for margin notes (editorial), not `heading` or `text_content`
- [ ] `heading` is used only for enacted headings (PART headings, CHAPTER titles)
- [ ] Sections with subsections have `text_content = null` on the SECTION node; text lives in child SUBSECTION nodes
- [ ] Sections without subsections carry their full text directly in `text_content`
- [ ] `sort_order` is managed automatically by the `node()` helper — do not set it manually
- [ ] `level` is computed automatically by the `node()` helper — do not set it manually
- [ ] The wipe-and-reseed block appears before any node creation
- [ ] Cross-references are seeded in a final pass after all nodes exist
- [ ] PREAMBLE and SCHEDULE keys are manually assigned to `$this->sections`
- [ ] The seeder class is registered in `DatabaseSeeder::run()`
- [ ] The seeder is idempotent — running it twice does not duplicate data
- [ ] `has_children` is updated with the raw SQL statement at the end of `run()`
- [ ] Everything is wrapped in `DB::transaction(...)`

---

## Appendix — Model Primary Keys

> All custom primary key names must be passed explicitly when querying; they are **not** the Laravel default `id`.

| Model          | Primary Key    | Table              |
|----------------|----------------|--------------------|
| `Gazette`      | `gazette_id`   | `gazettes`         |
| `Act`          | `act_id`       | `acts`             |
| `LegalNode`    | `node_id`      | `legal_nodes`      |
| `CrossReference`| `reference_id` | `cross_references` |
| `ActAiMetadata`| `id`           | `act_ai_metadata`  |
| `ActStatistic` | `id`           | `act_statistics`   |
| `CaseLaw`      | `id`           | `case_laws`        |
