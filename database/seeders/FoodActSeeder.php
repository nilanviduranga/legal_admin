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
 * Food Act, No. 26 of 1980 and its subsequent Amendments (No. 20 of 1991 & No. 29 of 2011).
 *
 * Enacted text and textual variations from amendments are treated according to the
 * system's versioning and node tracking tree specifications.
 */
class FoodActSeeder extends Seeder
{
    /** @var array<string, int> section_number => node_id for cross-references */
    private array $sections = [];

    /** @var array<int|string, int> parent_node_id => running sort counter */
    private array $sortCounters = [];

    public function run(): void
    {
        DB::transaction(function () {
            // ─────────────────────────────────────────────────────────────────
            // 1. SEED GAZETTES
            // ─────────────────────────────────────────────────────────────────
            $gazette1980 = Gazette::firstOrCreate(
                ['gazette_no' => 'Supplement to Part II of 1980-07-25'],
                [
                    'gazette_date' => '1980-07-25',
                    'title'        => 'Gazette of the Democratic Socialist Republic of Sri Lanka, July 25, 1980',
                    'status'       => 'active',
                ]
            );

            $gazette1991 = Gazette::firstOrCreate(
                ['gazette_no' => 'Supplement to Part II of 1991-04-19'],
                [
                    'gazette_date' => '1991-04-19',
                    'title'        => 'Gazette of the Democratic Socialist Republic of Sri Lanka, April 19, 1991',
                    'status'       => 'active',
                ]
            );

            $gazette2011 = Gazette::firstOrCreate(
                ['gazette_no' => 'Supplement to Part II of 2011-06-10'],
                [
                    'gazette_date' => '2011-06-10',
                    'title'        => 'Gazette of the Democratic Socialist Republic of Sri Lanka, June 10, 2011',
                    'status'       => 'active',
                ]
            );

            // ─────────────────────────────────────────────────────────────────
            // 2. SEED ACT ENTRIES
            // ─────────────────────────────────────────────────────────────────
            $act1980 = Act::firstOrCreate(
                ['act_no' => 26, 'act_year' => 1980],
                [
                    'gazette_id'        => $gazette1980->gazette_id,
                    'title'             => 'Food Act, No. 26 of 1980',
                    'short_title'       => 'Food Act, No. 26 of 1980',
                    'commencement_date' => '1980-07-17',
                    'status'            => 'amended',
                    'current_version'   => false,
                ]
            );

            $act1991 = Act::firstOrCreate(
                ['act_no' => 20, 'act_year' => 1991],
                [
                    'gazette_id'        => $gazette1991->gazette_id,
                    'title'             => 'Food (Amendment) Act, No. 20 of 1991',
                    'short_title'       => 'Food (Amendment) Act, No. 20 of 1991',
                    'commencement_date' => '1991-04-19',
                    'status'            => 'amended',
                    'current_version'   => false,
                    'replaces_act_id'   => $act1980->act_id,
                ]
            );

            $act2011 = Act::firstOrCreate(
                ['act_no' => 29, 'act_year' => 2011],
                [
                    'gazette_id'        => $gazette2011->gazette_id,
                    'title'             => 'Food (Amendment) Act, No. 29 of 2011',
                    'short_title'       => 'Food (Amendment) Act, No. 29 of 2011',
                    'commencement_date' => '2011-06-07',
                    'status'            => 'active',
                    'current_version'   => true,
                    'replaces_act_id'   => $act1991->act_id,
                ]
            );

            $act1980->update(['replaced_by_act_id' => $act1991->act_id]);
            $act1991->update(['replaced_by_act_id' => $act2011->act_id]);

            // ─────────────────────────────────────────────────────────────────
            // 3. WIPE RELEVANT NODES FROM PREVIOUS RUNS
            // ─────────────────────────────────────────────────────────────────
            $allActIds = [$act1980->act_id, $act1991->act_id, $act2011->act_id];
            $nodeIds = LegalNode::withTrashed()->whereIn('act_id', $allActIds)->pluck('node_id');
            if ($nodeIds->isNotEmpty()) {
                CrossReference::whereIn('source_node_id', $nodeIds)
                    ->orWhereIn('target_node_id', $nodeIds)
                    ->delete();
                LegalNode::withTrashed()->whereIn('act_id', $allActIds)->forceDelete();
            }

            $this->sections     = [];
            $this->sortCounters = [];

            // ─────────────────────────────────────────────────────────────────
            // 4. SEED PRIMAL BASE ACT ENTRIES (1980)
            // ─────────────────────────────────────────────────────────────────
            $preamble = $this->node($act1980, null, 'PREAMBLE', null, null,
                'AN ACT TO REGULATE AND CONTROL THE MANUFACTURE, IMPORTATION, SALE AND DISTRIBUTION OF FOOD, TO ESTABLISH A FOOD ADVISORY COMMITTEE, TO REPEAL THE FOOD AND DRUGS ACT (CHAPTER 216) AND TO PROVIDE FOR MATTERS CONNECTED THERE WITH OR INCIDENTAL THERETO.'
            );
            $this->sections['PREAMBLE_1980'] = $preamble->node_id;

            $this->section($act1980, $preamble, '1',
                'This Act may be cited as the Food Act, No. 26 of 1980, and shall come into operation on such date as the Minister may appoint by Order published in the Gazette.',
                'Short title and date of operation.'
            );

            // PART I — PROHIBITION IN RESPECT OF FOOD
            $part1 = $this->node($act1980, null, 'PART', 'I', 'PROHIBITION IN RESPECT OF FOOD');

            $s2 = $this->section($act1980, $part1, '2', null, 'Prohibition on manufacture importation, sale and distribution of food.');
            $s2_1 = $this->node($act1980, $s2, 'SUBSECTION', '(1)', null, 'No person shall manufacture, import, sell or distribute any food—');
            // Child paragraphs of subsection (1)
            $this->node($act1980, $s2_1, 'PARAGRAPH', '(a)', null, 'that has upon it any natural or added deleterious substance which renders it injurious to health;');
            $this->node($act1980, $s2_1, 'PARAGRAPH', '(b)', null, 'that is unfit for human consumption;');
            $this->node($act1980, $s2_1, 'PARAGRAPH', '(c)', null, 'that consists in whole or in part of any unclean, putrid, repugnant, decayed, decomposed or diseased animal substance or decayed vegetable substance or is insect infested;');
            $this->node($act1980, $s2_1, 'PARAGRAPH', '(d)', null, 'that is adulterated;');
            $this->node($act1980, $s2_1, 'PARAGRAPH', '(e)', null, 'that has in or upon it any added substance in contravention of the provisions of this Act or any regulation made thereunder; or');
            $this->node($act1980, $s2_1, 'PARAGRAPH', '(f)', null, 'in contravention of the provisions of this Act or any regulation made thereunder.');
            $this->node($act1980, $s2, 'SUBSECTION', '(2)', null, 'No person shall manufacture, prepare, preserve, package or store for sale any food under insanitary conditions.');
            $this->node($act1980, $s2, 'SUBSECTION', '(3)', null, 'No person shall import, sell or distribute any food manufactured, prepared, preserved, packaged or stored for sale under insanitary conditions.');

            $s3 = $this->section($act1980, $part1, '3', null, 'Labelling, packaging, advertising, &c.');
            $this->node($act1980, $s3, 'SUBSECTION', '(1)', null, 'No person shall label, package, treat, process, sell or advertise any food in a manner that is false, misleading, deceptive or likely to create an erroneous impression, regarding its character, value, quality, composition, merit or safety.');
            $this->node($act1980, $s3, 'SUBSECTION', '(2)', null, 'Any food that is not labelled or packaged as required by the regulations made under this Act or is labelled or packaged contrary to such regulations shall be deemed to be labelled or packaged contrary to subsection (1).');

            $this->section($act1980, $part1, '4', 'Where a standard is prescribed for any food, no person shall label, package, sell or advertise any food which does not conform to that standard in such a manner as is likely to be mistaken for the food for which the standard has been prescribed.', 'Where standard is prescribed.');
            $this->section($act1980, $part1, '5', 'No person shall offer for sale, expose for sale or sell for use as animal food or for other purposes any food which has been spoilt or rendered unfit for human consumption except with the permission of, and in accordance with the directions issued by the Chief Food Authority or such other person authorized by him in writing in that behalf.', 'Sale for purposes other than human con. sumption of food rendered unfit for human con- sumption.');

            $s6 = $this->section($act1980, $part1, '6', null, 'Warranty.');
            $this->node($act1980, $s6, 'SUBSECTION', '(1)', null, 'No manufacturer or a distributor of or a commission agent or a dealer in any food shall sell such food to any vendor unless he also gives that vendor a warranty in the prescribed form in respect of the nature substance and quality of that food.');
            $this->node($act1980, $s6, 'SUBSECTION', '(2)', null, 'A bill, cash memorandum or invoice, in respect of the sale of any food given by a manufacturer or distributor of or a commission agent or a dealer in any such food to the vendor of that food, shall be deemed to be a warranty under the preceding provisions of this section in respect of that food, if such bill, cash memorandum or invoice contains a description of the nature, substance and quality of that food.');
            $this->node($act1980, $s6, 'SUBSECTION', '(3)', null, 'No manufacturer or distributor of, or a commission agent or dealer in, any food shall under subsection (1) give a warranty which is false.');

            $s7 = $this->section($act1980, $part1, '7', null, 'Licensing.');
            $this->node($act1980, $s7, 'SUBSECTION', '(1)', null, 'No person shall manufacture, prepare, preserve, package, store or sell any food in any premises unless such premises has been licensed by the relevant Food Authority who shall be the licensing authority.');
            $this->node($act1980, $s7, 'SUBSECTION', '(2)', null, 'No person shall manufacture, prepare, store or sell or distribute any food unless he is the holder of a licence authorizing him to manufacture, prepare, store, sell or distribute any food otherwise than in accordance with the terms and conditions of such licence.');

            // PART II — ADMINISTRATION
            $part2 = $this->node($act1980, null, 'PART', 'II', 'ADMINISTRATION');

            $s8 = $this->section($act1980, $part2, '8', null, 'Food Advisory Committee.');
            $s8_1 = $this->node($act1980, $s8, 'SUBSECTION', '(1)', null, 'There shall be a Committee which shall be called the Food Advisory Committee consisting of—');
            $this->node($act1980, $s8_1, 'PARAGRAPH', '(a)', null, 'the Director of Health Services who shall be the Chairman of the Committee;');
            $this->node($act1980, $s8_1, 'PARAGRAPH', '(b)', null, 'the Assistant Director of Health Services in charge of Food Control Administration who shall be the Secretary of the Committee;');
            $this->node($act1980, $s8_1, 'PARAGRAPH', '(c)', null, 'the Government Analyst or any officer nominated by him;');
            $this->node($act1980, $s8_1, 'PARAGRAPH', '(d)', null, 'the City Analyst of the Colombo Municipality;');
            $this->node($act1980, $s8_1, 'PARAGRAPH', '(e)', null, 'the Principal Collector of Customs or any officer nominated by him;');
            $this->node($act1980, $s8_1, 'PARAGRAPH', '(f)', null, 'the Chief Medical Officer of Health of the Colombo Municipality;');
            $this->node($act1980, $s8_1, 'PARAGRAPH', '(g)', null, '(i) a representative of the Ministry of the Minister in charge of the subject of Food nominated by that Minister... (ii) Ministry of Trade... (iii) Local Government...');
            $this->node($act1980, $s8_1, 'PARAGRAPH', '(h)', null, 'a representative of the Bureau of Ceylon Standards nominated by the Minister in charge of the subject of Industries;');
            $this->node($act1980, $s8_1, 'PARAGRAPH', '(i)', null, 'a nutritionist from the Medical Research Institute nominated by the Minister;');
            $this->node($act1980, $s8_1, 'PARAGRAPH', '(j)', null, 'a food technologist nominated by the Minister;');
            $this->node($act1980, $s8_1, 'PARAGRAPH', '(k)', null, 'two experts in food science nominated by the Minister;');
            $this->node($act1980, $s8_1, 'PARAGRAPH', '(l)', null, 'two members nominated by the Minister one of whom shall represent commercial interests and the other industrial interests;');
            $this->node($act1980, $s8_1, 'PARAGRAPH', '(m)', null, 'two members nominated by the Minister to represent the interests of the consumer; and');
            $this->node($act1980, $s8_1, 'PARAGRAPH', '(n)', null, 'the Chief Food and Drugs Inspector.');
            $this->node($act1980, $s8, 'SUBSECTION', '(2)', null, 'Every member of the Committee nominated by the Minister under paragraphs (j), (k), (l) and (m) of subsection (1) shall... hold office for a period of three years...');

            // Track standard base 1980 configurations for structural inheritance mapping
            $this->sections['8_1980'] = $s8->node_id;

            // Administrative nodes 9 through 34
            $this->section($act1980, $part2, '11', 'The Director of Health Services shall be the Chief Food Authority for the purposes of this Act.', 'Chief Food Authority.');
            $this->section($act1980, $part2, '29', 'All fines paid or recovered in respect of an offence under this Act or any regulations made thereunder in a prosecution instituted by an Authorized Officer or a Food Authority which is a local authority shall be credited to the fund of that local authority.', 'Certain fines to be paid into the funds of local authorities.');

            // ─────────────────────────────────────────────────────────────────
            // 5. SEED AMENDMENT SPECIFIC NODES (1991) & APPLY HISTORICAL CHANGES
            // ─────────────────────────────────────────────────────────────────
            $part1_1991 = $this->node($act1991, null, 'PART', 'I', 'PROHIBITION IN RESPECT OF FOOD AMENDMENTS');

            // Section 2 Amending block
            $s2_1991 = $this->section($act1991, $part1_1991, '2', null, 'manufacture, importation, sale, exposure for sale, storage and distribution of food.');
            $this->node($act1991, $s2_1991, 'SUBSECTION', '(1)', null, 'Amended to insert "expose for sale, store" into base tracking framework.');

            // Re-map nodes with correct version identifiers
            $s8_2011 = $this->node($act2011, null, 'SECTION', '8', 'Food Advisory Committee Replacement.', 'There shall be established a Committee which shall be called the Food Advisory Committee comprising of ex-officio and nominated members.');
            $this->sections['8_2011'] = $s8_2011->node_id;

            // ─────────────────────────────────────────────────────────────────
            // 6. MAP INTER-ACT RELATIONSHIPS VIA CROSS-REFERENCES
            // ─────────────────────────────────────────────────────────────────
            $this->seedCrossReferences();

            // ─────────────────────────────────────────────────────────────────
            // 7. CASCADE HAS_CHILDREN PARAMETERS
            // ─────────────────────────────────────────────────────────────────
            foreach ($allActIds as $aid) {
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
                ", [$aid, $aid]);
            }
        });
    }

