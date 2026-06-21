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
 * Consumer Affairs Authority Act, No. 9 of 2003.
 *
 * Body text is reproduced VERBATIM from the certified Act
 * (Supplement to Part II of the Gazette of 21 March 2003, certified 17 March 2003).
 * Original spelling/typos in the enacted text are preserved on purpose.
 *
 * The descriptive captions printed in the page margin (e.g. "Objects of the
 * Authority.", "Delegation of powers to public officers.") are stored in the
 * `marginal_note` column. They are editorial side-notes, not enacted text, and
 * sit on the SECTION / SCHEDULE-paragraph / PREAMBLE node they describe.
 *
 * Requires new columns on `legal_nodes`:
 * ALTER TABLE `legal_nodes`
 * ADD COLUMN `marginal_note` VARCHAR(500) NULL AFTER `heading`,
 * ADD COLUMN `level` TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER `parent_id`;
 * and `marginal_note`, `level` added to LegalNode::$fillable.
 */
class ConsumerAffairsActSeeder extends Seeder
{
    /** @var array<string,int> section-number => node_id (for cross-references) */
    private array $sections = [];

    /** @var array<int,int> parent_node_id => running sort counter */
    private array $sortCounters = [];

    public function run(): void
    {
        DB::transaction(function () {
            $gazette = Gazette::firstOrCreate(
                ['gazette_no' => 'Supplement to Part II'],
                [
                    'gazette_date' => '2003-03-21',
                    'title'        => 'Gazette of the Democratic Socialist Republic of Sri Lanka, March 21, 2003',
                    'status'       => 'active',
                ]
            );

            $act = Act::firstOrCreate(
                ['act_no' => 9, 'act_year' => 2003],
                [
                    'gazette_id'        => $gazette->gazette_id,
                    'title'             => 'Consumer Affairs Authority Act',
                    'short_title'       => 'Consumer Affairs Authority Act, No. 9 of 2003',
                    'commencement_date' => '2003-03-17',
                    'status'            => 'active',
                    'current_version'   => true,
                ]
            );

            // Wipe any previous seed of this Act (cross-refs first, then nodes).
            $nodeIds = LegalNode::withTrashed()->where('act_id', $act->act_id)->pluck('node_id');
            if ($nodeIds->isNotEmpty()) {
                CrossReference::whereIn('source_node_id', $nodeIds)
                    ->orWhereIn('target_node_id', $nodeIds)
                    ->delete();
                LegalNode::withTrashed()->where('act_id', $act->act_id)->forceDelete();
            }

            $this->sections     = [];
            $this->sortCounters = [];

            $this->seedPreamble($act);
            $this->seedPartI($act);
            $this->seedPartII($act);
            $this->seedPartIII($act);
            $this->seedPartIV($act);
            $this->seedPartV($act);
            $this->seedPartVI($act);
            $this->seedPartVII($act);
            $this->seedSchedule($act);

            $this->seedCrossReferences();

            // Mark every node that has at least one child as has_children = true.
            // Wrapped in a subquery alias to work around MySQL's restriction on
            // updating a table that is also referenced in the WHERE subquery.
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

    // ─────────────────────────────────────────────────────────────────────────
    // PREAMBLE
    // ─────────────────────────────────────────────────────────────────────────
    private function seedPreamble(Act $act): void
    {
        $preamble = $this->node(
            $act,
            null,
            'PREAMBLE',
            null,
            null,
            'AN ACT TO PROVIDE FOR THE ESTABLISHMENT OF THE CONSUMER AFFAIRS AUTHORITY ; '
                . 'FOR THE PROMOTION OF EFFECTIVE COMPETITION AND THE PROTECTION OF CONSUMERS ; '
                . 'FOR THE REGULATION OF INTERNAL TRADE ; FOR THE ESTABLISHMENT OF A CONSUMER AFFAIRS COUNCIL ; '
                . 'FOR THE REPEAL OF THE CONSUMER PROTECTION ACT, NO. 1 OF 1979, THE FAIR TRADING COMMISSION '
                . 'ACT, NO. 1 OF 1987 AND THE CONTROL OF PRICES ACT (CHAPTER 173) ; AND FOR ALL MATTERS '
                . 'CONNECTED THEREWITH OR INCIDENTAL THERETO'
                . "\n\n"
                . 'WHEREAS it is the policy of the Government of Sri Lanka to provide for the better protection '
                . 'of consumers through the regulation of trade and the prices of goods and services and to '
                . 'protect traders and manufacturers against unfair trade practices and restrictive trade practices :'
                . "\n\n"
                . 'AND WHEREAS the Government of Sri Lanka is also desirous of promoting competitive pricing '
                . 'wherever possible and ensure healthy competition among traders and manufacturers of goods and services :'
                . "\n\n"
                . 'NOW THEREFORE be it enacted by the Parliament of the Democratic Socialist Republic of Sri Lanka as follows :—',
            'Preamble.'
        );
        $this->sections['PREAMBLE'] = $preamble->node_id;

        // 1.
        $this->section(
            $act,
            $preamble,
            '1',
            'This Act may be cited as the Consumer Affairs Authority Act, No. 9 of 2003.',
            'Short title.'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PART I — Establishment of the Consumer Affairs Authority (ss. 1–8)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedPartI(Act $act): void
    {
        $part = $this->node($act, null, 'PART', 'I', 'ESTABLISHMENT OF THE CONSUMER AFFAIRS AUTHORITY');

        // 2.
        $s2 = $this->section($act, $part, '2', null, 'Establishment of the Consumer Affairs Authority.');
        $this->node(
            $act,
            $s2,
            'SUBSECTION',
            '(1)',
            null,
            'An authority called the Consumer Affairs Authority (hereinafter referred to as the "Authority") '
                . 'is hereby established which shall consist of the persons who are for the time being members of '
                . 'the Authority under section 3.'
        );
        $this->node(
            $act,
            $s2,
            'SUBSECTION',
            '(2)',
            null,
            'The Authority shall, by the name assigned to it by subsection (1), be a body corporate and shall '
                . 'have perpetual succession and a common seal and may sue and be sued in such name.'
        );

        // 3.
        $s3 = $this->section($act, $part, '3', null, 'Constitution of the Authority.');
        $this->node(
            $act,
            $s3,
            'SUBSECTION',
            '(1)',
            null,
            'The Authority shall consist of a Chairman and not less than ten other members who shall be '
                . 'appointed by the Minister from among persons who possess recognized qualifications, have had '
                . 'wide experience and have distinguished themselves in the field of industry, law, economics, '
                . 'commerce, administration, accountancy, science or health.'
        );
        $this->node(
            $act,
            $s3,
            'SUBSECTION',
            '(2)',
            null,
            'The Chairman and three of the members, selected by the Minister from among the members appointed '
                . 'under subsection (1), shall be full time members (hereinafter referred to as "full-time members '
                . 'of the Authority").'
        );
        $this->node(
            $act,
            $s3,
            'SUBSECTION',
            '(3)',
            null,
            'The members of the Authority other than the Chairman and the three full time members of the '
                . 'Authority shall be paid such remuneration as may be determined by the Minister in consultation '
                . 'with the Minister in charge of the subject of Finance.'
        );
        $this->node(
            $act,
            $s3,
            'SUBSECTION',
            '(4)',
            null,
            'The provisions of the Schedule to this Act, shall have effect in relation to the term of office '
                . 'of the members of the Authority, the meetings and seal of the Authority.'
        );

        // 4.
        $this->section(
            $act,
            $part,
            '4',
            'The Chairman and the full time members of the Authority shall each hold office for a period of '
                . 'three years from the date of their respective appointments, and shall be paid for their services '
                . 'such remuneration as may be determined by the Minister in consultation with the Minister in '
                . 'charge of the subject of Finance.',
            'Chairman and full time members.'
        );

        // 5.
        $this->section(
            $act,
            $part,
            '5',
            'The Director-General of the Authority appointed under section 52, shall act as the Secretary to the Authority.',
            'Director-General to act as the Secretary to the Authority.'
        );

        // 6.
        $this->section(
            $act,
            $part,
            '6',
            'The Authority may for the purpose of discharging its functions under this Act, delegate to any '
                . 'public officer by name or office such functions vested in, or imposed upon, or assigned to the '
                . 'Authority by or under this Act, on such terms and conditions as may be agreed upon between such '
                . 'officer and the Authority.',
            'Delegation of powers to public officers.'
        );

        // 7.
        $s7 = $this->section($act, $part, '7', 'The objects of the Authority shall be—', 'Objects of the Authority.');
        $this->node(
            $act,
            $s7,
            'PARAGRAPH',
            '(a)',
            null,
            'to protect consumers against the marketing of goods or the provision of services which are '
                . 'hazardous to life and property of consumers ;'
        );
        $this->node(
            $act,
            $s7,
            'PARAGRAPH',
            '(b)',
            null,
            'to protect consumers against unfair trade practices and guarantee that consumers interest shall '
                . 'be given due consideration ;'
        );
        $this->node(
            $act,
            $s7,
            'PARAGRAPH',
            '(c)',
            null,
            'to ensure that wherever possible consumers have adequate access to goods and services at '
                . 'competitive prices ; and'
        );
        $this->node(
            $act,
            $s7,
            'PARAGRAPH',
            '(d)',
            null,
            'to seek redress against unfair trade practices, restrictive trade practices or any other forms '
                . 'of exploitation of consumers by traders.'
        );

        // 8.
        $s8 = $this->section($act, $part, '8', 'The functions of the Authority shall be to—', 'Functions of the Authority.');
        $s8a = $this->node($act, $s8, 'PARAGRAPH', '(a)', null, 'control or eliminate—');
        $this->node($act, $s8a, 'SUBPARAGRAPH', '(i)', null, 'restrictive trade agreements among enterprises ;');
        $this->node($act, $s8a, 'SUBPARAGRAPH', '(ii)', null, 'arrangements amongst enterprises with regard to prices ;');
        $this->node(
            $act,
            $s8a,
            'SUBPARAGRAPH',
            '(iii)',
            null,
            'abuse of a dominant position with regard to domestic trade or economic development within the '
                . 'market or in a substantial part of the market ; or'
        );
        $this->node(
            $act,
            $s8a,
            'SUBPARAGRAPH',
            '(iv)',
            null,
            'any restraint of competition adversely affecting domestic or international trade or economic development ;'
        );
        $this->node(
            $act,
            $s8,
            'PARAGRAPH',
            '(b)',
            null,
            'investigate or inquire into anti-competitive practices and abuse of a dominant position ;'
        );
        $this->node(
            $act,
            $s8,
            'PARAGRAPH',
            '(c)',
            null,
            'maintain and promote effective competition between persons supplying goods and services ;'
        );
        $this->node(
            $act,
            $s8,
            'PARAGRAPH',
            '(d)',
            null,
            'promote and protect the rights and interests of consumers, purchasers and other users of goods '
                . 'and services in respect of the price, availability and quality of such goods and services and '
                . 'the variety supplied ;'
        );
        $this->node(
            $act,
            $s8,
            'PARAGRAPH',
            '(e)',
            null,
            'to keep consumers informed about the quality, quantity, potency, purity, standards and price of '
                . 'goods and services made available for purchase ;'
        );
        $this->node(
            $act,
            $s8,
            'PARAGRAPH',
            '(f)',
            null,
            'carry out investigations and inquiries in relation to any matter specified in this Act ;'
        );
        $this->node(
            $act,
            $s8,
            'PARAGRAPH',
            '(g)',
            null,
            'promote competitive prices in markets where competition is less than effective ;'
        );
        $this->node(
            $act,
            $s8,
            'PARAGRAPH',
            '(h)',
            null,
            'undertake studies, publish reports and provide information to the public relating to market '
                . 'conditions and consumer affairs ;'
        );
        $this->node($act, $s8, 'PARAGRAPH', '(i)', null, 'undertake public sector and private sector efficiency studies ;');
        $this->node(
            $act,
            $s8,
            'PARAGRAPH',
            '(j)',
            null,
            'promote consumer education with regard to good health, safety and security of consumers ;'
        );
        $this->node(
            $act,
            $s8,
            'PARAGRAPH',
            '(k)',
            null,
            'promote the exchange of information relating to market conditions and consumer affairs with other institutions ;'
        );
        $this->node(
            $act,
            $s8,
            'PARAGRAPH',
            '(l)',
            null,
            'promote, assist and encourage the establishment of consumer organizations ;'
        );
        $this->node(
            $act,
            $s8,
            'PARAGRAPH',
            '(m)',
            null,
            'charge such fees in respect of any services rendered by the Authority ;'
        );
        $this->node(
            $act,
            $s8,
            'PARAGRAPH',
            '(n)',
            null,
            'appoint any such committee or committees as may be necessary to facilitate the discharge of the '
                . 'functions of the Authority ; and'
        );
        $this->node(
            $act,
            $s8,
            'PARAGRAPH',
            '(o)',
            null,
            'do all such other acts as may be necessary for attainment of the objects of the Authority and '
                . 'for the effective discharge of the functions of such Authority.'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PART II — Regulation of Trade (ss. 9–33)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedPartII(Act $act): void
    {
        $part = $this->node($act, null, 'PART', 'II', 'REGULATION OF TRADE');

        // 9.
        $s9 = $this->section(
            $act,
            $part,
            '9',
            'The Authority may—',
            'Authority may undertake studies on the distribution of goods and services.'
        );
        $this->node(
            $act,
            $s9,
            'PARAGRAPH',
            '(a)',
            null,
            'undertake such studies in respect of the sale or supply of any class of goods and services as '
                . 'would ensure the availability to the consumer of such goods and services of satisfactory quality '
                . 'at reasonable prices and in adequate quantities ;'
        );
        $this->node(
            $act,
            $s9,
            'PARAGRAPH',
            '(b)',
            null,
            'promote, assist and encourage the State or other organisations including organisations of '
                . 'consumers, for the purposes described in paragraph (a) ; and'
        );
        $this->node(
            $act,
            $s9,
            'PARAGRAPH',
            '(c)',
            null,
            'assist and encourage associations of traders to enter into agreements with the Authority for the '
                . 'purposes described in section 14.'
        );

        // 10.
        $s10 = $this->section(
            $act,
            $part,
            '10',
            null,
            'Authority to issue directions to manufacturers or traders in respect of price marking, labelling and packeting of goods.'
        );
        $s10_1 = $this->node($act, $s10, 'SUBSECTION', '(1)', null, 'The Authority may, for the protection of the consumer—');
        $this->node(
            $act,
            $s10_1,
            'PARAGRAPH',
            '(a)',
            null,
            'issue general directions to manufacturers or traders in respect of labelling, price marking, '
                . 'packeting, sale or manufacture of any goods ; and'
        );
        $s10_1b = $this->node(
            $act,
            $s10_1,
            'PARAGRAPH',
            '(b)',
            null,
            'issue special directions to any class of manufacturers or traders, specifying—'
        );
        $this->node(
            $act,
            $s10_1b,
            'SUBPARAGRAPH',
            '(i)',
            null,
            'the times during which and the places at which, such goods may be sold ; and'
        );
        $this->node(
            $act,
            $s10_1b,
            'SUBPARAGRAPH',
            '(ii)',
            null,
            'any other conditions as to the manufacturing, importing, marketing, storing, selling and stocking, of any goods.'
        );
        $this->node(
            $act,
            $s10,
            'SUBSECTION',
            '(2)',
            null,
            'Every direction issued by the Authority under subsection (1) shall be published in the Gazette '
                . 'and in at least one Sinhala, one Tamil and one English newspaper.'
        );
        $this->node(
            $act,
            $s10,
            'SUBSECTION',
            '(3)',
            null,
            'Any manufacturer or trader who fails to comply with any direction issued under subsection (1) '
                . 'shall be guilty of an offence under this Act.'
        );
        $this->node(
            $act,
            $s10,
            'SUBSECTION',
            '(4)',
            null,
            'Any person who removes, alters, obliterates, erases or defaces any label, description or price '
                . 'mark on any goods in respect of which a direction under subsection (1) has been issued, or sells '
                . 'or offers for sale any such goods from or on which the label, description or price mark has been '
                . 'removed, altered, obliterated, erased or defaced, shall be guilty of an offence under this Act.'
        );

        // 11.
        $this->section(
            $act,
            $part,
            '11',
            'Any person who sells or offers to sell any goods above the price marked on the goods in accordance '
                . 'with a direction issued under section 10, shall be guilty of an offence under this Act.',
            'Selling or offering to sell above the marked price.'
        );

        // 12.
        $s12 = $this->section(
            $act,
            $part,
            '12',
            null,
            'Determining standards and specifications relating to goods and supply of services.'
        );
        $this->node(
            $act,
            $s12,
            'SUBSECTION',
            '(1)',
            null,
            'The Authority may for the purpose of protecting the consumer and ensuring the quality of goods '
                . 'sold or services provided, by Notification published in the Gazette, from time to time, determine '
                . 'such standards and specifications relating to the production, manufacture, supply, storage, '
                . 'transportation and sale of any goods, and to the supply of any services.'
        );
        $this->node(
            $act,
            $s12,
            'SUBSECTION',
            '(2)',
            null,
            'The Authority may by Notification published in the Gazette adopt such standards and specifications '
                . 'prescribed by the Sri Lanka Standards Institution established by the Sri Lanka Standards Institution '
                . 'Act, No. 6 of 1984, relating to the production, manufacture, supply, storage, transportation and '
                . 'sale of any goods, and to the supply of any services, as standards and specifications, to be '
                . 'determined under subsection (1).'
        );

        // 13.
        $s13 = $this->section($act, $part, '13', null, 'Authority to inquire into complaints.');
        $s13_1 = $this->node($act, $s13, 'SUBSECTION', '(1)', null, 'The Authority may inquire into complaints regarding—');
        $this->node(
            $act,
            $s13_1,
            'PARAGRAPH',
            '(a)',
            null,
            'the production, manufacture, supply, storage, transportation or sale of any goods and to the '
                . 'supply of any services which does not conform to the standards and specifications determined '
                . 'under section 12 ; and'
        );
        $this->node(
            $act,
            $s13_1,
            'PARAGRAPH',
            '(b)',
            null,
            'the manufacture or sale of any goods which does not conform to the warranty or guarantee given by '
                . 'implication or otherwise, by the manufacturer or trader.'
        );
        $this->node(
            $act,
            $s13,
            'SUBSECTION',
            '(2)',
            null,
            'A complaint under subsection (1) which relates to the sale of any goods or to the provision of '
                . 'any service shall be made to the Authority in writing within three months of the sale of such '
                . 'goods or the provisions of such service, as the case may be.'
        );
        $this->node(
            $act,
            $s13,
            'SUBSECTION',
            '(3)',
            null,
            'At any inquiry held into a complaint under subsection (1), the Authority shall give the '
                . 'manufacturer or trader against whom such complaint is made an opportunity of being heard either '
                . 'in person or by an agent nominated in that behalf.'
        );
        $this->node(
            $act,
            $s13,
            'SUBSECTION',
            '(4)',
            null,
            'Where after an inquiry into a complaint, the Authority is of opinion that a manufacture or sale '
                . 'of any goods or the provision of any services has been made which does not conform to the '
                . 'standards or specifications determined or deemed to be determined by the Authority, or that a '
                . 'manufacture or sale has been made of any goods not conforming to any warranty or guarantee given '
                . 'by implication or otherwise by the manufacturer or trader, it shall order the manufacturer or '
                . 'trader to pay compensation to the aggrieved party or to replace such goods or to refund the '
                . 'amount paid for such goods or the provision of such service, as the case may be.'
        );
        $this->node(
            $act,
            $s13,
            'SUBSECTION',
            '(5)',
            null,
            'An order under subsection (4) shall be made in writing and be communicated to such manufacturer '
                . 'or trader by registered post.'
        );
        $this->node(
            $act,
            $s13,
            'SUBSECTION',
            '(6)',
            null,
            'Where any manufacturer or trader fails or refuses to comply with an order made under subsection '
                . '(4) of this section, such manufacturer or trader shall be guilty of an offence under this Act, '
                . 'and the sum of money due on the order as compensation or refund may, on application being made '
                . 'in that behalf by the Authority to the Magistrate\'s Court having jurisdiction over the place of '
                . 'business or residence of such manufacturer or trader as the case may be, be recovered in like '
                . 'manner as a fine imposed by such court, notwithstanding that such sum may exceed the amount of a '
                . 'fine which that court may, in the exercise of its ordinary jurisdiction, impose.'
        );

        // 14.
        $s14 = $this->section($act, $part, '14', null, 'Agreement to provide for maximum price &c. of goods.');
        $s14_1 = $this->node(
            $act,
            $s14,
            'SUBSECTION',
            '(1)',
            null,
            'The Authority may enter into such written agreements as it may deem necessary, with any manufacturer '
                . 'or trader or with any association of manufacturers or traders to provide for—'
        );
        $this->node($act, $s14_1, 'PARAGRAPH', '(a)', null, 'the maximum price above which any goods shall not be sold;');
        $this->node(
            $act,
            $s14_1,
            'PARAGRAPH',
            '(b)',
            null,
            'the standards and specifications of any goods manufactured, sold or offered for sale ;'
        );
        $this->node(
            $act,
            $s14_1,
            'PARAGRAPH',
            '(c)',
            null,
            'any other conditions as to the manufacture, import, supply, storage, distribution, transportation, '
                . 'marketing, labelling or sale of any goods.'
        );
        $this->node(
            $act,
            $s14,
            'SUBSECTION',
            '(2)',
            null,
            'Every written agreement entered into under subsection (1) between the Authority and any manufacturer '
                . 'or trader or with any association of manufacturers or traders, shall be binding on every authorised '
                . 'distributor of such manufacturer or trader and every member of such association, as though he was '
                . 'a party to such agreement and whether or not he was a member at the time of entering into the agreement.'
        );
        $this->node(
            $act,
            $s14,
            'SUBSECTION',
            '(3)',
            null,
            'Every manufacturer or trader or any authorised distributor of such manufacturer or trader or any '
                . 'association of manufacturers or traders or any member thereof, who contravenes any provision of '
                . 'any agreement entered into with the Authority under subsection (1), shall be guilty of an offence under this Act.'
        );
        $this->node(
            $act,
            $s14,
            'SUBSECTION',
            '(4)',
            null,
            'Every agreement entered into with the Authority under subsection (1), shall be registered with the '
                . 'Authority and shall contain a schedule giving the name and description of each authorised '
                . 'distributor of such manufacturer or trader or members of such association, as the case may be.'
        );
        $this->node(
            $act,
            $s14,
            'SUBSECTION',
            '(5)',
            null,
            'Every agreement entered into with the Authority under subsection (1), shall come into force from '
                . 'the date of such agreement, unless such agreement provides that the agreement shall come into '
                . 'force on any subsequent date.'
        );
        $this->node(
            $act,
            $s14,
            'SUBSECTION',
            '(6)',
            null,
            'Where the Authority thinks fit, it may cause any agreement made under this section to be published '
                . 'in the Gazette. Where an agreement is published in the Gazette, the production of a copy of the '
                . 'Gazette in which such agreement is published shall, until the contrary is proved, be proof of the '
                . 'contents of such agreement.'
        );
        $this->node(
            $act,
            $s14,
            'SUBSECTION',
            '(7)',
            null,
            'For the purpose of any prosecution under this section, a certificate given by the Authority that '
                . 'any person is an authorised distributor of any manufacturer or trader or is a member of any '
                . 'association of traders or manufacturers shall be admissible and be prima facie proof in a court '
                . 'of law that such person is an authorised distributor of such manufacturer or trader or a member '
                . 'of such association.'
        );

        // 15.
        $s15 = $this->section($act, $part, '15', null, 'Refusal to sell goods to be an offence.');
        $this->node(
            $act,
            $s15,
            'SUBSECTION',
            '(1)',
            null,
            'No trader who has in his possession or custody or under his control any goods for purpose of trade '
                . 'within Sri Lanka, shall refuse to sell such goods.'
        );
        $s15_2 = $this->node(
            $act,
            $s15,
            'SUBSECTION',
            '(2)',
            null,
            'In any prosecution of any trader for the contravention of the provisions of subsection (1), it '
                . 'shall be a sufficient defence for the accused to prove that —'
        );
        $this->node(
            $act,
            $s15_2,
            'PARAGRAPH',
            '(a)',
            null,
            'on the occasion in question, he supplied a reasonable quantity of the goods, or had not a '
                . 'sufficient quantity in his possession to supply the quantity;'
        );
        $this->node(
            $act,
            $s15_2,
            'PARAGRAPH',
            '(b)',
            null,
            'he carried on business in the goods as a wholesale trader only, and that the sale of the quantity '
                . 'demanded by the buyer would have been contrary to the normal practice of a wholesale business ; or'
        );
        $this->node(
            $act,
            $s15_2,
            'PARAGRAPH',
            '(c)',
            null,
            'the sale of the goods on that occasion in question would have been contrary to any provisions of '
                . 'any written law or any general or special direction issued to him by the Authority under section 10.'
        );

        // 16.
        $s16 = $this->section(
            $act,
            $part,
            '16',
            'No trader who has in his possession or custody or under his '
                . 'control any goods for purpose of trade within Sri Lanka, shall —',
            'Denial of possession of any goods for purposes of trade or the sale of such goods subject to any condition to be an offence.'
        );
        $this->node($act, $s16, 'PARAGRAPH', '(a)', null, 'deny the possession of such goods ; or');
        $this->node(
            $act,
            $s16,
            'PARAGRAPH',
            '(b)',
            null,
            'offer such goods for sale subject to a condition requiring the purchase of any other goods or the '
                . 'making of any payment in respect of any service or to any other condition, other than the '
                . 'condition that the buyer shall pay the price of such goods forthwith.'
        );

        // 17.
        $s17 = $this->section($act, $part, '17', null, 'Hoarding of goods by any trader or person to be an offence.');
        $this->node(
            $act,
            $s17,
            'SUBSECTION',
            '(1)',
            null,
            'No trader shall conceal in his place of business or in any other place, any goods in such quantity '
                . 'as is in excess of the normal trading requirements of such trader.'
        );
        $s17_2 = $this->node(
            $act,
            $s17,
            'SUBSECTION',
            '(2)',
            null,
            'No trader shall have in his possession or custody or under his control in his place of business or '
                . 'in any other place, any goods in such quantity in excess of —'
        );
        $this->node(
            $act,
            $s17_2,
            'PARAGRAPH',
            '(a)',
            null,
            'the quantity required for his personal consumption and of the members of his household ; or'
        );
        $this->node($act, $s17_2, 'PARAGRAPH', '(b)', null, 'the requirements of the normal trading activities of such trader.');
        $this->node(
            $act,
            $s17,
            'SUBSECTION',
            '(3)',
            null,
            'No person other than a trader shall have in his possession or custody or under his control any '
                . 'goods in such quantity in excess of the normal personal requirements of such person.'
        );

        // 18.
        $s18 = $this->section(
            $act,
            $part,
            '18',
            null,
            'Increase of retail or wholesale price of certain goods and certain services.'
        );
        $this->node(
            $act,
            $s18,
            'SUBSECTION',
            '(1)',
            null,
            'Where the Minister is of opinion that any goods or any service is essential to the life of the '
                . 'community or part thereof, the Minister in consultation with the Authority may by Order published '
                . 'in the Gazette prescribe such goods or such service as specified goods or specified service as '
                . 'the case may be.'
        );
        $this->node(
            $act,
            $s18,
            'SUBSECTION',
            '(2)',
            null,
            'No manufacturer or trader shall increase the retail or wholesale price of any goods or any service '
                . 'specified under subsection (1), except with the prior written approval of the Authority.'
        );
        $s18_3 = $this->node(
            $act,
            $s18,
            'SUBSECTION',
            '(3)',
            null,
            'A manufacturer or trader who seeks to obtain the approval of the Authority under subsection (2), '
                . 'shall make an application in that behalf to the Authority, and the Authority shall, after holding '
                . 'such inquiry as it may consider appropriate :—'
        );
        $this->node(
            $act,
            $s18_3,
            'PARAGRAPH',
            '(a)',
            null,
            'approve such increase where it is satisfied that the increase is reasonable ; or'
        );
        $this->node(
            $act,
            $s18_3,
            'PARAGRAPH',
            '(b)',
            null,
            'approve any other increase as the Authority may consider reasonable, and inform the manufacturer '
                . 'or trader of its decision within thirty days of the receipt of such application.'
        );
        $this->node(
            $act,
            $s18,
            'SUBSECTION',
            '(4)',
            null,
            'Where the Authority fails to give a decision within thirty days of the receipt of an application '
                . 'as required under subsection (3), the manufacturer or trader who made the application shall be '
                . 'entitled to, notwithstanding the provisions of subsection (1), increase the price : '
                . 'Provided however, where the delay in giving its decision within the stipulated period was due to '
                . 'the failure of the manufacturer or trader to give any assistance required by the Authority in '
                . 'carrying out its inquiry into the application, the Authority shall have the power to make an '
                . 'interim order preventing the said manufacturer or trader from increasing the price, until the '
                . 'Authority makes its decision on the application.'
        );

        // 19.
        $s19 = $this->section($act, $part, '19', null, 'Director-General to refer matters to the Council.');
        $s19_1 = $this->node(
            $act,
            $s19,
            'SUBSECTION',
            '(1)',
            null,
            'Where it appears to the Director-General that any goods are being sold or any services are being '
                . 'provided by a manufacturer or trader at an excessive price or any market manipulation or other '
                . 'market imperfection exists in respect of such goods or services provided by such manufacturer or '
                . 'trader, the Director-General may, in consultation with the Authority as he may deem necessary and '
                . 'having regard to whether —'
        );
        $this->node(
            $act,
            $s19_1,
            'PARAGRAPH',
            '(a)',
            null,
            'the sale of such goods or the provision of such services is of general economic importance ; or'
        );
        $this->node(
            $act,
            $s19_1,
            'PARAGRAPH',
            '(b)',
            null,
            'any category of consumers are significantly affected by such excessive price,'
        );
        $this->node($act, $s19_1, 'PARAGRAPH', null, null, 'refer such matter to the Council for investigation and report.');
        $this->node(
            $act,
            $s19,
            'SUBSECTION',
            '(2)',
            null,
            'The Council shall conclude its investigation and submit its report to the Director-General within '
                . 'two months of such reference being made to it by the Director-General.'
        );

        // 20.
        $s20 = $this->section($act, $part, '20', null, 'Investigation into a reference made under section 19.');
        $this->node(
            $act,
            $s20,
            'SUBSECTION',
            '(1)',
            null,
            'Where any reference is made to the Council by the Director-General under section 19, the Council '
                . 'shall cause such reference to be brought to the notice of such persons, who in the opinion of the '
                . 'Council would have an interest in the proposed investigation to be carried out by it on such '
                . 'reference, and shall further give such persons adequate notice of the date on which the '
                . 'investigation is scheduled to commence.'
        );
        $this->node(
            $act,
            $s20,
            'SUBSECTION',
            '(2)',
            null,
            'For the purpose of carrying out an investigation, the Council may by notice in writing require any '
                . 'person or any one authorized by such person, to produce such documents as may be specified in the '
                . 'notice, and at such time and place as mentioned therein, which are under the custody or control '
                . 'of such person and which relates to the matter under investigation.'
        );
        $this->node(
            $act,
            $s20,
            'SUBSECTION',
            '(3)',
            null,
            'At the conclusion of an investigation, the Council shall, subject to the provisions of section 21, '
                . 'cause its decision with its reasons to be published in such manner as it shall consider '
                . 'appropriate, and submit a report on such investigation to the Director-General as required under '
                . 'subsection (2) of section 19.'
        );
        $this->node(
            $act,
            $s20,
            'SUBSECTION',
            '(4)',
            null,
            'Where the Council concludes that the goods are being sold or the services are being provided at an '
                . 'excessive price, it shall recommend to the Authority in writing the maximum price above which '
                . 'such goods should not be sold or such services should not be provided or the price structure in '
                . 'accordance with which such maximum price shall be fixed.'
        );
        $this->node(
            $act,
            $s20,
            'SUBSECTION',
            '(5)',
            null,
            'On receipt of the recommendations of the Council under subsection (4), the Authority shall, by '
                . 'order published in the Gazette, fix the maximum price above which the goods shall not be sold or '
                . 'the services shall not be provided. Every order made under this subsection shall come into '
                . 'operation on the date of publication of such Order in the Gazette.'
        );
        $this->node(
            $act,
            $s20,
            'SUBSECTION',
            '(6)',
            null,
            'Nothing in this section shall be construed as precluding the Council from proceeding with a '
                . 'subsequent investigation which relates wholly or partly to the same matter which was the subject '
                . 'matter of a previous investigation carried out under this section.'
        );
        $this->node(
            $act,
            $s20,
            'SUBSECTION',
            '(7)',
            null,
            'Any person aggrived by an order made by the Authority under subsection (5) may appeal against such '
                . 'decision to the Court of Appeal.'
        );
        $this->node(
            $act,
            $s20,
            'SUBSECTION',
            '(8)',
            null,
            'Any document produced in compliance with a notice issued under subsection (2) shall be considered '
                . 'confidential, and nothing contained in such document shall be disclosed to any person by the '
                . 'Council, except where it is required to do so under any law.'
        );

        // 21.
        $s21 = $this->section($act, $part, '21', null, 'Undertaking to be given on a reference made.');
        $this->node(
            $act,
            $s21,
            'SUBSECTION',
            '(1)',
            null,
            'At anytime before making a recommendation under subsection (4) of section 20, the Council may '
                . 'accept any undertaking offered by a manufacturer or trader in respect of whom the investigation '
                . 'is being carried out, regarding the price at which the goods shall be sold or services shall be '
                . 'provided as the case may be, by such manufacturer or trader.'
        );
        $s21_2 = $this->node(
            $act,
            $s21,
            'SUBSECTION',
            '(2)',
            null,
            'Where an undertaking given under subsection (1) is accepted, it shall be the duty of the Council to —'
        );
        $this->node(
            $act,
            $s21_2,
            'PARAGRAPH',
            '(a)',
            null,
            'give adequate publicity to such undertaking in a manner it considers appropriate ;'
        );
        $this->node($act, $s21_2, 'PARAGRAPH', '(b)', null, 'periodically review the implementation of such undertaking ;');
        $s21_2c = $this->node(
            $act,
            $s21_2,
            'PARAGRAPH',
            '(c)',
            null,
            'consider whether, by reason of any change in the circumstances —'
        );
        $this->node(
            $act,
            $s21_2c,
            'SUBPARAGRAPH',
            '(i)',
            null,
            'the undertaking has become no longer appropriate and as such the person should be released from '
                . 'his duty to implement the undertaking ;'
        );
        $this->node($act, $s21_2c, 'SUBPARAGRAPH', '(ii)', null, 'whether any changes should be made to the undertaking ; or');
        $this->node(
            $act,
            $s21_2c,
            'SUBPARAGRAPH',
            '(iii)',
            null,
            'whether the existing undertaking should be replaced by a fresh undertaking ; and'
        );
        $this->node(
            $act,
            $s21_2,
            'PARAGRAPH',
            '(d)',
            null,
            'give notice to the person giving such undertaking, of any failure or shortcoming of such person in '
                . 'implementing the undertaking.'
        );
        $s21_3 = $this->node(
            $act,
            $s21,
            'SUBSECTION',
            '(3)',
            null,
            'Where the Council is of the view that action should be taken under paragraph (c) of subsection (2), '
                . 'the Council shall issue a notice to the person who entered into the undertaking, informing such person—'
        );
        $this->node($act, $s21_3, 'PARAGRAPH', '(a)', null, 'of the changes required to be made to the undertaking ;');
        $this->node(
            $act,
            $s21_3,
            'PARAGRAPH',
            '(b)',
            null,
            'of the requirements of the fresh undertaking which should replace the existing undertaking ; or'
        );
        $this->node($act, $s21_3, 'PARAGRAPH', '(c)', null, 'of his release from the duty of implementing the undertaking.');
        $s21_4 = $this->node(
            $act,
            $s21,
            'SUBSECTION',
            '(4)',
            null,
            'The Council shall, after issue of a notice under subsection (3), make a fresh decision—'
        );
        $this->node(
            $act,
            $s21_4,
            'PARAGRAPH',
            '(a)',
            null,
            'agreeing to a fresh undertaking, or to the undertaking being changed, in accordance with the '
                . 'recommendations made by the Council ; or'
        );
        $this->node(
            $act,
            $s21_4,
            'PARAGRAPH',
            '(b)',
            null,
            'agreeing to a fresh undertaking or to the undertaking being changed as offered by the trader or '
                . 'manufacturer on his own initiative.'
        );
        $this->node(
            $act,
            $s21,
            'SUBSECTION',
            '(5)',
            null,
            'Where an undertaking given by a manufacturer or trader is accepted by the Council under this '
                . 'section, any violation of such undertaking by that manufactuer or trader, shall be an offence under this Act.'
        );

        // 22.
        $s22 = $this->section(
            $act,
            $part,
            '22',
            null,
            'Members of the public, associations or organizations to refer certain matters to the Director-General.'
        );
        $this->node(
            $act,
            $s22,
            'SUBSECTION',
            '(1)',
            null,
            'Where goods are being sold or services are being provided at an excessive price and any member of '
                . 'the public or any association of persons or any organisation wish the Council to investigate into '
                . 'the matter, such member of the public, association or organization, as the case may be, may '
                . 'request the Director-General to refer the matter to the Council for investigation.'
        );
        $this->node(
            $act,
            $s22,
            'SUBSECTION',
            '(2)',
            null,
            'On receipt of a request under subsection (1) the Director-General may, where he is of the opinion '
                . 'that it is a matter which, having regard to the provisions of section 19 should be investigated '
                . 'into by the Council, refer the matter to the Council for investigation and the provisions of '
                . 'sections 20 and 21 shall, mutatis mutandis, apply to and in relation to, such investigation.'
        );

        // 23.
        $this->section(
            $act,
            $part,
            '23',
            'The Minister may, either of his own motion or on representations made to him by a person or body '
                . 'of persons, refer any question relating to the price of any goods or the charge for any service, '
                . 'to the Authority for examination and report, and accordingly, the Authority shall examine such '
                . 'question and send its report to the Minister within one month from the date of receipt of such reference.',
            'Advisory reference.'
        );

        // 24.
        $this->section(
            $act,
            $part,
            '24',
            'The Authority may, either of its own motion or on representations made to it by any person or body '
                . 'of persons, review any question relating to the price of any goods or the charge for any service '
                . 'and report thereon to the Minister.',
            'Authority to review questions relating to prices of goods &c.'
        );

        // 25.
        $this->section(
            $act,
            $part,
            '25',
            'The Minister may cause to be published any report made to him under the provisions of this Act, or '
                . 'any recommendations made in such report, in such manner as he thinks fit.',
            'Publication of a report made under this Act.'
        );

        // 26.
        $s26 = $this->section($act, $part, '26', null, 'Trader to display price list.');
        $this->node(
            $act,
            $s26,
            'SUBSECTION',
            '(1)',
            null,
            'Every trader shall exhibit conspicuously in his place of business, a notice specifying the maximum '
                . 'retail or wholesale price, as the case may be, of goods available for sale in his place of '
                . 'business other than the price of any goods, the price of which is marked on the goods itself or '
                . 'on the wrapper or pack containing it or marked in any other manner as may be required by any law.'
        );
        $this->node(
            $act,
            $s26,
            'SUBSECTION',
            '(2)',
            null,
            'A complete list of the price of goods available for sale shall be kept within the place of business '
                . 'at all times for inspection whenever required.'
        );

        // 27.
        $this->section(
            $act,
            $part,
            '27',
            'Every trader shall, register with the Authority on payment of an annual fee of a sum not exceeding '
                . 'rupees one hundred thousand as may be determined by the Minister, in consultation with the '
                . 'Minister in charge of the subject of Finance.',
            'Registration of traders.'
        );

        // 28.
        $s28 = $this->section(
            $act,
            $part,
            '28',
            'Every trader who sells any goods shall on demand, issue to the purchaser thereof, a receipt setting out—',
            'Trader to issue receipts to purchasers.'
        );
        $this->node($act, $s28, 'PARAGRAPH', '(a)', null, 'the date of the sale;');
        $this->node($act, $s28, 'PARAGRAPH', '(b)', null, 'the quantity of goods sold ;');
        $this->node($act, $s28, 'PARAGRAPH', '(c)', null, 'the price paid for such quantity;');
        $this->node(
            $act,
            $s28,
            'PARAGRAPH',
            '(d)',
            null,
            'nature of the transaction, that is to say, whether the sale was wholesale or retail; and'
        );
        $this->node(
            $act,
            $s28,
            'PARAGRAPH',
            '(e)',
            null,
            'any other requirements that may be imposed under any law relating to the issue of receipts by a trader.'
        );

        // 29.
        $s29 = $this->section($act, $part, '29', null, 'Trader to furnish notice board.');
        $this->node(
            $act,
            $s29,
            'SUBSECTION',
            '(1)',
            null,
            'Every trader shall keep in conspicuous place in his place of business, a notice board for the '
                . 'display of any notice, direction or warning issued by the Authority under this Act.'
        );
        $this->node(
            $act,
            $s29,
            'SUBSECTION',
            '(2)',
            null,
            'Every trader shall affix or cause to be affixed on such notice board any notice, direction or '
                . 'warning issued to such trader by the Authority under this Act.'
        );
        $this->node(
            $act,
            $s29,
            'SUBSECTION',
            '(3)',
            null,
            'Any person who removes, alters, obliterates, erases or defaces such notice, direction or warning '
                . 'other than a person acting under the direction or authority of the Authority, shall be guilty of '
                . 'an offence under this Act.'
        );

        // 30.
        $this->section(
            $act,
            $part,
            '30',
            'No trader shall, in the course of a trade or business, engage in any type of conduct that is '
                . 'misleading or deceptive or is likely to mislead or deceive the consumer or any other trader.',
            'Misleading or deceptive conduct.'
        );

        // 31.
        $s31 = $this->section(
            $act,
            $part,
            '31',
            'Any trader who, in the course of a trade or business, in connection with the supply or possible '
                . 'supply of goods or services or in connection with the promotion by any means of the supply or '
                . 'use of goods or services—',
            'False representations.'
        );
        $this->node(
            $act,
            $s31,
            'PARAGRAPH',
            '(a)',
            null,
            'falsely represents that goods or services are of a particular standard, quality or grade, or that '
                . 'goods are of a particular style or model ;'
        );
        $this->node($act, $s31, 'PARAGRAPH', '(b)', null, 'falsely represents that goods are new ;');
        $this->node(
            $act,
            $s31,
            'PARAGRAPH',
            '(c)',
            null,
            'represents that goods or services have sponsorship, approval, performance, characteristics '
                . 'accessories, uses or benefits they do not have ;'
        );
        $this->node(
            $act,
            $s31,
            'PARAGRAPH',
            '(d)',
            null,
            'represents that such trader has a sponsorship approval or affiliation he does not have ;'
        );
        $this->node(
            $act,
            $s31,
            'PARAGRAPH',
            '(e)',
            null,
            'makes false or misleading statements concerning the existence of, or amounts of price reduction or '
                . 'price increase ;'
        );
        $this->node(
            $act,
            $s31,
            'PARAGRAPH',
            '(f)',
            null,
            'makes false or misleading statements concerning the need for any goods, services, replacements or repairs ; or'
        );
        $this->node(
            $act,
            $s31,
            'PARAGRAPH',
            '(g)',
            null,
            'makes false or misleading statements concerning the existence or effect of any warranty or guarantee,'
        );
        $this->node($act, $s31, 'PARAGRAPH', null, null, 'shall be guilty of an offence under this Act.');

        // 32.
        $s32 = $this->section($act, $part, '32', null, 'Warranties in relation to the supply of goods or services.');
        $s32_1 = $this->node(
            $act,
            $s32,
            'SUBSECTION',
            '(1)',
            null,
            'In every contract for the supply of goods or for the provision of services by any person in the '
                . 'course of a business of supply of such goods or provisions of such services to a consumer, there '
                . 'is an implied warranty that—'
        );
        $this->node($act, $s32_1, 'PARAGRAPH', '(a)', null, 'the services will be provided with due care and skill ;');
        $this->node(
            $act,
            $s32_1,
            'PARAGRAPH',
            '(b)',
            null,
            'that any materials supplied in connection with provision of such services will be reasonably fit '
                . 'for the purpose for which they are supplied ;'
        );
        $this->node(
            $act,
            $s32_1,
            'PARAGRAPH',
            '(c)',
            null,
            'the goods supplied or services provided will be in conformity with the standards and specifications '
                . 'determined under section 12 of this Act ; and'
        );
        $this->node(
            $act,
            $s32_1,
            'PARAGRAPH',
            '(d)',
            null,
            'the goods supplied will be reasonably fit for the purpose for which they are supplied.'
        );
        $this->node(
            $act,
            $s32,
            'SUBSECTION',
            '(2)',
            null,
            'Where a trader or any person other than a trader supplies any goods or provides any service to a '
                . 'consumer in the course of a business and the consumer, expressly or by implication, makes known '
                . 'to the trader or other person of any particular purpose for which the goods or services are '
                . 'required or the result that he desire the service to achieve, there is an implied warranty that '
                . 'the services provided under the contract for the provision of such services and any materials '
                . 'supplied in connection with those services will be reasonably fit for that purpose or are of such '
                . 'a nature and quality that they might reasonably be expected to achieve that result, except where '
                . 'circumstances show the consumer does not rely, or that it is unreasonable for him to rely, on the '
                . 'trader\'s or such other person\'s skill or judgement.'
        );
        $this->node(
            $act,
            $s32,
            'SUBSECTION',
            '(3)',
            null,
            'A consumer aggrieved by the breach of an implied warranty as provided for in subsection (1) or (2) '
                . 'may make a complaint to the Authority in writing against such breach within one month of the '
                . 'supply of such goods or the provision of such services as the case may be, or the supply of '
                . 'materials supplied in connection with the provision of those services.'
        );
        $this->node(
            $act,
            $s32,
            'SUBSECTION',
            '(4)',
            null,
            'At any inquiry held into a complaint made under subsection (3), the Authority shall give the trader '
                . 'or other person against whom the complaint is made, an opportunity of being heard either in '
                . 'person or by an agent on his behalf.'
        );
        $this->node(
            $act,
            $s32,
            'SUBSECTION',
            '(5)',
            null,
            'Where after the inquiry the Authority is of opinion that a breach of an implied warranty has taken '
                . 'place, it shall order the trader or other person to pay compensation to the aggrieved party or '
                . 'refund the amount paid for the supply of such goods or provision of such services as the case may '
                . 'be, and for the supply of any materials in connection with the provision of those services, '
                . 'within such period as shall be specified in the order.'
        );
        $this->node(
            $act,
            $s32,
            'SUBSECTION',
            '(6)',
            null,
            'An order under subsection (5) shall be made in writing and communicated to such trader or other '
                . 'person by registered post.'
        );
        $this->node(
            $act,
            $s32,
            'SUBSECTION',
            '(7)',
            null,
            'Where a trader or any other person against whom an order is made under subsection (5) fails or '
                . 'refuses to pay such compensation or to refund the amount required to be paid as the case may be, '
                . 'within the period specified in the order, such sum may, on application being made in that behalf '
                . 'by the Authority to the Magistrate\'s Court having jurisdiction over the place of business or '
                . 'residence of the trader or such other person against whom the order was made, be recovered in '
                . 'like manner as a fine imposed by such court, notwithstanding that such sum may exceed the amount '
                . 'of a fine which that Court may, in the exercise of its ordinary jurisdiction impose.'
        );

        // 33.
        $this->section(
            $act,
            $part,
            '33',
            'At the request of the Minister, the Authority shall carry out a public sector and private sector '
                . 'efficiency study and submit a report to the Minister. It shall be the duty of the Minister to '
                . 'place such report before Parliament.',
            'Public efficiency reports.'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PART III — Promotion of Competition and Consumer Interest (ss. 34–38)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedPartIII(Act $act): void
    {
        $part = $this->node($act, null, 'PART', 'III', 'PROMOTION OF COMPETITION AND CONSUMER INTEREST');

        // 34.
        $s34 = $this->section(
            $act,
            $part,
            '34',
            null,
            'Authority to carry out investigations into anti-competitive practices.'
        );
        $this->node(
            $act,
            $s34,
            'SUBSECTION',
            '(1)',
            null,
            'The Authority may either of its own motion or on a complaint or request made to it by any person, '
                . 'any organization of consumers or an association of traders, carry out an investigation with '
                . 'respect to the prevalence of any anti-competitive practice.'
        );
        $this->node(
            $act,
            $s34,
            'SUBSECTION',
            '(2)',
            null,
            'It shall be the duty of the Authority to complete an investigation under subsection (1), within '
                . 'one hundred days of its initiation.'
        );

        // 35.
        $this->section(
            $act,
            $part,
            '35',
            'For the purpose of section 34, an anti-competitive practice shall be deemed to prevail, where a '
                . 'person in the course of business, pursues a course of conduct which of itself or when taken '
                . 'together with a course of condut pursued by persons associated with him, has or is intended to '
                . 'have or is likely to have the effect of restricting, distorting or preventing competition in '
                . 'connection with the production, supply or acquisition of goods in Sri Lanka or the supply or '
                . 'securing of services in Sri Lanka.',
            'Anti-competitive practice.'
        );

        // 36.
        $s36 = $this->section($act, $part, '36', null, 'Provisions relating to an investigation under section 34.');
        $this->node(
            $act,
            $s36,
            'SUBSECTION',
            '(1)',
            null,
            'The Authority may give to all persons including representatives of associations or organisations '
                . 'of consumers interested in a matter which forms the subject of an investigation under section 34, '
                . 'an opportunity of being heard and of producing such evidence, oral or documentary, as in the '
                . 'opinion of the Authority is relevant to such matter.'
        );
        $s36_2 = $this->node(
            $act,
            $s36,
            'SUBSECTION',
            '(2)',
            null,
            'For the purpose of conducting an investigation under section 33, the Authority shall have all the '
                . 'powers of a District Court—'
        );
        $this->node(
            $act,
            $s36_2,
            'PARAGRAPH',
            '(a)',
            null,
            'to issue notices and require the attendance of any witness ;'
        );
        $this->node(
            $act,
            $s36_2,
            'PARAGRAPH',
            '(b)',
            null,
            'to require the production of documents or records ; and'
        );
        $this->node(
            $act,
            $s36_2,
            'PARAGRAPH',
            '(c)',
            null,
            'to administer any oath or affirmation to any witness.'
        );
        $s36_3 = $this->node($act, $s36, 'SUBSECTION', '(3)', null, 'Where any person—');
        $this->node(
            $act,
            $s36_3,
            'PARAGRAPH',
            '(a)',
            null,
            'without sufficient reason publishes any statement or does anything during the progress or after '
                . 'the conclusion of any investigation, which may bring the Authority or any member thereof into '
                . 'disrepute ; or'
        );
        $this->node($act, $s36_3, 'PARAGRAPH', '(b)', null, 'interferes with the lawful process of the Authority ; or');
        $s36_3c = $this->node($act, $s36_3, 'PARAGRAPH', '(c)', null, 'in the course of an investigation—');
        $this->node(
            $act,
            $s36_3c,
            'SUBPARAGRAPH',
            '(i)',
            null,
            'fails without cause, to appear before the Authority at the time and place specified in any notice '
                . 'issued by the Authority to such person ; or'
        );
        $this->node(
            $act,
            $s36_3c,
            'SUBPARAGRAPH',
            '(ii)',
            null,
            'refuses to be sworn or affirmed or having been duly sworn or affirmed refuses or fails without '
                . 'cause, to answer any questions put to him relating to the matter being investigated by the Authority ; or'
        );
        $this->node(
            $act,
            $s36_3c,
            'SUBPARAGRAPH',
            '(iii)',
            null,
            'refuses or fails without cause, to produce and show to the Authority any document or record which '
                . 'is in his possession or control, and which in the opinion of the Authority is relevant to the '
                . 'matter being investigated by the Authority,'
        );
        $this->node(
            $act,
            $s36_3,
            'PARAGRAPH',
            null,
            null,
            'such person shall be guilty of an offence of contempt against or in disrespect of the Authority and shall be punishable for such offence by the Court of Appeal.'
        );
        $this->node(
            $act,
            $s36,
            'SUBSECTION',
            '(4)',
            null,
            'Where the Authority determines that a person had committed any offence of contempt against or in '
                . 'disrespect of its authority, the Authority may cause the Director-General to transmit to the '
                . 'Court of Appeal a certificate setting out its determination and every such certificate shall '
                . 'be signed by the Chairman of the Authority.'
        );
        $this->node(
            $act,
            $s36,
            'SUBSECTION',
            '(5)',
            null,
            'In any proceedings for the punishment of an offence of contempt which the Court of Appeal may '
                . 'think fit to take cognisance of as provided in subsection (3), any document purporting to be '
                . 'a certificate signed and transmitted to the Court of Appeal under subsection (4), shall be '
                . 'prima facie evidence of the facts stated in the determination set out therein, and be '
                . 'conclusive evidence that such determination was made by the Authority.'
        );

        // 37.
        $s37 = $this->section($act, $part, '37', null, 'Authority to make an application to the Council.');
        $this->node(
            $act,
            $s37,
            'SUBSECTION',
            '(1)',
            null,
            'Upon the conclusion of an investigation under section 34, the Authority may make an application '
                . 'to the Council for purpose of determining on such matter.'
        );
        $this->node(
            $act,
            $s37,
            'SUBSECTION',
            '(2)',
            null,
            'The application referred to in subsection (1), shall be made in writing and shall be in such '
                . 'form as may be prescribed for that purpose.'
        );

        // 38.
        $s38 = $this->section(
            $act,
            $part,
            '38',
            'Where upon conclusion of an investigation by the Authority under section 34, the Authority decides '
                . 'not to make an application to the Council under section 37 in respect of such investigation, '
                . 'the person, any orgnisation of consumers or association of traders, as the case may be, on '
                . 'whose complaint or request such investigation was caried out, may by application in writing '
                . 'addressed to the Council, request the Council to—',
            'Council to entertain applications made by any person or association of traders.'
        );
        $this->node(
            $act,
            $s38,
            'PARAGRAPH',
            '(a)',
            null,
            'call upon the Authority to submit to the Council its report on the investigation; and'
        );
        $this->node(
            $act,
            $s38,
            'PARAGRAPH',
            '(b)',
            null,
            'hear and determine such application, where the Council is of opinion that there is sufficient '
                . 'material in the report to warrant the Council to take up such application for determination.'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PART IV — Consumer Affairs Council (ss. 39–48)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedPartIV(Act $act): void
    {
        $part = $this->node($act, null, 'PART', 'IV', 'CONSUMER AFFAIRS COUNCIL');

        // 39.
        $s39 = $this->section($act, $part, '39', null, 'Establishment and constitution of a Consumer Affairs Council.');
        $this->node(
            $act,
            $s39,
            'SUBSECTION',
            '(1)',
            null,
            'There shall be established for the purpose of this Act a Council to be known as the "Consumer Affairs Council".'
        );
        $s39_2 = $this->node(
            $act,
            $s39,
            'SUBSECTION',
            '(2)',
            null,
            'The Council shall consist of the following persons who shall be appointed by the Minister :—'
        );
        $this->node($act, $s39_2, 'PARAGRAPH', '(a)', null, 'one person who has had wide experience in the field of commercial law;');
        $this->node($act, $s39_2, 'PARAGRAPH', '(b)', null, 'one person who has had wide experience in the management of business enterprises; and');
        $this->node($act, $s39_2, 'PARAGRAPH', '(c)', null, 'an economist with wide experience in trade practices and consumer affairs.');
        $this->node(
            $act,
            $s39,
            'SUBSECTION',
            '(3)',
            null,
            'The members of the Council shall hold office for a period of three years and the Minister shall '
                . 'nominate one of the members to be its Chairman.'
        );
        $this->node(
            $act,
            $s39,
            'SUBSECTION',
            '(4)',
            null,
            'The members of the Council shall be paid such remuneration as may be determined by the Minister, '
                . 'in consultation with the Minister in charge of the subject of Finance.'
        );
        $this->node(
            $act,
            $s39,
            'SUBSECTION',
            '(5)',
            null,
            'There shall be appointed a Secretary to the Council who shall be responsible for maintaining records '
                . 'of all proceedings of the Council, and for sending out any notices required to be sent by the Council under this Act.'
        );

        // 40.
        $s40 = $this->section($act, $part, '40', null, 'Function of the Council.');
        $this->node(
            $act,
            $s40,
            'SUBSECTION',
            '(1)',
            null,
            'The function of the Council shall be to hear and determine all applications and references made to it under this Act.'
        );
        $this->node(
            $act,
            $s40,
            'SUBSECTION',
            '(2)',
            null,
            'Where a decision of the Council is not unanimous, the decision of the majority of the members shall '
                . 'be deemed to be the decision of the Council.'
        );

        // 41.
        $s41 = $this->section($act, $part, '41', null, 'Order to be made by the Council.');
        $s41_1 = $this->node(
            $act,
            $s41,
            'SUBSECTION',
            '(1)',
            null,
            'Where an application is made to the Council under section 37 or section 38, as the case may be, '
                . 'the Council shall, on being satisfied that —'
        );
        $this->node(
            $act,
            $s41_1,
            'PARAGRAPH',
            '(a)',
            null,
            'an anti-competitive practice exists but such anti-competitive practice does not operate or is '
                . 'not likely to operate against public interest, by order made in that behalf, authorise such '
                . 'anti-competitive practice; or'
        );
        $s41_1b = $this->node(
            $act,
            $s41_1,
            'PARAGRAPH',
            '(b)',
            null,
            'an anti-competitive practice exists and that it operates against public interest, by order made '
                . 'in that behalf, provide for —'
        );
        $this->node(
            $act,
            $s41_1b,
            'SUBPARAGRAPH',
            '(i)',
            null,
            'the termination of such anti-competitive practice in such manner as may be specified in the order; and'
        );
        $this->node(
            $act,
            $s41_1b,
            'SUBPARAGRAPH',
            '(ii)',
            null,
            'such other action as the Council may consider necessary for the purpose of remedying or '
                . 'preventing the adverse effects of any anti-competitive practice.'
        );
        $s41_2 = $this->node(
            $act,
            $s41,
            'SUBSECTION',
            '(2)',
            null,
            'In determining for the purposes of this section, whether any anti-competitive practice operates, '
                . 'or is likely to operate, against public interest, the Council shall take into account all matters '
                . 'which appear to the Council to be relevant to the matter under investigation and shall have '
                . 'special regard to the desirability of —'
        );
        $this->node(
            $act,
            $s41_2,
            'PARAGRAPH',
            '(a)',
            null,
            'maintaining and promoting effective competition between persons supplying goods and providing services;'
        );
        $this->node(
            $act,
            $s41_2,
            'PARAGRAPH',
            '(b)',
            null,
            'promoting the interests of consumers, purchasers and other users of goods and services in respect '
                . 'of the price and quality of such goods and services and the variety of goods supplied and '
                . 'services provided in Sri Lanka; and'
        );
        $this->node(
            $act,
            $s41_2,
            'PARAGRAPH',
            '(c)',
            null,
            'promoting through competition the reduction of costs, the development and use of new techniques '
                . 'and products and facilitating the entry of new competitors into existing markets.'
        );
        $this->node(
            $act,
            $s41,
            'SUBSECTION',
            '(3)',
            null,
            'Where an application is made to the Council under section 37 or section 38, as the case may be, '
                . 'it shall be the duty of the Council to make its order on such application, within one month of its receipt.'
        );

        // 42.
        $this->section(
            $act,
            $part,
            '42',
            'The Authority may cause to be published any order made by the Council under section 41, in such manner as it thinks fit.',
            'Publication of an order made under section 41.'
        );

        // 43.
        $s43 = $this->section($act, $part, '43', null, 'Power to issue notices to witnesses.');
        $this->node(
            $act,
            $s43,
            'SUBSECTION',
            '(1)',
            null,
            'The Council shall have the power to issue notices and examine witnesses at any inquiry or '
                . 'investigation conducted by the Council. Every notice shall be issued under the hand of the '
                . 'Secretary to the Council.'
        );
        $this->node(
            $act,
            $s43,
            'SUBSECTION',
            '(2)',
            null,
            'A notice may be issued by delivering it to the person named therein, or if that is not '
                . 'practicable, by leaving it at the last known place of abode of that person.'
        );
        $this->node(
            $act,
            $s43,
            'SUBSECTION',
            '(3)',
            null,
            'Every person to whom a notice is issued shall attend before the Council at the time and place '
                . 'mentioned therein, and shall give evidence or produce such documents, records or other things '
                . 'as are required of him and are in his possession or power, according to the tenor of the notice.'
        );

        // 44.
        $s44 = $this->section($act, $part, '44', null, 'Evidence.');
        $s44_1 = $this->node(
            $act,
            $s44,
            'SUBSECTION',
            '(1)',
            null,
            'The Council shall for the purpose of any inquiry or investigation under this Act have power —'
        );
        $this->node(
            $act,
            $s44_1,
            'PARAGRAPH',
            '(a)',
            null,
            'to procure and receive all such evidence written or oral, and to examine all such persons as '
                . 'witnesses, as it may think necessary or desirable to procure, receive or examine; and'
        );
        $this->node(
            $act,
            $s44_1,
            'PARAGRAPH',
            '(b)',
            null,
            'to require the evidence, whether written or oral of any witness to be given on oath or '
                . 'affirmation and such oath or affirmation to be that which would be required of that witness '
                . 'if he was giving evidence in a court, and to administer an oath or affirmation to every such witness.'
        );
        $this->node(
            $act,
            $s44,
            'SUBSECTION',
            '(2)',
            null,
            'In the conduct of proceedings under this Act, the Council shall not be bound by any of the '
                . 'provisions of the Evidence Ordinance.'
        );

        // 45.
        $s45 = $this->section(
            $act,
            $part,
            '45',
            'If any person upon whom a notice is issued under section 43 —',
            'Failure to obey a notice issued or to give evidence &c.'
        );
        $this->node(
            $act,
            $s45,
            'PARAGRAPH',
            '(a)',
            null,
            'fails without cause which in the opinion of the Council is reasonable, to appear before the '
                . 'Council at the time and place mentioned in the notice;'
        );
        $this->node(
            $act,
            $s45,
            'PARAGRAPH',
            '(b)',
            null,
            'refuses to be sworn-in or affirmed, or having been duly sworn-in or affirmed, refuses or fails '
                . 'without cause which in the opinion of the Council is reasonable, to answer any questions put to '
                . 'him relating to the matters being inquired or investigated into by the Council; or'
        );
        $this->node(
            $act,
            $s45,
            'PARAGRAPH',
            '(c)',
            null,
            'refuses or fails without cause, which in the opinion of the Council is reasonable, to produce to '
                . 'the Council any document or other thing which is in his possession or power and which is in '
                . 'the opinion of the Council necessary for arriving at the truth of the matters to be inquired '
                . 'or investigated into,'
        );
        $this->node($act, $s45, 'PARAGRAPH', null, null, 'such person shall be guilty of the offence of contempt against or in disrespect of, the Council.');

        // 46.
        $s46 = $this->section($act, $part, '46', null, 'Punishment for offence of contempt.');
        $this->node(
            $act,
            $s46,
            'SUBSECTION',
            '(1)',
            null,
            'Any act or omission in contravention of the provisions of section 45 shall constitute an offence '
                . 'of contempt against, or in disrespect of the Council, if such act or omission would if done or '
                . 'omitted to be done in relation to the Court of Appeal, have constituted an offence of contempt '
                . 'against, or in disrespect of the authority of such Court.'
        );
        $this->node(
            $act,
            $s46,
            'SUBSECTION',
            '(2)',
            null,
            'Where the Council determines that a person has committed an offence of contempt against or in '
                . 'disrespect of its authority, the Council may cause its Chairman to transmit to the Court of '
                . 'Appeal a certificate setting out such determination. Every such certificate shall be signed '
                . 'by the Chairman of the Council.'
        );
        $s46_3 = $this->node(
            $act,
            $s46,
            'SUBSECTION',
            '(3)',
            null,
            'In any proceedings for the punishment of an offence of contempt which the Court of Appeal may '
                . 'think fit to take cognisance of as provided for in this section, any document purporting to '
                . 'be a certificate signed and transmitted to such Court under subsection (2), shall—'
        );
        $this->node(
            $act,
            $s46_3,
            'PARAGRAPH',
            '(a)',
            null,
            'be received in evidence, and be deemed to be such a certificate without further proof unless, '
                . 'the contrary is proved; and'
        );
        $this->node(
            $act,
            $s46_3,
            'PARAGRAPH',
            '(b)',
            null,
            'be evidence that the determination set out in the certificate was made by the Council on the '
                . 'facts stated in the determination.'
        );
        $this->node(
            $act,
            $s46,
            'SUBSECTION',
            '(4)',
            null,
            'In any proceeding taken as provided for in this section for the punishment of any alleged '
                . 'offence of contempt against or in disrespect of the Council, any member of the Council may '
                . 'be summoned or examined as a witness and any such proceeding shall be heard and disposed of '
                . 'within three months of the receipt of the certificate referred to in subsection (2).'
        );

        // 47.
        $this->section(
            $act,
            $part,
            '47',
            'Every person who gives evidence before the Council shall in respect of such evidence, be entitled '
                . 'to all the privileges to which a witness giving evidence before a court of law is entitled to in '
                . 'respect of evidence given by him before such court.',
            'Privileges of witnesses.'
        );

        // 48.
        $this->section(
            $act,
            $part,
            '48',
            'The Council may determine the procedure to be followed in the conduct of any inquiry or '
                . 'investigation before such Council.',
            'Procedure to be laid down by the Council.'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PART V — Fund of the Authority (ss. 49–51)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedPartV(Act $act): void
    {
        $part = $this->node($act, null, 'PART', 'V', 'FUND OF THE AUTHORITY');

        // 49.
        $s49 = $this->section($act, $part, '49', null, 'Fund of the Authority.');
        $this->node($act, $s49, 'SUBSECTION', '(1)', null, 'The Authority shall have its own Fund.');
        $s49_2 = $this->node($act, $s49, 'SUBSECTION', '(2)', null, 'There shall be paid into the Fund of the Authority');
        $this->node(
            $act,
            $s49_2,
            'PARAGRAPH',
            '(a)',
            null,
            'all such sums of money as may be voted from time to time by Parliament for the use of the Authority;'
        );
        $this->node(
            $act,
            $s49_2,
            'PARAGRAPH',
            '(b)',
            null,
            'all such sums of money as may be received by the Authority by way of fees or otherwise in the discharge of its functions;'
        );
        $this->node($act, $s49_2, 'PARAGRAPH', '(c)', null, 'the proceeds from the sale of any goods forfeited under this Act;');
        $this->node($act, $s49_2, 'PARAGRAPH', '(d)', null, 'one-third of every fine imposed for the commission of any offence under this Act;');
        $this->node($act, $s49_2, 'PARAGRAPH', '(e)', null, 'all such sums of money as may be made available to it by way of grant or donations; and');
        $this->node($act, $s49_2, 'PARAGRAPH', '(f)', null, 'all such sums of money as are credited or transferred to the Fund under section 59 or section 73.');
        $s49_3 = $this->node($act, $s49, 'SUBSECTION', '(3)', null, 'There shall be paid out of the Fund');
        $this->node($act, $s49_3, 'PARAGRAPH', '(a)', null, 'remuneration payable to the members of the Authority and of the Council;');
        $this->node(
            $act,
            $s49_3,
            'PARAGRAPH',
            '(b)',
            null,
            'such sums as the Authority may consider necessary for the promotion, assistance and encouragement '
                . 'of consumer organizations and the administration and development of such organizations ;'
        );
        $this->node(
            $act,
            $s49_3,
            'PARAGRAPH',
            '(c)',
            null,
            'such sums as the Authority may consider necessary for consumer education and the dissemination '
                . 'of information relating thereto, and for any purpose connected with or incidental to the '
                . 'furtherance of such education; and'
        );
        $this->node(
            $act,
            $s49_3,
            'PARAGRAPH',
            '(d)',
            null,
            'such sums of money as may be required to defray the expenses incurred by the Authority and the '
                . 'Council in the exercise, discharge and performance of its powers, functions and duties under this Act.'
        );

        // 50.
        $s50 = $this->section($act, $part, '50', null, 'Financial year and the audit of accounts.');
        $this->node($act, $s50, 'SUBSECTION', '(1)', null, 'The Financial year of the Authority shall be the calendar year.');
        $this->node(
            $act,
            $s50,
            'SUBSECTION',
            '(2)',
            null,
            'The Authority shall cause proper books of accounts to be kept of the income and expenditure, '
                . 'assets and liabilities and all other transactions of the Authority.'
        );
        $this->node(
            $act,
            $s50,
            'SUBSECTION',
            '(3)',
            null,
            'The provisions of Article 154 of the Constitution relating to the audit of the accounts of '
                . 'public corporations shall apply to and in respect of the audit of the accounts of the Authority.'
        );

        // 51.
        $this->section(
            $act,
            $part,
            '51',
            'The provisions of Part II of the Finance Act, No. 38 of 1971, shall, mutatis mutandis, apply '
                . 'to the financial control and accounts of the Authority.',
            'Application of Part II of the Finance Act, No. 38 of 1971.'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PART VI — Staff of the Authority (ss. 52–54)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedPartVI(Act $act): void
    {
        $part = $this->node($act, null, 'PART', 'VI', 'STAFF OF THE AUTHORITY');

        // 52.
        $s52 = $this->section($act, $part, '52', null, 'Director-General of the Authority.');
        $this->node(
            $act,
            $s52,
            'SUBSECTION',
            '(1)',
            null,
            'The Authority may with the approval in writing of the Minister, appoint a Director-General to '
                . 'the Authority (in this Act referred to as the "Director-General").'
        );
        $this->node(
            $act,
            $s52,
            'SUBSECTION',
            '(2)',
            null,
            'The Director-General appointed under subsection (1) shall be the chief executive officer of '
                . 'the Authority, and shall at all times act under the directions of the Authority.'
        );
        $this->node(
            $act,
            $s52,
            'SUBSECTION',
            '(3)',
            null,
            'Director-General shall be paid such remuneration as may be determined by the Authority in consultation with the Minister.'
        );
        $this->node(
            $act,
            $s52,
            'SUBSECTION',
            '(4)',
            null,
            'Whenever the Director-General is by reason of illness or absence from Sri Lanka or for any '
                . 'other cause, unable to perform the duties of his office, the Authority may, with the approval '
                . 'in writing to the Minister, appoint some other person to act in his place, during such absence.'
        );

        // 53.
        $this->section(
            $act,
            $part,
            '53',
            'The Authority may appoint such number of Directors, Deputy Directors, Assistant Directors and '
                . 'other officers, servants and advisers as are necessary for the performance of the work of '
                . 'the Authority and of the Council, who shall be remunerated at such rates as may be determined '
                . 'by the Authority in consultation with the Minister.',
            'Appointment of officers, servants and advisers to the Authority.'
        );

        // 54.
        $s54 = $this->section($act, $part, '54', null, 'Appointment of public officers to the staff of the Authority.');
        $this->node(
            $act,
            $s54,
            'SUBSECTION',
            '(1)',
            null,
            'At the request of the Authority, any officer in the public service may, with the consent of '
                . 'that officer and the Secretary to the Ministry of the Minister in charge of the subject of '
                . 'Public Administration, be temporarily appointed to the staff of the Authority for such period '
                . 'as may be determined by the Authority, with like consent, or with like consent be permanently '
                . 'appointed to such staff.'
        );
        $this->node(
            $act,
            $s54,
            'SUBSECTION',
            '(2)',
            null,
            'Where any officer in the public service is temporarily appointed to the staff of the Authority '
                . 'the provisions of subsection (2) of section 14 of the National Transport Commission Act, '
                . 'No. 37 of 1991, shall, mutatis mutandis, apply and in relation to him.'
        );
        $this->node(
            $act,
            $s54,
            'SUBSECTION',
            '(3)',
            null,
            'Where any officer in the public service is permanently appointed to the staff of the Authority, '
                . 'the provisions of subsection (3) of section 14 of the National Transport Commission Act, '
                . 'No. 37 of 1991, shall, mutatis mutandis, apply to, and in relation to him.'
        );
        $this->node(
            $act,
            $s54,
            'SUBSECTION',
            '(4)',
            null,
            'Where the Authority employs any person who has agreed to serve the Government for a specified '
                . 'period, service to the Authority by that person shall be regarded as service to the Government '
                . 'for the purpose of discharging the obligations under such agreement.'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PART VII — General (ss. 55–77)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedPartVII(Act $act): void
    {
        $part = $this->node($act, null, 'PART', 'VII', 'GENERAL');

        // 55.
        $s55 = $this->section($act, $part, '55', null, 'Power of Authority to send warning.');
        $this->node(
            $act,
            $s55,
            'SUBSECTION',
            '(1)',
            null,
            'Notwithstanding anything to the contrary in any provisions of this Act, the Authority may, if '
                . 'it is satisfied after such inquiry as it may deem necessary that any person has contravened '
                . 'any of the provisions of this Act or any direction given thereunder, in the case of the '
                . 'first contravention, send to such person a warning in writing.'
        );
        $this->node($act, $s55, 'SUBSECTION', '(2)', null, 'A warning under subsection (1) shall be sent by the Authority by registered post.');

        // 56.
        $s56 = $this->section($act, $part, '56', null, 'Power to require maintenance of records and the furnishing of returns.');
        $s56_1 = $this->node(
            $act,
            $s56,
            'SUBSECTION',
            '(1)',
            null,
            'The Authority may, for the proper discharge of its functions under this Act, require the '
                . 'manufacturers, importers, distributors and exporters of any goods or services—'
        );
        $this->node(
            $act,
            $s56_1,
            'PARAGRAPH',
            '(a)',
            null,
            'to maintain records in respect of such matters as the Authority may consider necessary for '
                . 'the proper discharge of its functions under this Act and in such form as may be determined by the Authority; and'
        );
        $this->node(
            $act,
            $s56_1,
            'PARAGRAPH',
            '(b)',
            null,
            'to furnish to the authority returns in respect of such matters as the Authority may consider '
                . 'necessary for the proper discharge of its functions under this Act, at such intervals and '
                . 'in such form as may be determined by the Authority.'
        );
        $this->node(
            $act,
            $s56,
            'SUBSECTION',
            '(2)',
            null,
            'It shall be the duty of all manufacturers, importers, distributors and exporters who are '
                . 'required under paragraph (a) of subsection (1) to maintain records, to preserve the records '
                . 'being so maintained, for a period not exceeding six years.'
        );

        // 57.
        $s57 = $this->section($act, $part, '57', null, 'Power of Authority to call for information.');
        $this->node(
            $act,
            $s57,
            'SUBSECTION',
            '(1)',
            null,
            'The Authority or any person authorised in that behalf by the Authority may, by notice in writing '
                . 'require any person within such period as shall be specified in the notice, to furnish any '
                . 'information or to produce any document as shall be specified in such notice, which the Authority '
                . 'may consider necessary for the proper discharge of its functions under this Act, and it shall be '
                . 'the duty of any person who receives such notice to comply with such requirement within the time '
                . 'specified in such notice, notwithstanding the provisions of any written law which may prohibit '
                . 'such person from disclosing such information or from producing such document : '
                . 'Provided however, that nothing in this subsection shall be read and construed as enabling the '
                . 'Authority or any person authorised in that behalf by the Authority, by notice to require any '
                . 'person to furnish any information or to produce any document, if the disclosure of such information '
                . 'or the production of such document is prohibited by any provision of any law providing for the '
                . 'imposition and recovery of any tax.'
        );
        $this->node(
            $act,
            $s57,
            'SUBSECTION',
            '(2)',
            null,
            'No information contained in a return furnished under section 56 and no information furnished or '
                . 'the contents of a document produced in compliance with the terms of a notice issued under this '
                . 'section, shall be published or communicated by the Authority to any other person except with the '
                . 'consent of the person furnishing such return or information, or producing such document, as the '
                . 'case may be, or in the course of the discharge of its functions by the Authority.'
        );

        // 58.
        $s58 = $this->section($act, $part, '58', null, 'Power of entry, inspection and search.');
        $s58_1 = $this->node(
            $act,
            $s58,
            'SUBSECTION',
            '(1)',
            null,
            'The Authority or any other officer authorised in writing in that behalf by the Authority may, for '
                . 'the purpose of ascertaining whether the provisions of this Act or any regulation made thereunder '
                . 'are being complied with, have the power on reasonable grounds and on production, if so required, '
                . 'his credentials'
        );
        $this->node(
            $act,
            $s58_1,
            'PARAGRAPH',
            '(a)',
            null,
            'to enter, inspect and search at all reasonable hours of the day the premises in which any '
                . 'manufacturer or trader is carrying on his business or any other premises where any goods are '
                . 'being stored or exposed for sale ;'
        );
        $this->node(
            $act,
            $s58_1,
            'PARAGRAPH',
            '(b)',
            null,
            'to seize and detain any goods found in such premises in contravention of the provisions of section 16 or 17; and'
        );
        $this->node(
            $act,
            $s58_1,
            'PARAGRAPH',
            '(c)',
            null,
            'to inspect, take copies of or seize and detain any records or documents required to be kept by or '
                . 'under this Act or any other law in respect of such business.'
        );
        $this->node(
            $act,
            $s58,
            'SUBSECTION',
            '(2)',
            null,
            'Any goods seized and detained under paragraph (b) of subsection (1), shall be produced in court, '
                . 'and on conviction of such manufacturer or trader, be forfeited and disposed of in such manner as '
                . 'may be determined by court, and any money realised shall be credited to the Fund of the Authority.'
        );

        // 59.
        $s59 = $this->section($act, $part, '59', null, 'Sale of perishable goods.');
        $this->node(
            $act,
            $s59,
            'SUBSECTION',
            '(1)',
            null,
            'Where any goods seized and detained under paragraph (b) of subsection (1) of section 58, is of '
                . 'such perishable nature, as in the opinion of the Authority render their immediate sale necessary '
                . 'or advisable, the Authority shall give not less than three days notice of the sale of such goods '
                . 'by an advertisement published in one Sinhala, Tamil and English newspaper each circulating in '
                . 'Sri Lanka and thereafter cause such goods to be sold.'
        );
        $this->node(
            $act,
            $s59,
            'SUBSECTION',
            '(2)',
            null,
            'The proceeds of a sale under subsection (1) of any goods, shall be credited to a special account '
                . 'to be maintained for that purpose by the Authority, and where the person from whom the goods '
                . 'were seized is acquitted or discharged subsequently by the court, the amount realised from the '
                . 'sale of such goods shall be given to such person.'
        );
        $this->node(
            $act,
            $s59,
            'SUBSECTION',
            '(3)',
            null,
            'Where the court convicts the person from whom the goods were seized, the money realised from the '
                . 'sale of such goods and lying to the credit of the special account referred to in subsection (2), '
                . 'shall be credited to the Fund of the Authority.'
        );

        // 60.
        $s60 = $this->section($act, $part, '60', null, 'offences and Penalties.');
        $s60_1 = $this->node($act, $s60, 'SUBSECTION', '(1)', null, 'Any person who. —');
        $s60_1a = $this->node($act, $s60_1, 'PARAGRAPH', '(a)', null, 'fails or refuses to furnish');
        $this->node($act, $s60_1a, 'SUBPARAGRAPH', '(i)', null, 'a return when required by the Authority to do so under section 56; or');
        $this->node($act, $s60_1a, 'SUBPARAGRAPH', '(ii)', null, 'any information or to produce any document when required to do so by a notice sent under section 57;');
        $this->node(
            $act,
            $s60_1,
            'PARAGRAPH',
            '(b)',
            null,
            'knowingly makes any false statement in any return furnished by him under this Act, or knowingly '
                . 'furnishes any false information when required by the Authority to furnish any information; or'
        );
        $this->node(
            $act,
            $s60_1,
            'PARAGRAPH',
            '(c)',
            null,
            'obstructs any officer acting in the exercise of his powers under section 58,'
        );
        $this->node($act, $s60_1, 'PARAGRAPH', null, null, 'shall be guilty of an offence under this Act, and shall on conviction after trial before a Magistrate, be liable —');
        $this->node(
            $act,
            $s60_1,
            'SUBPARAGRAPH',
            '(i)',
            null,
            'where such person is not a body corporate, to a fine not less than rupees one thousand and not '
                . 'exceeding rupees five thousand or to imprisonment of either description for a term not exceeding '
                . 'three months or to both such fine and imprisonment in the case of a first offence, and to a fine '
                . 'not less than rupees two thousand and not exceeding rupees ten thousand or to an imprisonment of '
                . 'either description for a term not exceeding six months or to both such fine and imprisonment in '
                . 'the case of a subsequent offence; or'
        );
        $this->node(
            $act,
            $s60_1,
            'SUBPARAGRAPH',
            '(ii)',
            null,
            'where such person is a body corporate, to a fine not less than rupees five thousand and not '
                . 'exceeding rupees ten thousand in the case of a first offence, and to a fine not less than rupees '
                . 'ten thousand and not exceeding rupees twenty thousand in the case of a subsequent offence.'
        );

        $s60_2 = $this->node($act, $s60, 'SUBSECTION', '(2)', null);
        $s60_2a = $this->node(
            $act,
            $s60_2,
            'PARAGRAPH',
            '(a)',
            null,
            'Any person who fails or refuses to comply with an order made under paragraph (b) of subsection (1) '
                . 'of section 41 or acts in contravention of such order, shall be guilty of an offence under this '
                . 'Act, and shall on conviction after trial before a Magistrate be liable'
        );
        $this->node(
            $act,
            $s60_2a,
            'SUBPARAGRAPH',
            '(i)',
            null,
            'where such person is not a body corporate, to a fine not less than rupees five thousand and not '
                . 'exceeding rupees fifty thousand or to imprisonment of either description for a term not exceeding '
                . 'one year or to both such fine and imprisonment in the case of a first offence, and to a fine '
                . 'not less than rupees ten thousand and not exceeding rupees one hundred thousand or to an '
                . 'imprisonment of either description for a term not exceeding two years or to both such fine and '
                . 'imprisonment in the case of a subsequent offence; or'
        );
        $this->node(
            $act,
            $s60_2a,
            'SUBPARAGRAPH',
            '(ii)',
            null,
            'where such person is a body corporate, to a fine not less than rupees fifty thousand and not '
                . 'exceeding rupees one million in the case of a first offence, and to a fine not less than rupees '
                . 'one hundred thousand and not exceeding rupees two million in the case of subsequent offence.'
        );
        $this->node(
            $act,
            $s60_2,
            'PARAGRAPH',
            '(b)',
            null,
            'The court may in addition, order such persons to refrain from carrying on the activities in respect '
                . 'of which an order has been made under paragraph (b) of subsection (1) of section 41 for such '
                . 'period as may be determined by such court or until such person complies with the order made '
                . 'under that paragraph.'
        );

        $s60_3 = $this->node(
            $act,
            $s60,
            'SUBSECTION',
            '(3)',
            null,
            'Any person guilty of an offence under this Act by reason of a contravention of the provisions of '
                . 'section 15 or section 16 or section 17 shall, on conviction after trial before a Magistrate, be liable'
        );
        $this->node(
            $act,
            $s60_3,
            'PARAGRAPH',
            '(a)',
            null,
            'where such person is not a body corporate, to a fine not less than rupees one thousand and not '
                . 'exceeding rupees ten thousand or to an imprisonment of either description for a term not '
                . 'exceeding six months or to both such fine and imprisonment in the case of a first offence, '
                . 'and to a fine not less than rupees two thousand and not exceeding rupees twenty thousand or to '
                . 'an imprisonment of either description for a term not exceeding one year or to both such fine and '
                . 'imprisonment in the case of a subsequent offence; or'
        );
        $this->node(
            $act,
            $s60_3,
            'PARAGRAPH',
            '(b)',
            null,
            'where such person is a body corporate, to a fine not less than rupees ten thousand and not '
                . 'exceeding rupees fifty thousand in the case of a first offence, and to a fine not less than '
                . 'rupees one hundred thousand and not exceeding rupees two hundred thousand in the case of a '
                . 'subsequent offence.'
        );

        $s60_4 = $this->node(
            $act,
            $s60,
            'SUBSECTION',
            '(4)',
            null,
            'Any person guilty of an offence under this Act by reason of a contravention of the provisions of '
                . 'section 18 shall, on conviction after trial before a Magistrate, be liable'
        );
        $this->node(
            $act,
            $s60_4,
            'PARAGRAPH',
            '(a)',
            null,
            'where such person is not a body corporate, to a fine not less than rupees five thousand and not '
                . 'exceeding rupees fifty thousand or to an imprisonment of either discrpition for a term not '
                . 'exceeding one year or to both such fine and imprisonment in the case of a first offence, and '
                . 'to a fine not less than rupees ten thousand and not exceeding rupees one hundred thousand or '
                . 'to an imprisonment of either description for a term not exceeding two years or to both such '
                . 'fine and imprisonment in the case of a subsequent offence; or'
        );
        $this->node(
            $act,
            $s60_4,
            'PARAGRAPH',
            '(b)',
            null,
            'where such person is a body corporate, to a fine not less than rupees fifty thousand and not '
                . 'exceeding rupees one million in the case of a first offence, and to a fine not less than '
                . 'rupees one hundred thousand and not exceeding rupees two million in the case of a subsequent offence.'
        );

        $s60_5 = $this->node(
            $act,
            $s60,
            'SUBSECTION',
            '(5)',
            null,
            'Every person who acts in contravention of any provision of this Act, (other than the provisions '
                . 'referred to in subsections (1), (2), (3) and (4) of this section) or any requirements imposed '
                . 'under any such provision, or any regulations made under section 72, shall be guilty of an '
                . 'offence under this Act and shall, on conviction after trial before a Magistrate, be liable'
        );
        $this->node(
            $act,
            $s60_5,
            'PARAGRAPH',
            '(a)',
            null,
            'where such person is not a body corporate, to a fine not less than rupees one thousand and not '
                . 'exceeding rupees ten thousand or to an imprisonment of either description for a term not exceeding '
                . 'six months or to both such fine and imprisonment in the case of a first offence, and to a fine '
                . 'not less than rupees two thousand and not exceeding rupees twenty thousand or to an imprisonment '
                . 'of either description for a term not exceeding one year or to both such fine and imprisonment in '
                . 'the case of a subsequent offence; or'
        );
        $this->node(
            $act,
            $s60_5,
            'PARAGRAPH',
            '(b)',
            null,
            'where such person is a body corporate to a fine not less than rupees ten thousand and not exceeding '
                . 'rupees one hundred thousand in the case of a first offence, and to a fine not less than rupees '
                . 'twenty thousand and not exceeding rupees two hundred thousand in the case of a subsequent offence.'
        );

        $this->node(
            $act,
            $s60,
            'SUBSECTION',
            '(6)',
            null,
            'The Authority may publish or cause to be published in such manner as it shall deem fit, the names '
                . 'and addresses of all persons convicted under this Act.'
        );

        $s60_7 = $this->node($act, $s60, 'SUBSECTION', '(7)', null, 'Where any offence under this Act is committed by a body of persons, then —');
        $this->node($act, $s60_7, 'PARAGRAPH', '(a)', null, 'if the body of persons is a body corporate, every director and officer of such body corporate; and');
        $this->node(
            $act,
            $s60_7,
            'PARAGRAPH',
            '(b)',
            null,
            'if the body of persons is a firm, every partner of that firm,'
        );
        $this->node(
            $act,
            $s60_7,
            'PARAGRAPH',
            null,
            null,
            'shall be deemed guilty of that offence : Provided that no such director, officer or partner shall '
                . 'be deemed guilty of that offence if he proves that such offence was committed without his '
                . 'knowledge or that he exercised all due diligence to prevent the commission of such offence.'
        );

        // 61.
        $s61 = $this->section($act, $part, '61', null, 'Obtaining samples for purpose of ensuring compliance with standards and specifications.');
        $s61_1 = $this->node(
            $act,
            $s61,
            'SUBSECTION',
            '(1)',
            null,
            'The Authority may where it has reason to believe that any goods that are being sold do not conform '
                . 'to the standards and specifications laid down under section 12 of this Act—'
        );
        $s61_1a = $this->node($act, $s61_1, 'PARAGRAPH', '(a)', null, 'authorize an officer in writing to obtain a sample of such goods');
        $this->node($act, $s61_1a, 'SUBPARAGRAPH', '(i)', null, 'from any premises in which a manufacturer or trader of such goods are carrying on his business;');
        $this->node($act, $s61_1a, 'SUBPARAGRAPH', '(ii)', null, 'from any premises where any such goods are being stored or exposed for sale ; or');
        $this->node($act, $s61_1a, 'SUBPARAGRAPH', '(iii)', null, 'from any vehicle in which such goods are being transported; and');
        $this->node(
            $act,
            $s61_1,
            'PARAGRAPH',
            '(b)',
            null,
            'issue a directive to the manufacturer or trader from whose possession such sample was obtained, '
                . 'to refrain from selling or exposing for sale the goods concerned for a period to be specified '
                . 'in the directive, so however such period shall not exceed a period of six weeks from the date '
                . 'of the issue of such directive.'
        );

        $this->node(
            $act,
            $s61,
            'SUBSECTION',
            '(2)',
            null,
            'It shall be the duty of the officer who obtained the sample to forthwith submit it to an approved '
                . 'analyst for purpose of determining whether such goods conforms to the standards and specifications '
                . 'laid down under section 12, and the said approved analyst may, except in exceptional circumstances, '
                . 'submit his report to the Authority within a period not exceeding one month of the receipt of the sample.'
        );

        $s61_3 = $this->node($act, $s61, 'SUBSECTION', '(3)', null, 'On receipt of the report of an approved analyst, the Authority shall —');
        $this->node(
            $act,
            $s61_3,
            'PARAGRAPH',
            '(a)',
            null,
            'where it discloses that the goods concerned conforms to the standards and specifications laid down '
                . 'under section 12, forthwith remove the restriction imposed by the directive issued under subsection (1); or'
        );
        $this->node(
            $act,
            $s61_3,
            'PARAGRAPH',
            '(b)',
            null,
            'where it discloses that the goods do not conform to the standards and specifications laid down '
                . 'under section 12 of this Act and that such non conformity has resulted in the goods concerned '
                . 'being rendered unfit for any use or consumption, seize and detain such goods or consignment of '
                . 'goods as the case may be.'
        );

        $this->node(
            $act,
            $s61,
            'SUBSECTION',
            '(4)',
            null,
            'Where any goods or consignment of goods are seized and detained under paragraph (b) of subsection (3) '
                . 'from any manufacturer or trader as the case may be, such manufacturer or trader shall be guilty of '
                . 'an offence under this Act.'
        );

        $this->node(
            $act,
            $s61,
            'SUBSECTION',
            '(5)',
            null,
            'In a prosecution for an offence under this section, a certificate issued under the hand of an '
                . 'officer authorised by the Director-General in writing to analyse any goods for the purpose of '
                . 'this section, stating the ingredients or the composition of the goods in relation to which the '
                . 'offence is alleged to have been committed, shall be admissible in evidence and shall be prima '
                . 'facie proof of the statements made therein.'
        );

        $this->node(
            $act,
            $s61,
            'SUBSECTION',
            '(6)',
            null,
            'A prosecution for an offence under this section shall not be instituted except by or with the '
                . 'written sanction of the Director-General.'
        );

        $this->node(
            $act,
            $s61,
            'SUBSECTION',
            '(7)',
            null,
            'For the purpose of this section, an "approved analyst" means a person who is qualified to perform '
                . 'such function and approved by the Director-General.'
        );

        // 62.
        $s62 = $this->section($act, $part, '62', null, 'General forfeiture.');
        $s62_1 = $this->node(
            $act,
            $s62,
            'SUBSECTION',
            '(1)',
            null,
            'Where in a conviction for an offence under subsection (4) of section 61 it is proved that the '
                . 'non-conformity with the standards and specifications has rendered the goods or consignment of '
                . 'goods unfit for any use or for consumption, then —'
        );
        $this->node(
            $act,
            $s62_1,
            'PARAGRAPH',
            '(a)',
            null,
            'if no appeal has been preferred to the High Court of the relevant Province established by Article '
                . '154P of the Constitution, against the relevant conviction within the time allowed therefor; or'
        );
        $this->node(
            $act,
            $s62_1,
            'PARAGRAPH',
            '(b)',
            null,
            'if an appeal has been preferred to the High Court of the relevant Province established by Article '
                . '154P of the Constitution, against the relevant conviction, and the conviction is affirmed, '
                . 'the court shall order that such goods or consignment of goods be forfeited to the State to be '
                . 'destroyed in such manner as the court may direct.'
        );

        $s62_2 = $this->node(
            $act,
            $s62,
            'SUBSECTION',
            '(2)',
            null,
            'Where in an action for an offence referred to in subsection (1), the accused is acquitted for the '
                . 'reason that it was not proved to the satisfaction of the court that the non-conformity with the '
                . 'standards and specifications has rendered the goods or consignment of goods unfit for any use or '
                . 'consumption, then —'
        );
        $this->node(
            $act,
            $s62_2,
            'PARAGRAPH',
            '(a)',
            null,
            'if no appeal has been preferred to the High Court of the relevant Province established by Article '
                . '154P of the Constitution, against the relevant acquittal within the time allowed therefor; or'
        );
        $this->node(
            $act,
            $s62_2,
            'PARAGRAPH',
            '(b)',
            null,
            'if an appeal has been preferred to the High Court of the relevant Province established by Article '
                . '154P of the Constitution, against the relevant acquittal and the acquittal is affirmed by the '
                . 'Court of Appeal, the court shall order the release of goods or consignment of goods seized.'
        );

        // 63.
        $this->section(
            $act,
            $part,
            '63',
            'Every court shall give priority to the disposal of any case filed under section 61 before such '
                . 'court, and to the hearing of any appeal from any conviction against any order imposed on such conviction.',
            'Priority to be given for cases under section 61.'
        );

        // 64.
        $s64 = $this->section(
            $act,
            $part,
            '64',
            'No civil or criminal proceedings shall be instituted—',
            'Protection for action taken under this Act.'
        );
        $this->node(
            $act,
            $s64,
            'PARAGRAPH',
            '(a)',
            null,
            'against the Authority for any act which in good faith is done or purported to be done by the Authority under this Act; or'
        );
        $this->node(
            $act,
            $s64,
            'PARAGRAPH',
            '(b)',
            null,
            'against any member, Director-General, Director, Deputy Director, Assistant Director, officer, '
                . 'servant or adviser of the Authority or any member of the Council, for any act which in good '
                . 'faith is done or purported to be done by him under this Act or on the directions of the Authority '
                . 'or the Council as the case may be.'
        );

        // 65.
        $s65 = $this->section($act, $part, '65', null, 'Expenses to be paid out of the Fund.');
        $this->node(
            $act,
            $s65,
            'SUBSECTION',
            '(1)',
            null,
            'Any expense incurred by the Authority in any suit or prosecution brought by or against the '
                . 'Authority before any court, shall be paid out of the Fund of the Authority, and any costs paid '
                . 'to or recovered by the Authority in any such suit or prosecution, shall be credited to the '
                . 'Fund of the Authority.'
        );
        $this->node(
            $act,
            $s65,
            'SUBSECTION',
            '(2)',
            null,
            'Any expenses incurred by any member, Director-General, Director, Deputy Director, Assistant Director, '
                . 'officer, servant or adviser of the Authority or any member of the Council in any suit or prosecution '
                . 'brought by or against such person before any court, in respect of any act which is done or '
                . 'purported to be done by such person under this Act or on the directions of the Authority or Council '
                . 'shall, if the court holds that such act was done in good faith, be paid out of the Fund of the '
                . 'Authority, unless such expenses are recovered by him in such suit or prosecution.'
        );

        // 66.
        $s66 = $this->section(
            $act,
            $part,
            '66',
            'All members of the Authority and the Council, Director-General, Directors, Deputy Directors, '
                . 'Assistant Directors, officers, servants and advisers of the Authority shall be deemed to be —',
            'Members of the Authority and the Council and officers and servants of the Authority deemed to be public servants.'
        );
        $this->node($act, $s66, 'PARAGRAPH', '(a)', null, 'public servants within the meaning of, and for the purposes of the Penal Code; and');
        $this->node(
            $act,
            $s66,
            'PARAGRAPH',
            '(b)',
            null,
            'public servant within the meaning of section 136 of the Code of Criminal Procedure Act, No. 15 '
                . 'of 1979 for the purpose of instituting proceedings in respect of offences under this Act.'
        );

        // 67.
        $this->section(
            $act,
            $part,
            '67',
            'The Authority and the Council shall be deemed to be Scheduled Institutions within the meaning of '
                . 'the Bribery Act and the provisions of that Act shall be construed accordingly.',
            'The Authority and the council deemed to be Scheduled Institutions within the meaning of the Bribery Act.'
        );

        // 68.
        $this->section(
            $act,
            $part,
            '68',
            'Where any offence under this Act is committed by an agent or servant of any manufacturer or trader, '
                . 'such offence shall be deemed to have been committed by such manufacturer or trader, unless he '
                . 'proves that such offence was committed without his knowledge.',
            'Principal liable for offences of agents and servants.'
        );

        // 69.
        $s69 = $this->section(
            $act,
            $part,
            '69',
            'Where any person is convicted of any offence under this Act or where any goods are seized and '
                . 'ordered to be forfeited under this Act, the Authority may pay —',
            'Rewards to certain persons.'
        );
        $this->node(
            $act,
            $s69,
            'PARAGRAPH',
            '(a)',
            null,
            'to the person who provided the information leading to such conviction or the seizure and forfeiture of such goods; and'
        );
        $this->node(
            $act,
            $s69,
            'PARAGRAPH',
            '(b)',
            null,
            'to any person who was concerned in the detection of such offence or effecting the seizure and forfeiture of such goods,'
        );
        $this->node($act, $s69, 'PARAGRAPH', null, null, 'such amount as it may deem reasonable, as a reward.');

        // 70.
        $this->section(
            $act,
            $part,
            '70',
            'Notwithstanding anything to the contrary in the Code of Criminal Procedure Act, No. 15 of 1979, '
                . 'every offence under this Act, shall be deemed to be a cognizable offence within the meaning and '
                . 'for the purposes of that Code.',
            'Offences to be cognizable offences.'
        );

        // 71.
        $this->section(
            $act,
            $part,
            '71',
            'The Director-General, Directors, Deputy Directors and Assistant Directors of the Authority shall '
                . 'be deemed to be peace officers within the meaning of the Code of Criminal Procedure Act, No. 15 '
                . 'of 1979 for the purpose of exercising any of the powers conferred upon peace officers by that Code.',
            'Certain officers to be peace officers.'
        );

        // 72.
        $s72 = $this->section($act, $part, '72', null, 'Regulations.');
        $this->node(
            $act,
            $s72,
            'SUBSECTION',
            '(1)',
            null,
            'The Minister may make regulations in respect of any matter required by this Act to be prescribed '
                . 'or in respect of which regulations are authorised by this Act to be made.'
        );
        $s72_2 = $this->node(
            $act,
            $s72,
            'SUBSECTION',
            '(2)',
            null,
            'In particular and without prejudice to the generality of the powers conferred by subsection (1), '
                . 'the Minister may make regulations in respect of all or any of the following matters:'
        );
        $this->node($act, $s72_2, 'PARAGRAPH', '(a)', null, 'prescribing the manner in which meetings of the Authority shall be convened;');
        $this->node($act, $s72_2, 'PARAGRAPH', '(b)', null, 'prescribing the manner of conduct of meetings of the Authority;');
        $this->node(
            $act,
            $s72_2,
            'PARAGRAPH',
            '(c)',
            null,
            'prescribing the persons by whom, and the manner in which, the expenses of witnesses notified to '
                . 'attend inquiries and investigations held by the Authority and the Council are to be paid.'
        );
        $this->node(
            $act,
            $s72,
            'SUBSECTION',
            '(3)',
            null,
            'Every regulation made by the Minister shall be published in the Gazette and shall come into '
                . 'operation on the date of such publication or on such later date as may be specified in such regulation.'
        );
        $this->node(
            $act,
            $s72,
            'SUBSECTION',
            '(4)',
            null,
            'Every regulation made by the Minister shall as soon as convenient after its publication in the '
                . 'Gazette, be brought before Parliament for approval. Any regulation which is not so approved shall '
                . 'be deemed to be rescinded as from the date of such disapproval, but without prejudice to anything '
                . 'previously done thereunder.'
        );
        $this->node(
            $act,
            $s72,
            'SUBSECTION',
            '(5)',
            null,
            'Notification of the date on which any regulation made by the Minister is deemed to be rescinded, '
                . 'shall be published in the Gazette.'
        );

        // 73.
        $s73 = $this->section(
            $act,
            $part,
            '73',
            null,
            'Repeal of Consumer Protection Act, No. 1 of 1979, Fair Trading Commission Act, No. 1 of 1987, Control of Prices Act, (Chapter 173) and savings.'
        );
        $s73_1 = $this->node($act, $s73, 'SUBSECTION', '(1)', null);
        $this->node($act, $s73_1, 'PARAGRAPH', '(a)', null, 'The Consumer Protection Act, No. 1 of 1979 is hereby repealed.');
        $s73_1b = $this->node($act, $s73_1, 'PARAGRAPH', '(b)', null, 'Notwithstanding the repeal of the aforesaid Act-');
        $s73_1b_i = $this->node($act, $s73_1b, 'SUBPARAGRAPH', '(i)', null);
        $this->node($act, $s73_1b_i, 'ITEM', '(aa)', null, 'every scheme of distribution published under section 3 of the repealed Act;');
        $this->node($act, $s73_1b_i, 'ITEM', '(bb)', null, 'every general direction issued by the Commissioner of Internal Trade under section 6 of the repealed Act;');
        $this->node($act, $s73_1b_i, 'ITEM', '(cc)', null, 'every standard and specification determined by the Commissioner of Internal Trade under section 8 of the repealed Act; and');
        $this->node(
            $act,
            $s73_1b_i,
            'ITEM',
            '(dd)',
            null,
            'every written agreement entered into by the Commissioner of Internal Trade with any manufacturer, '
                . 'trader or any association of manufacturers or traders under section 10 of the repealed Act, '
                . 'and in force on the day prior to the date of commencement of this Act, shall be deemed to be '
                . 'a scheme published, direction issued, standard and specification determined and written '
                . 'agreement entered into by the Authority;'
        );
        $this->node(
            $act,
            $s73_1b,
            'SUBPARAGRAPH',
            '(ii)',
            null,
            'all moneys lying to the credit of the Consumer Protection Fund established under section 33A of '
                . 'the repealed Act on the day immediately prior to the date of commencement of this Act, shall '
                . 'stand transferred with effect from that date, to the Fund established under this Act;'
        );
        $this->node(
            $act,
            $s73_1b,
            'SUBPARAGRAPH',
            '(iii)',
            null,
            'every regulations made by the Minister under section 34 of the repealed Act and in force on the '
                . 'date of commencement of this Act and which is not inconsistent with the provisions of this Act, '
                . 'shall be deemed to be regulations made under this Act, and may accordingly be amended, added '
                . 'to or rescinded, by regulations made under this Act;'
        );
        $this->node(
            $act,
            $s73_1b,
            'SUBPARAGRAPH',
            '(iv)',
            null,
            'all suits and prosecutions instituted under the repealed Act by or against the Attorney-General '
                . 'as representing the Department of Internal Trade in his official capacity, in any court or '
                . 'tribunal and pending on the day prior to the date of commencement of this Act, shall be deemed '
                . 'to be suits and prosecutions instituted by or against the Authority and may be continued accordingly;'
        );
        $this->node(
            $act,
            $s73_1b,
            'SUBPARAGRAPH',
            '(v)',
            null,
            'all decrees and orders entered or made by any competent court in favour of or against the '
                . 'Attorney-General as representing the Department of Internal Trade in his official capacity and '
                . 'remaining unsatisfied on the date of commencement of this Act, shall be deemed to have been '
                . 'entered or made in favour of or against the Authority and may be enforced accordingly; and'
        );
        $this->node(
            $act,
            $s73_1b,
            'SUBPARAGRAPH',
            '(vi)',
            null,
            'all debts, obligations, assets and liabilities incurred and all matters and things engaged to be '
                . 'done by with or on behalf of the Department of Internal Trade shall be deemed to have been '
                . 'incurred, and engaged to be done by, with or on behalf of the Authority.'
        );

        $s73_2 = $this->node($act, $s73, 'SUBSECTION', '(2)', null);
        $this->node($act, $s73_2, 'PARAGRAPH', '(a)', null, 'The Fair Trading Commission Act, No. 1 of 1987 is hereby repealed.');
        $s73_2b = $this->node($act, $s73_2, 'PARAGRAPH', '(b)', null, 'Notwithstanding the repeal of the aforesaid Act-');
        $this->node(
            $act,
            $s73_2b,
            'SUBPARAGRAPH',
            '(i)',
            null,
            'all moneys lying to the credit of the Fund established under section 30 of the repealed Act, on '
                . 'the day immediately prior to the date of commencement of this Act, shall be transferred to the '
                . 'Fund of the Authority established under this Act;'
        );
        $this->node(
            $act,
            $s73_2b,
            'SUBPARAGRAPH',
            '(ii)',
            null,
            'all inquiries and investigations pending before the Fair Trading Commission on the day prior to '
                . 'the date of commencement of this Act, shall be deemed to be inquiries and investigations pending '
                . 'before the Authority under the provisions of this Act, and may accordingly be continued and '
                . 'concluded before such Authority under the provisions of this Act;'
        );
        $this->node(
            $act,
            $s73_2b,
            'SUBPARAGRAPH',
            '(iii)',
            null,
            'all requests made under section 18 of the repealed Act and all questions referred to the Fair '
                . 'Trading Commission under the repealed Act and pending before such Commission, on the day prior '
                . 'to the date of commencement of this Act, shall be deemed to be requests made and questions '
                . 'referred to the Authority under this Act, and may accordingly be inquired into and examined by '
                . 'the Authority under the provisions of this Act;'
        );
        $this->node(
            $act,
            $s73_2b,
            'SUBPARAGRAPH',
            '(iv)',
            null,
            'all orders made and reports submitted by the Fair Trading Commission under the provisions of the '
                . 'repealed Act, shall be deemed to be orders made, and reports submitted by the Authority under '
                . 'the provisions of this Act;'
        );
        $this->node(
            $act,
            $s73_2b,
            'SUBPARAGRAPH',
            '(v)',
            null,
            'all suits and prosecutions instituted by or against the Fair Trading Commission in any court or '
                . 'tribunal and pending on the day prior to the date of commencement of this Act shall be deemed '
                . 'to be suits and prosecutions instituted by or against the Authority and may be continued accordingly;'
        );
        $this->node(
            $act,
            $s73_2b,
            'SUBPARAGRAPH',
            '(vi)',
            null,
            'all decrees and orders entered or made by any competent court in favour of or against the Fair '
                . 'Trading Commission and remaining unsatisfied on the date of commencement of this Act, shall be '
                . 'deemed to have been entered or made in favour of or against the Authority as the case may be, '
                . 'and may be enforced accordingly;'
        );
        $this->node(
            $act,
            $s73_2b,
            'SUBPARAGRAPH',
            '(vii)',
            null,
            'every regulation made by the Minister under the repealed Act and in force on the date of '
                . 'commencement of this Act and which is not inconsistent with the provisions of this Act, shall '
                . 'be deemed to be a regulation made under this Act and may accordingly be amended, added to, or '
                . 'rescinded by regulations made under this Act;'
        );
        $s73_2b_viii = $this->node(
            $act,
            $s73_2b,
            'SUBPARAGRAPH',
            '(viii)',
            null,
            'all officers and servants of the Commission on the day immediately preceding the date of commencement of this Act-'
        );
        $this->node(
            $act,
            $s73_2b_viii,
            'ITEM',
            '(a)',
            null,
            'who are offered employment with the Authority and accepts such offer, shall be employed therein '
                . 'on such terms and conditions as are not less favourable than the terms and conditions of '
                . 'employment that were applicable to them on the day immediately preceding the date of commencement '
                . 'of this Act; or'
        );
        $this->node(
            $act,
            $s73_2b_viii,
            'ITEM',
            '(b)',
            null,
            'who are not offered employment with the Authority or who are offered such employment and who do '
                . 'not accept such offer, shall be entitled to the payment of such compensation as may be '
                . 'determined by the Minister ; and'
        );
        $this->node(
            $act,
            $s73_2b,
            'SUBPARAGRAPH',
            '(ix)',
            null,
            'all debts, obligations, assets and liabilities incurred and all contracts and agreements executed '
                . 'or entered into and all matters and things engaged to be done by, with or for the Fair Trading '
                . 'Commission shall be deemed to have been incurred, executed, entered into and engaged to be done '
                . 'by, with or for the Authority.'
        );

        $this->node($act, $s73, 'SUBSECTION', '(3)', null, 'The Control of Prices Act, (Chapter 173) is hereby repealed.');

        // 74.
        $this->section(
            $act,
            $part,
            '74',
            'From and after the commencement of this Act, every reference to "Price Control Inspector" in '
                . 'any written law, shall be read and construed as a reference to the relevant officer of the '
                . 'Authority, assigned by the Authority to perform the functions relating to the regulation of '
                . 'the pricing of goods.',
            'Reference to "Price Control Inspector" to be a reference to relevant officer in the Authority.'
        );

        // 75.
        $s75 = $this->section($act, $part, '75', 'In this Act, unless the context otherwise requires—', 'Interpretation.');
        $this->node(
            $act,
            $s75,
            'PARAGRAPH',
            null,
            null,
            '"consumer" means any actual or potential user of any goods or services made available for a '
                . 'consideration by any trader or manufacturer ;'
        );
        $this->node(
            $act,
            $s75,
            'PARAGRAPH',
            null,
            null,
            '"Council" means the Consumer Affairs Council established by section 39 of this Act;'
        );
        $this->node(
            $act,
            $s75,
            'PARAGRAPH',
            null,
            null,
            '"fuel" includes all petroleum products in liquid or gaseous form, oil, other gases, coal or lubricants;'
        );
        $this->node(
            $act,
            $s75,
            'PARAGRAPH',
            null,
            null,
            '"goods" means any food, drink, pharmaceutical, fuel and all other merchandise ;'
        );
        $this->node(
            $act,
            $s75,
            'PARAGRAPH',
            null,
            null,
            '"local authority" means a Municipal Council, Urban Council, Pradeshiya Sabha and includes any '
                . 'authority created or established by or under any law to exercise, perform and discharge, powers, '
                . 'duties and functions corresponding to or similar to the powers, duties and functions exercised, '
                . 'performed and discharged by any such Council or Sabha ;'
        );
        $this->node(
            $act,
            $s75,
            'PARAGRAPH',
            null,
            null,
            '"price" means a charge of any description;'
        );
        $this->node(
            $act,
            $s75,
            'PARAGRAPH',
            null,
            null,
            '"Provincial Council" means a Provincial Council established under Chapter XVIIA of the Constitution;'
        );
        $s75_manuf = $this->node($act, $s75, 'PARAGRAPH', null, null, '"manufacturer" means any person who-');
        $this->node($act, $s75_manuf, 'SUBPARAGRAPH', '(a)', null, 'makes any article or any goods;');
        $this->node($act, $s75_manuf, 'SUBPARAGRAPH', '(b)', null, 'assembles or joins any article or any goods whether by chemical process or otherwise ; or');
        $this->node($act, $s75_manuf, 'SUBPARAGRAPH', '(c)', null, 'adapts for sale any article or any goods;');
        $s75_service = $this->node(
            $act,
            $s75,
            'PARAGRAPH',
            null,
            null,
            '"service" means service of any description which is made available to actual or potential users, and includes-'
        );
        $this->node($act, $s75_service, 'SUBPARAGRAPH', '(a)', null, 'banking, financing, insurance, shipping and entertainment;');
        $this->node($act, $s75_service, 'SUBPARAGRAPH', '(b)', null, 'the construction, production, manufacture, supply, storage, maintenance, repair, treatment, cleaning, processing or alteration of goods;');
        $this->node($act, $s75_service, 'SUBPARAGRAPH', '(c)', null, 'services in connection with the import, export or distribution of goods;');
        $this->node($act, $s75_service, 'SUBPARAGRAPH', '(d)', null, 'the transportation of goods and passengers;');
        $this->node($act, $s75_service, 'SUBPARAGRAPH', '(e)', null, 'the cleaning of buildings and building premises ;');
        $this->node($act, $s75_service, 'SUBPARAGRAPH', '(f)', null, 'the sale and supply of any utility services including electricity, water, gas and telecommunication;');
        $this->node($act, $s75_service, 'SUBPARAGRAPH', '(g)', null, 'the provision of information technology and communications;');
        $this->node($act, $s75_service, 'SUBPARAGRAPH', '(h)', null, 'professional services such as accounting, auditing, legal, medical and health, surveying, architecture and engineering.');
        $s75_trader = $this->node($act, $s75, 'PARAGRAPH', null, null, '"trader" means any person who-');
        $this->node($act, $s75_trader, 'SUBPARAGRAPH', '(a)', null, 'sells or supplies goods wholesale to other persons;');
        $this->node($act, $s75_trader, 'SUBPARAGRAPH', '(b)', null, 'sells or supplies goods at retail rates to consumers;');
        $this->node($act, $s75_trader, 'SUBPARAGRAPH', '(c)', null, 'imports goods for the purpose of sale or supply;');
        $this->node($act, $s75_trader, 'SUBPARAGRAPH', '(d)', null, 'provides services for a consideration.');

        // 76.
        $this->section(
            $act,
            $part,
            '76',
            'In the event of any inconsistency between Sinhala and Tamil texts of this Act, the Sinhala text shall prevail.',
            'Sinhala text to prevail in the event of any inconsistency.'
        );

        // 77.
        $this->section(
            $act,
            $part,
            '77',
            'Notwithstanding anything to the contrary contained in the preceding provisions of this Act, the '
                . 'provisions of this Act relating to anti-competitive practices, shall not apply to the supply of '
                . 'goods or services by any person who is supplying such goods or services under an agreement '
                . 'entered into with the government and which is in force on the date of commencement of this '
                . 'Act, during the period of its validity as specified in that agreement as on such date of commencement.',
            'Exemption of existing agreements.'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SCHEDULE
    // ─────────────────────────────────────────────────────────────────────────
    private function seedSchedule(Act $act): void
    {
        $schedule = $this->node($act, null, 'SCHEDULE', null, 'Section 3(4)', 'SCHEDULE');
        $this->sections['SCHEDULE'] = $schedule->node_id;

        // 1.
        $p1 = $this->node(
            $act,
            $schedule,
            'PARAGRAPH',
            '1',
            'Disqualification from appointment.',
            'A person shall be disqualified from being appointed or continuing as a member of the Authority-'
        );
        $this->node($act, $p1, 'SUBPARAGRAPH', '(a)', null, 'if he is, or becomes a Member of Parliament, any provincial council or any local authority;');
        $this->node($act, $p1, 'SUBPARAGRAPH', '(b)', null, 'if he is not, or ceases to be a citizen of Sri Lanka ;');
        $this->node($act, $p1, 'SUBPARAGRAPH', '(c)', null, 'if he is under any law in force in Sri Lanka or in any other country, found or declared to be of unsound mind;');
        $this->node($act, $p1, 'SUBPARAGRAPH', '(d)', null, 'if he is serving or has served, a sentence of imprisonment imposed by any court in Sri Lanka or any other country; or');
        $this->node($act, $p1, 'SUBPARAGRAPH', '(e)', null, 'if he had been removed from membership of the Authority for misconduct.');

        // 2.
        $this->node(
            $act,
            $schedule,
            'PARAGRAPH',
            '2',
            'Term of office of members.',
            'Every member of the Authority other than the chairman and the full-time members shall, unless he '
                . 'vacates office earlier by death, resignation or removal, hold office for a term of three years '
                . 'from the date of his appointment and unless he has been removed from office, be eligible for '
                . 'reappointment Provided that a member appointed in place of a member who had vacated office, by '
                . 'death, resignation or removal, shall hold office for the unexpired term of office of the member '
                . 'whom he succeeds.'
        );

        // 3.
        $p3 = $this->node($act, $schedule, 'PARAGRAPH', '3', 'Termination of appointment of members.');
        $this->node(
            $act,
            $p3,
            'SUBPARAGRAPH',
            '(1)',
            null,
            'The Minister may, by Order published in the Gazette, remove from office any member of the '
                . 'Authority, other than the chairman or a full-time member for misconduct or for physical or '
                . 'mental incapacity. A member in respect of whom an Order is made under this paragraph shall be '
                . 'deemed to have vacated office on the date of publication of the Order in the Gazette.'
        );
        $this->node(
            $act,
            $p3,
            'SUBPARAGRAPH',
            '(2)',
            null,
            'The removal of any member under sub-paragraph (1) shall not be called in question in any court.'
        );

        // 4.
        $this->node(
            $act,
            $schedule,
            'PARAGRAPH',
            '4',
            'Resignation of members.',
            'A member of the Authority other than the chairman or a full-time member may at any time resign '
                . 'from his office by letter to that effect addressed to the Minister and such resignation shall '
                . 'take effect upon it being accepted by the Minister in writing.'
        );

        // 5.
        $this->node(
            $act,
            $schedule,
            'PARAGRAPH',
            '5',
            'Vacation of office by death resignation or removal.',
            'In the event of the vacation of office, by death, resignation or removal, by any member of the '
                . 'Authority, the Minister may, having regard to the provisions of section 3 appoint another '
                . 'person to succeed such member.'
        );

        // 6.
        $this->node(
            $act,
            $schedule,
            'PARAGRAPH',
            '6',
            'Leave or absence of members.',
            'Where a member of the Authority, other than the chairman or a full-time member is by reason of '
                . 'illness, infirmity or absence from Sri Lanka for a period not less than three months, is '
                . 'temporarily unable to perform his duties, it shall be the duty of such member to inform the '
                . 'Minister in writing of such inability. Thereupon, the Minister may having regard to the '
                . 'provisions of section 3, appoint some other person to act in his place during such period.'
        );

        // 7.
        $p7 = $this->node($act, $schedule, 'PARAGRAPH', '7', 'Termination of appointment of Chairman.');
        $this->node(
            $act,
            $p7,
            'SUBPARAGRAPH',
            '(1)',
            null,
            'If the Chairman or any one of the full-time members are by reason of illness, infirmity or '
                . 'absence from Sri Lanka temporarily unable to perform the duties of his office, the Minister '
                . 'may appoint another member of the Authority to act in his place.'
        );
        $this->node(
            $act,
            $p7,
            'SUBPARAGRAPH',
            '(2)',
            null,
            'The Minister may by Order published in the Gazette remove the Chairman or any one of the full-time '
                . 'members of the Authority from office for misconduct or for physical or mental incapacity. Such '
                . 'person in respect of whom an Order is made under this paragraph shall be deemed to have vacated '
                . 'office on the date of publication of the Order in the Gazette.'
        );
        $this->node(
            $act,
            $p7,
            'SUBPARAGRAPH',
            '(3)',
            null,
            'The Chairman or any one of the full-time members may at any time resign from the office of Chairman '
                . 'or full-time members as the case may be by letter addressed to the Minister. Such resignation '
                . 'shall take effect upon it being accepted by the Minister in writing.'
        );

        // 8.
        $p8 = $this->node($act, $schedule, 'PARAGRAPH', '8', 'Meetings of the Authority.');
        $this->node(
            $act,
            $p8,
            'SUBPARAGRAPH',
            '(1)',
            null,
            'The Chairman of the Authority shall, if present preside at every meeting of the Authority. In '
                . 'the absence of the Chairman from any such meeting, one of the full-time members present shall '
                . 'preside, and in the absence of, the Chairman and all the full-time members from any meeting '
                . 'of the Authority, the members present at such meeting shall elect one of the members present, '
                . 'to preside at such meeting.'
        );
        $this->node($act, $p8, 'SUBPARAGRAPH', '(2)', null, 'The quorum for any meeting of the Authority shall be four members.');
        $this->node(
            $act,
            $p8,
            'SUBPARAGRAPH',
            '(3)',
            null,
            'The Chairman, or in his absence the member presiding at any meeting of the Authority shall have, '
                . 'in addition to his own vote, a casting vote.'
        );
        $this->node(
            $act,
            $p8,
            'SUBPARAGRAPH',
            '(4)',
            null,
            'Subject to the provisions of this paragraph and any regulation made under section 71 the Authority '
                . 'may regulate the procedure in regard to its meetings and the transaction of business at such meetings.'
        );

        // 9.
        $this->node(
            $act,
            $schedule,
            'PARAGRAPH',
            '9',
            'Proceedings act or decision not to deemed invalid by reason of any defect in the appointment.',
            'No act, decision or proceeding of the Authority shall be deemed invalid by reason only of the '
                . 'existence of a vacancy among its members or any defect in the appointment of any member thereof.'
        );

        // 10.
        $p10 = $this->node($act, $schedule, 'PARAGRAPH', '10', 'Seal of the Authority.');
        $this->node($act, $p10, 'SUBPARAGRAPH', '(1)', null, 'The seal of the Authority shall be in the custody of the Chairman.');
        $this->node(
            $act,
            $p10,
            'SUBPARAGRAPH',
            '(2)',
            null,
            'The application of the seal of the Authority shall be authenticated by the signature of the '
                . 'Chairman or some other member of the Authority authorised to authenticate the application of such seal.'
                . 'Chairman or some other member of the Authority authorised to authenticate the application of such seal.'
        );

        // 11.
        $this->node(
            $act,
            $schedule,
            'PARAGRAPH',
            '11',
            'Disclosure of interest by members.',
            'A member of the Authority who is directly or indirectly interested in any matter which forms the '
                . 'subject matter of any proceeding of the Authority or of any decision proposed to be made by the '
                . 'Authority, shall disclose the nature of his interest at a meeting of the Authority and such '
                . 'member shall not take part in any proceeding or decision in respect of such matter.'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────
    private function section(Act $act, ?LegalNode $parent, string $number, ?string $text, ?string $marginalNote = null): LegalNode
    {
        return $this->node($act, $parent, 'SECTION', $number, $marginalNote, $text);
    }

    private function node(Act $act, ?LegalNode $parent, string $type, ?string $number, ?string $marginalNote = null, ?string $text = null): LegalNode
    {
        $parentId = $parent?->node_id;

        // Calculate dynamic level based on parent node
        $level = $parent ? ($parent->level + 1) : 1;

        if (!isset($this->sortCounters[$parentId ?? 0])) {
            $this->sortCounters[$parentId ?? 0] = 0;
        }
        $this->sortCounters[$parentId ?? 0]++;

        $node = LegalNode::create([
            'act_id'        => $act->act_id,
            'parent_node_id'     => $parentId,
            'level'         => $level, // Assigned to DB model creation
            'node_type'     => $type,
            'node_no'   => $number,
            'marginal_note' => $marginalNote,
            'text_content'  => $text,
            'sort_order'    => $this->sortCounters[$parentId ?? 0],
        ]);

        if ($type === 'SECTION' && $number !== null) {
            $this->sections[$number] = $node->node_id;
        }

        return $node;
    }

    private function seedCrossReferences(): void
    {
        // Each tuple: [source_section_key, target_section_key, reference_type, reference_text]
        // Keys match $this->sections: section numbers as strings, 'PREAMBLE', 'SCHEDULE'.
        $refs = [
            // Part I — Establishment
            ['2',        '3',        'refers_to',  's 2(1): "consist of the persons who are for the time being members of the Authority under section 3"'],
            ['3',        'SCHEDULE', 'refers_to',  's 3(4): "provisions of the Schedule to this Act shall have effect in relation to the term of office of the members"'],
            ['5',        '52',       'refers_to',  's 5: "Director-General of the Authority appointed under section 52 shall act as the Secretary"'],

            // Part II — Regulation of Trade
            ['9',        '14',       'refers_to',  's 9(c): "assist and encourage associations of traders to enter into agreements with the Authority for the purposes described in section 14"'],
            ['11',       '10',       'refers_to',  's 11: "sell any goods above the price marked on the goods in accordance with a direction issued under section 10"'],
            ['13',       '12',       'refers_to',  's 13(1)(a): "does not conform to the standards and specifications determined under section 12"'],
            ['15',       '10',       'refers_to',  's 15(2)(c): "contrary to any general or special direction issued to him by the Authority under section 10"'],
            ['20',       '19',       'refers_to',  's 20(1): "any reference is made to the Council by the Director-General under section 19"'],
            ['20',       '21',       'subject_to', 's 20(3): "subject to the provisions of section 21, cause its decision with its reasons to be published"'],
            ['21',       '20',       'refers_to',  's 21(1): "before making a recommendation under subsection (4) of section 20"'],
            ['22',       '19',       'refers_to',  's 22(2): "having regard to the provisions of section 19 should be investigated into by the Council"'],
            ['22',       '20',       'refers_to',  's 22(2): "the provisions of sections 20 and 21 shall, mutatis mutandis, apply to such investigation"'],
            ['22',       '21',       'refers_to',  's 22(2): "the provisions of sections 20 and 21 shall, mutatis mutandis, apply to such investigation"'],
            ['32',       '12',       'refers_to',  's 32(1)(c): "goods supplied or services provided will be in conformity with the standards and specifications determined under section 12"'],

            // Part III — Promotion of Competition
            ['35',       '34',       'refers_to',  's 35: "For the purpose of section 34, an anti-competitive practice shall be deemed to prevail"'],
            ['36',       '34',       'refers_to',  's 36(1): "persons interested in a matter which forms the subject of an investigation under section 34"'],
            ['36',       '33',       'refers_to',  's 36(2): "For the purpose of conducting an investigation under section 33" (verbatim Act text; cf. s 34)'],
            ['37',       '34',       'refers_to',  's 37(1): "Upon the conclusion of an investigation under section 34, the Authority may make an application to the Council"'],
            ['38',       '34',       'refers_to',  's 38: "where the Authority decides not to make an application to the Council in respect of investigation under section 34"'],
            ['38',       '37',       'refers_to',  's 38: "where the Authority decides not to make an application to the Council under section 37"'],

            // Part IV — Consumer Affairs Council
            ['41',       '37',       'refers_to',  's 41(1): "Where an application is made to the Council under section 37 or section 38"'],
            ['41',       '38',       'refers_to',  's 41(1): "Where an application is made to the Council under section 37 or section 38"'],
            ['42',       '41',       'refers_to',  's 42: "The Authority may cause to be published any order made by the Council under section 41"'],
            ['45',       '43',       'refers_to',  's 45: "If any person upon whom a notice is issued under section 43"'],
            ['46',       '45',       'refers_to',  's 46(1): "any act or omission in contravention of the provisions of section 45"'],

            // Part V — Fund
            ['49',       '59',       'refers_to',  's 49(2)(f): "all such sums of money as are credited or transferred to the Fund under section 59"'],
            ['49',       '73',       'refers_to',  's 49(2)(f): "all such sums of money as are credited or transferred to the Fund under section 59 or section 73"'],

            // Part VII — General
            ['57',       '56',       'refers_to',  's 57(2): "information contained in a return furnished under section 56"'],
            ['58',       '16',       'refers_to',  's 58(1)(b): "seize and detain any goods found in contravention of the provisions of section 16 or 17"'],
            ['58',       '17',       'refers_to',  's 58(1)(b): "seize and detain any goods found in contravention of the provisions of section 16 or 17"'],
            ['59',       '58',       'refers_to',  's 59(1): "goods seized and detained under paragraph (b) of subsection (1) of section 58"'],
            ['60',       '15',       'refers_to',  's 60(3): "guilty of an offence by reason of a contravention of the provisions of section 15 or section 16 or section 17"'],
            ['60',       '16',       'refers_to',  's 60(3): "guilty of an offence by reason of a contravention of the provisions of section 15 or section 16 or section 17"'],
            ['60',       '17',       'refers_to',  's 60(3): "guilty of an offence by reason of a contravention of the provisions of section 15 or section 16 or section 17"'],
            ['60',       '18',       'refers_to',  's 60(4): "guilty of an offence by reason of a contravention of the provisions of section 18"'],
            ['60',       '41',       'refers_to',  's 60(2)(a): "fails or refuses to comply with an order made under paragraph (b) of subsection (1) of section 41"'],
            ['60',       '56',       'refers_to',  's 60(1)(a)(i): "fails or refuses to furnish a return when required by the Authority to do so under section 56"'],
            ['60',       '57',       'refers_to',  's 60(1)(a)(ii): "fails or refuses to furnish any information when required to do so by a notice sent under section 57"'],
            ['60',       '58',       'refers_to',  's 60(1)(c): "obstructs any officer acting in the exercise of his powers under section 58"'],
            ['60',       '72',       'refers_to',  's 60(5): "any requirements imposed under any such provision, or any regulations made under section 72"'],
            ['61',       '12',       'refers_to',  's 61(1): "any goods that are being sold do not conform to the standards and specifications laid down under section 12"'],
            ['62',       '61',       'refers_to',  's 62(1): "conviction for an offence under subsection (4) of section 61"'],
            ['63',       '61',       'refers_to',  's 63: "every court shall give priority to the disposal of any case filed under section 61"'],
            ['75',       '39',       'refers_to',  's 75: "\'Council\' means the Consumer Affairs Council established by section 39 of this Act"'],

            // Schedule → Act sections
            ['SCHEDULE', '3',        'refers_to',  'Sch paras 5 & 6: "having regard to the provisions of section 3 appoint another person"'],
            ['SCHEDULE', '71',       'refers_to',  'Sch para 8(4): "any regulation made under section 71 the Authority may regulate the procedure" (verbatim Act text)'],
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
}