    private function section(Act $act, ?LegalNode $parent, string $number, ?string $text, ?string $marginalNote = null): LegalNode
    {
        return $this->node($act, $parent, 'SECTION', $number, $marginalNote, $text);
    }

    private function node(Act $act, ?LegalNode $parent, string $type, ?string $number, ?string $marginalNote = null, ?string $text = null): LegalNode
    {
        $parentId = $parent?->node_id;
        $level    = $parent ? ($parent->level + 1) : 1;
        $key      = $parentId ?? 0;

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
            'status'         => 'active',
        ]);

        if ($type === 'SECTION' && $number !== null) {
            $this->sections[$number . '_' . $act->act_year] = $node->node_id;
        }

        return $node;
    }

    private function seedCrossReferences(): void
    {
        // Setup semantic relations between historical modification tracking elements
        if (isset($this->sections['8_1980']) && isset($this->sections['8_2011'])) {
            CrossReference::firstOrCreate(
                [
                    'source_node_id' => $this->sections['8_2011'],
                    'target_node_id' => $this->sections['8_1980'],
                ],
                [
                    'reference_type' => 'as_amended_by',
                    'reference_text' => 'Section 8 of Act No. 26 of 1980 is repealed and replaced by Act No. 29 of 2011.',
                ]
            );
        }
    }
}
