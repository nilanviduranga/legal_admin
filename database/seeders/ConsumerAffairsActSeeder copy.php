<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Act;
use App\Models\CrossReference;
use App\Models\Gazette;
use App\Models\LegalNode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsumerAffairsActSeeder extends Seeder
{
    private array $nodeIndex = [];

    public function run(): void
    {
        DB::transaction(function () {
            $gazette = Gazette::firstOrCreate(
                ['gazette_no' => 'Supplement to Part II'],
                [
                    'gazette_date' => '2003-03-21',
                    'title' => 'Gazette of the Democratic Socialist Republic of Sri Lanka, March 21, 2003',
                    'status' => 'active',
                ]
            );

            $act = Act::firstOrCreate(
                ['act_no' => 9, 'act_year' => 2003],
                [
                    'gazette_id' => $gazette->gazette_id,
                    'title' => 'Consumer Affairs Authority Act',
                    'short_title' => 'Consumer Affairs Authority Act, No. 9 of 2003',
                    'commencement_date' => '2003-03-17',
                    'status' => 'active',
                    'current_version' => true,
                ]
            );

            // Collect existing node IDs, delete cross-references first, then nodes
            $nodeIds = LegalNode::withTrashed()->where('act_id', $act->act_id)->pluck('node_id');
            if ($nodeIds->isNotEmpty()) {
                CrossReference::whereIn('source_node_id', $nodeIds)
                    ->orWhereIn('target_node_id', $nodeIds)
                    ->delete();
                LegalNode::withTrashed()->where('act_id', $act->act_id)->forceDelete();
            }

            $this->nodeIndex = [];

            // ── PREAMBLE ──────────────────────────────────────────────────────────
            $preamble = $this->node($act, null, 'PREAMBLE', null, null,
                'AN ACT TO PROVIDE FOR THE ESTABLISHMENT OF THE CONSUMER AFFAIRS AUTHORITY; FOR THE PROMOTION OF EFFECTIVE COMPETITION AND THE PROTECTION OF CONSUMERS; FOR THE REGULATION OF INTERNAL TRADE; AND FOR MATTERS CONNECTED THEREWITH OR INCIDENTAL THERETO.',
                1, 1);
            $this->nodeIndex['PREAMBLE'] = $preamble->node_id;

            // ══════════════════════════════════════════════════════════════════════
            // PART I — Establishment of the Consumer Affairs Authority (ss. 1–8)
            // ══════════════════════════════════════════════════════════════════════
            $partI = $this->node($act, null, 'PART', 'I', 'Establishment of the Consumer Affairs Authority', null, 1, 2);

            // § 1 Short title
            $sec1 = $this->node($act, $partI, 'SECTION', '1', 'Short title', null, 2, 1);
            $this->node($act, $sec1, 'SUBSECTION', '(1)', null,
                'This Act may be cited as the Consumer Affairs Authority Act, No. 9 of 2003.',
                3, 1);
            $this->nodeIndex['1'] = $sec1->node_id;

            // § 2 Establishment
            $sec2 = $this->node($act, $partI, 'SECTION', '2', 'Establishment of the Consumer Affairs Authority', null, 2, 2);
            $s2_1 = $this->node($act, $sec2, 'SUBSECTION', '(1)', null,
                'There shall be established for the purposes of this Act, an Authority which shall be called the "Consumer Affairs Authority" (hereinafter referred to as "the Authority").',
                3, 1);
            $this->node($act, $sec2, 'SUBSECTION', '(2)', null,
                'The Authority shall, by the name assigned to it by subsection (1), be a body corporate with perpetual succession and a common seal and may sue and be sued in its corporate name.',
                3, 2);
            $this->nodeIndex['2'] = $sec2->node_id;
            $this->nodeIndex['2.(1)'] = $s2_1->node_id;

            // § 3 Constitution of the Authority
            $sec3 = $this->node($act, $partI, 'SECTION', '3', 'Constitution of the Authority', null, 2, 3);
            $s3_1 = $this->node($act, $sec3, 'SUBSECTION', '(1)', null,
                'The Authority shall consist of— (a) a Chairman; and (b) not less than ten and not more than fifteen other members, who shall be appointed by the Minister.',
                3, 1);
            $this->node($act, $sec3, 'SUBSECTION', '(2)', null,
                'In making appointments under subsection (1), the Minister shall have regard to the need for the Authority to include persons with knowledge of, or experience in, commerce, industry, consumer affairs, economics, law, accountancy or any other field relevant to the work of the Authority.',
                3, 2);
            $this->node($act, $sec3, 'SUBSECTION', '(3)', null,
                'The Minister shall designate one of the members of the Authority to be the Deputy Chairman of the Authority.',
                3, 3);
            $s3_4 = $this->node($act, $sec3, 'SUBSECTION', '(4)', null,
                'The provisions of the Schedule to this Act shall apply to and in relation to the members of the Authority.',
                3, 4);
            $this->nodeIndex['3'] = $sec3->node_id;
            $this->nodeIndex['3.(1)'] = $s3_1->node_id;
            $this->nodeIndex['3.(4)'] = $s3_4->node_id;

            // § 4 Director-General
            $sec4 = $this->node($act, $partI, 'SECTION', '4', 'Director-General of the Authority', null, 2, 4);
            $this->node($act, $sec4, 'SUBSECTION', '(1)', null,
                'There shall be a Director-General of the Authority who shall be appointed by the Minister.',
                3, 1);
            $this->node($act, $sec4, 'SUBSECTION', '(2)', null,
                'The Director-General shall be the chief executive officer of the Authority and shall be responsible for the implementation of the decisions of the Authority and for the management of its day-to-day affairs.',
                3, 2);
            $this->node($act, $sec4, 'SUBSECTION', '(3)', null,
                'The Director-General may attend meetings of the Authority and may address such meetings but shall not be entitled to vote thereat.',
                3, 3);
            $this->node($act, $sec4, 'SUBSECTION', '(4)', null,
                'The Director-General shall hold office for a period of three years and shall be eligible for re-appointment.',
                3, 4);
            $this->node($act, $sec4, 'SUBSECTION', '(5)', null,
                'The Director-General shall be paid such remuneration as the Minister may, with the concurrence of the Minister in charge of the subject of Finance, determine.',
                3, 5);
            $this->nodeIndex['4'] = $sec4->node_id;

            // § 5 Powers of Director-General
            $sec5 = $this->node($act, $partI, 'SECTION', '5', 'Powers of Director-General', null, 2, 5);
            $this->node($act, $sec5, 'SUBSECTION', '(1)', null,
                'Subject to the general or special directions of the Authority, the Director-General shall have power to— (a) investigate any complaint referred to the Director-General under this Act; (b) conduct inspections and inquiries under this Act; (c) issue any direction authorised by this Act.',
                3, 1);
            $this->node($act, $sec5, 'SUBSECTION', '(2)', null,
                'The Director-General may, subject to such conditions as he considers fit, delegate any of the powers conferred on him by or under this Act to an officer of the Authority.',
                3, 2);
            $this->nodeIndex['5'] = $sec5->node_id;

            // § 6 Acting Director-General
            $sec6 = $this->node($act, $partI, 'SECTION', '6', 'Acting Director-General', null, 2, 6);
            $this->node($act, $sec6, 'SUBSECTION', '(1)', null,
                'In the event of the absence or inability to act of the Director-General, the Minister may appoint any officer of the Authority to act as Director-General during the period of such absence or inability.',
                3, 1);
            $this->nodeIndex['6'] = $sec6->node_id;

            // § 7 Objects of the Authority
            $sec7 = $this->node($act, $partI, 'SECTION', '7', 'Objects of the Authority', null, 2, 7);
            $s7_1 = $this->node($act, $sec7, 'SUBSECTION', '(1)', null,
                'The objects of the Authority shall be—',
                3, 1);
            $this->node($act, $s7_1, 'PARAGRAPH', '(a)', null,
                'the regulation of internal trade in Sri Lanka;', 4, 1);
            $this->node($act, $s7_1, 'PARAGRAPH', '(b)', null,
                'the promotion of effective competition for the benefit of consumers and for the efficiency of the economy of Sri Lanka;', 4, 2);
            $this->node($act, $s7_1, 'PARAGRAPH', '(c)', null,
                'the protection of consumers from unfair trade practices;', 4, 3);
            $this->node($act, $s7_1, 'PARAGRAPH', '(d)', null,
                'the promotion of consumer education and the enhancement of consumer awareness.', 4, 4);
            $this->nodeIndex['7'] = $sec7->node_id;
            $this->nodeIndex['7.(1)'] = $s7_1->node_id;

            // § 8 Functions of the Authority
            $sec8 = $this->node($act, $partI, 'SECTION', '8', 'Functions of the Authority', null, 2, 8);
            $s8_1 = $this->node($act, $sec8, 'SUBSECTION', '(1)', null,
                'The functions of the Authority shall be—',
                3, 1);
            $functions = [
                '(a)' => 'to regulate, in accordance with the provisions of this Act, the supply of goods and provision of services in the internal trade of Sri Lanka;',
                '(b)' => 'to conduct surveys and studies on matters relating to the supply of goods and provision of services in the internal trade of Sri Lanka;',
                '(c)' => 'to inquire into complaints made by consumers in respect of any goods or services and to take appropriate action thereon;',
                '(d)' => 'to promote the maintenance of standards in the supply of goods and the provision of services;',
                '(e)' => 'to promote competition in the supply of goods and the provision of services;',
                '(f)' => 'to promote consumer education and consumer awareness;',
                '(g)' => 'to investigate anti-competitive practices in any trade;',
                '(h)' => 'to investigate practices relating to the sale of goods or the provision of services that are harmful to the interests of consumers;',
                '(i)' => 'to inquire into and take action on complaints received from traders in respect of anti-competitive practices by other traders;',
                '(j)' => 'to recommend to the Minister the adoption of such legislative or other measures as the Authority considers necessary for the purpose of protecting consumers or promoting competition;',
                '(k)' => 'to co-operate with any international body with objects similar to the objects of the Authority;',
                '(l)' => 'to make available to the public information relating to the prices, quality and characteristics of goods and services;',
                '(m)' => 'to keep under review the operation of this Act and to make recommendations to the Minister for amendments thereto;',
                '(n)' => 'to advise the Minister on all matters relating to consumer affairs, trade and competition policy;',
                '(o)' => 'to perform such other functions as are assigned to the Authority by or under this Act or any other written law.',
            ];
            $fnSort = 1;
            foreach ($functions as $para => $funcText) {
                $this->node($act, $s8_1, 'PARAGRAPH', $para, null, $funcText, 4, $fnSort++);
            }
            $this->nodeIndex['8'] = $sec8->node_id;
            $this->nodeIndex['8.(1)'] = $s8_1->node_id;

            // ══════════════════════════════════════════════════════════════════════
            // PART II — Regulation of Trade (ss. 9–33)
            // ══════════════════════════════════════════════════════════════════════
            $partII = $this->node($act, null, 'PART', 'II', 'Regulation of Trade', null, 1, 3);

            // § 9 Surveys and studies
            $sec9 = $this->node($act, $partII, 'SECTION', '9', 'Authority to conduct surveys and studies', null, 2, 9);
            $this->node($act, $sec9, 'SUBSECTION', '(1)', null,
                'The Authority may, from time to time, conduct surveys and studies on any matter relating to the supply of goods or the provision of services in the internal trade of Sri Lanka.',
                3, 1);
            $this->node($act, $sec9, 'SUBSECTION', '(2)', null,
                'The Authority shall publish the results of any survey or study conducted under subsection (1) in such manner as the Authority considers appropriate.',
                3, 2);
            $this->nodeIndex['9'] = $sec9->node_id;

            // § 10 Directions relating to prices and labelling
            $sec10 = $this->node($act, $partII, 'SECTION', '10', 'Authority to issue directions relating to prices and labelling of goods', null, 2, 10);
            $s10_1 = $this->node($act, $sec10, 'SUBSECTION', '(1)', null,
                'The Authority may, in the prescribed manner, by direction in writing, require any trader to—',
                3, 1);
            $this->node($act, $s10_1, 'PARAGRAPH', '(a)', null,
                'display on any goods sold by him or kept by him for sale, or on any label, tag or ticket attached to such goods, the price at which such goods are offered for sale;', 4, 1);
            $this->node($act, $s10_1, 'PARAGRAPH', '(b)', null,
                'indicate in such manner as may be prescribed the maximum retail price of such goods on the package, wrapper or container in which such goods are packed or sold.', 4, 2);
            $this->node($act, $sec10, 'SUBSECTION', '(2)', null,
                'Any trader who fails to comply with any direction issued under subsection (1) shall be guilty of an offence under this Act.',
                3, 2);
            $this->node($act, $sec10, 'SUBSECTION', '(3)', null,
                'Every direction issued under subsection (1) shall be published in the Gazette.',
                3, 3);
            $this->nodeIndex['10'] = $sec10->node_id;

            // § 11 Selling above marked price
            $sec11 = $this->node($act, $partII, 'SECTION', '11', 'Selling above marked price to be an offence', null, 2, 11);
            $this->node($act, $sec11, 'SUBSECTION', '(1)', null,
                'It shall be an offence for any trader to sell any goods at a price exceeding the maximum retail price indicated on such goods or on the package, wrapper or container in which such goods are packed.',
                3, 1);
            $this->nodeIndex['11'] = $sec11->node_id;

            // § 12 Standards
            $sec12 = $this->node($act, $partII, 'SECTION', '12', 'Authority to issue directions relating to standards', null, 2, 12);
            $this->node($act, $sec12, 'SUBSECTION', '(1)', null,
                'The Authority may, by direction in writing, direct any trader to comply with such standards as may be specified in relation to— (a) the quality of any goods sold or offered for sale by such trader; (b) the provision of any service by such trader.',
                3, 1);
            $this->node($act, $sec12, 'SUBSECTION', '(2)', null,
                'Any trader who fails to comply with a direction issued under subsection (1) shall be guilty of an offence.',
                3, 2);
            $this->nodeIndex['12'] = $sec12->node_id;

            // § 13 Inquire into complaints
            $sec13 = $this->node($act, $partII, 'SECTION', '13', 'Authority to inquire into complaints', null, 2, 13);
            $s13_1 = $this->node($act, $sec13, 'SUBSECTION', '(1)', null,
                'The Authority may inquire into any complaint made by a consumer or a trader in respect of any goods sold or services provided by a trader.',
                3, 1);
            $this->node($act, $sec13, 'SUBSECTION', '(2)', null,
                'Upon receipt of a complaint under subsection (1), the Authority shall afford to the trader against whom the complaint is made a reasonable opportunity to be heard.',
                3, 2);
            $this->node($act, $sec13, 'SUBSECTION', '(3)', null,
                'After inquiry under this section the Authority may, if satisfied that the complaint is established— (a) order the trader to refund to the consumer any amount overcharged; (b) order the trader to pay the consumer such compensation as the Authority may determine; (c) order the trader to replace the goods supplied with goods of the same description and quality.',
                3, 3);
            $this->node($act, $sec13, 'SUBSECTION', '(4)', null,
                'Every order made by the Authority under subsection (3) shall be communicated in writing to the trader by registered post.',
                3, 4);
            $this->node($act, $sec13, 'SUBSECTION', '(5)', null,
                'A trader who fails to comply with any order made under subsection (3) shall be guilty of an offence.',
                3, 5);
            $this->nodeIndex['13'] = $sec13->node_id;
            $this->nodeIndex['13.(1)'] = $s13_1->node_id;

            // § 14 Agreements
            $sec14 = $this->node($act, $partII, 'SECTION', '14', 'Authority to inquire into agreements', null, 2, 14);
            $this->node($act, $sec14, 'SUBSECTION', '(1)', null,
                'The Authority may inquire into any agreement relating to any goods or services if the Authority has reason to believe that such agreement contains provisions which are detrimental to the interests of consumers or which tend to restrict competition.',
                3, 1);
            $this->node($act, $sec14, 'SUBSECTION', '(2)', null,
                'After inquiry under subsection (1) the Authority may, if satisfied that the agreement is detrimental to consumers or tends to restrict competition, make such orders as it considers appropriate for the purpose of eliminating those detrimental effects.',
                3, 2);
            $this->nodeIndex['14'] = $sec14->node_id;

            // § 15 Refusal to sell
            $sec15 = $this->node($act, $partII, 'SECTION', '15', 'Refusal to sell goods to be an offence', null, 2, 15);
            $this->node($act, $sec15, 'SUBSECTION', '(1)', null,
                'It shall be an offence for any trader to refuse to sell to any person any goods which he has in his stock for sale, unless such refusal is justified by reasonable commercial grounds.',
                3, 1);
            $this->node($act, $sec15, 'SUBSECTION', '(2)', null,
                'For the purposes of this section, "reasonable commercial grounds" includes the credit-worthiness of the customer or the fact that the goods are damaged or do not conform to the quality required.',
                3, 2);
            $this->nodeIndex['15'] = $sec15->node_id;

            // § 16 Denial of possession
            $sec16 = $this->node($act, $partII, 'SECTION', '16', 'Denial of possession of any goods', null, 2, 16);
            $this->node($act, $sec16, 'SUBSECTION', '(1)', null,
                'It shall be an offence for any trader to deny to any person that he has any particular goods in his stock, if he has such goods in his stock, with the intent of inducing such person to purchase other goods.',
                3, 1);
            $this->nodeIndex['16'] = $sec16->node_id;

            // § 17 Hoarding
            $sec17 = $this->node($act, $partII, 'SECTION', '17', 'Hoarding of goods by any trader or person to be an offence', null, 2, 17);
            $this->node($act, $sec17, 'SUBSECTION', '(1)', null,
                'It shall be an offence for any trader or other person to acquire and keep in his possession or under his control, in excess of normal stock levels, any goods with the object of making a profit from any increase in the price of such goods or of creating an artificial scarcity of such goods.',
                3, 1);
            $this->nodeIndex['17'] = $sec17->node_id;

            // § 18 Increase of price
            $sec18 = $this->node($act, $partII, 'SECTION', '18', 'Increase of price of goods', null, 2, 18);
            $this->node($act, $sec18, 'SUBSECTION', '(1)', null,
                'It shall be an offence for any trader to increase the price of any goods by more than such percentage as may be prescribed, within such period as may be prescribed, without obtaining the prior approval of the Authority.',
                3, 1);
            $this->node($act, $sec18, 'SUBSECTION', '(2)', null,
                'Any trader who wishes to increase the price of any goods by more than the prescribed percentage shall make an application in that behalf to the Authority.',
                3, 2);
            $this->nodeIndex['18'] = $sec18->node_id;

            // § 19 Director-General to refer matters to Council
            $sec19 = $this->node($act, $partII, 'SECTION', '19', 'Director-General to refer matters to the Council', null, 2, 19);
            $s19_1 = $this->node($act, $sec19, 'SUBSECTION', '(1)', null,
                'Where the Director-General is of the opinion that any goods or services are being supplied at prices which are unreasonably high having regard to the cost of production or provision thereof, the Director-General shall refer the matter to the Council established under Part IV for inquiry and report.',
                3, 1);
            $this->node($act, $sec19, 'SUBSECTION', '(2)', null,
                'Where the Director-General refers any matter to the Council under subsection (1), the Director-General shall furnish to the Council all information available to him relating to such matter.',
                3, 2);
            $this->nodeIndex['19'] = $sec19->node_id;
            $this->nodeIndex['19.(1)'] = $s19_1->node_id;

            // § 20 Council to investigate reference
            $sec20 = $this->node($act, $partII, 'SECTION', '20', 'Council to investigate reference', null, 2, 20);
            $s20_1 = $this->node($act, $sec20, 'SUBSECTION', '(1)', null,
                'The Council shall, upon a reference being made under section 19, investigate the matter referred to it and submit a report thereon to the Authority.',
                3, 1);
            $this->node($act, $sec20, 'SUBSECTION', '(2)', null,
                'In conducting an investigation under subsection (1), the Council shall afford to every person who is likely to be affected by any recommendation a reasonable opportunity to make representations.',
                3, 2);
            $s20_3 = $this->node($act, $sec20, 'SUBSECTION', '(3)', null,
                'After completing an investigation, the Council shall submit to the Authority a report containing its findings and any recommendation it considers appropriate.',
                3, 3);
            $this->nodeIndex['20'] = $sec20->node_id;
            $this->nodeIndex['20.(1)'] = $s20_1->node_id;
            $this->nodeIndex['20.(3)'] = $s20_3->node_id;

            // § 21 Acceptance of undertaking
            $sec21 = $this->node($act, $partII, 'SECTION', '21', 'Acceptance of undertaking', null, 2, 21);
            $this->node($act, $sec21, 'SUBSECTION', '(1)', null,
                'Where, in the course of an investigation under section 20, the Council is of the opinion that a trader is supplying goods at an unreasonably high price, the Council may accept from the trader an undertaking that the trader will, within a period specified by the Council, reduce the price of such goods to a level which the Council considers reasonable.',
                3, 1);
            $this->node($act, $sec21, 'SUBSECTION', '(2)', null,
                'Where the Council accepts an undertaking under subsection (1), the Council shall not make any recommendation against the trader in respect of the matter to which the undertaking relates, for as long as the trader complies with the undertaking.',
                3, 2);
            $this->nodeIndex['21'] = $sec21->node_id;

            // § 22 Public interest referrals by Minister
            $sec22 = $this->node($act, $partII, 'SECTION', '22', 'Public interest references by the Minister', null, 2, 22);
            $this->node($act, $sec22, 'SUBSECTION', '(1)', null,
                'The Minister may, in the public interest, refer to the Council for inquiry and report any matter relating to the supply of goods or the provision of services in the internal trade of Sri Lanka.',
                3, 1);
            $s22_2 = $this->node($act, $sec22, 'SUBSECTION', '(2)', null,
                'The provisions of sections 20 and 21 shall, with the necessary modifications, apply to and in relation to an investigation conducted by the Council pursuant to a reference made under subsection (1).',
                3, 2);
            $this->nodeIndex['22'] = $sec22->node_id;
            $this->nodeIndex['22.(2)'] = $s22_2->node_id;

            // § 23 Advisory references by Authority
            $sec23 = $this->node($act, $partII, 'SECTION', '23', 'Advisory references by the Authority', null, 2, 23);
            $this->node($act, $sec23, 'SUBSECTION', '(1)', null,
                'The Authority may refer to the Council for its opinion any question of law or fact which arises in the course of any inquiry conducted by the Authority under this Act.',
                3, 1);
            $this->node($act, $sec23, 'SUBSECTION', '(2)', null,
                'The opinion of the Council on any question referred to it under subsection (1) shall be communicated to the Authority in writing and shall be advisory only.',
                3, 2);
            $this->nodeIndex['23'] = $sec23->node_id;

            // § 24 Review of prices
            $sec24 = $this->node($act, $partII, 'SECTION', '24', 'Authority to review prices', null, 2, 24);
            $this->node($act, $sec24, 'SUBSECTION', '(1)', null,
                'The Authority shall, at least once in every six months, review the prices of such goods and services as may be prescribed for the purpose of determining whether such prices are fair and reasonable.',
                3, 1);
            $this->nodeIndex['24'] = $sec24->node_id;

            // § 25 Publication of price information
            $sec25 = $this->node($act, $partII, 'SECTION', '25', 'Publication of price information', null, 2, 25);
            $this->node($act, $sec25, 'SUBSECTION', '(1)', null,
                'The Authority shall publish, in such manner as it considers appropriate, information in respect of the prices of such goods and services as it considers should be made available to the public.',
                3, 1);
            $this->nodeIndex['25'] = $sec25->node_id;

            // § 26 Display of price lists
            $sec26 = $this->node($act, $partII, 'SECTION', '26', 'Display of price lists', null, 2, 26);
            $this->node($act, $sec26, 'SUBSECTION', '(1)', null,
                'The Authority may, by direction in writing, require any trader to display in a prominent place in or on his premises a list of the prices at which any specified goods are offered for sale.',
                3, 1);
            $this->node($act, $sec26, 'SUBSECTION', '(2)', null,
                'Any trader who fails to comply with any direction issued under subsection (1) shall be guilty of an offence.',
                3, 2);
            $this->nodeIndex['26'] = $sec26->node_id;

            // § 27 Registration of traders
            $sec27 = $this->node($act, $partII, 'SECTION', '27', 'Registration of traders', null, 2, 27);
            $this->node($act, $sec27, 'SUBSECTION', '(1)', null,
                'The Minister may make regulations prescribing classes of traders who shall be required to register with the Authority and specifying the conditions subject to which such registration shall be granted.',
                3, 1);
            $this->node($act, $sec27, 'SUBSECTION', '(2)', null,
                'Every trader who is required to register with the Authority under regulations made under subsection (1) shall apply for registration in the prescribed form and manner.',
                3, 2);
            $this->node($act, $sec27, 'SUBSECTION', '(3)', null,
                'A trader who carries on business without being registered as required by this section or any regulation made thereunder shall be guilty of an offence.',
                3, 3);
            $this->nodeIndex['27'] = $sec27->node_id;

            // § 28 Receipts
            $sec28 = $this->node($act, $partII, 'SECTION', '28', 'Obligation to issue receipts', null, 2, 28);
            $this->node($act, $sec28, 'SUBSECTION', '(1)', null,
                'Every trader shall, on request by a consumer, issue to such consumer a receipt in respect of any goods sold or services provided.',
                3, 1);
            $this->node($act, $sec28, 'SUBSECTION', '(2)', null,
                'Every receipt issued under subsection (1) shall specify the description and quantity of the goods or services and the total price paid.',
                3, 2);
            $this->node($act, $sec28, 'SUBSECTION', '(3)', null,
                'Any trader who fails to comply with subsection (1) shall be guilty of an offence.',
                3, 3);
            $this->nodeIndex['28'] = $sec28->node_id;

            // § 29 Notice board
            $sec29 = $this->node($act, $partII, 'SECTION', '29', 'Obligation to display notice board', null, 2, 29);
            $this->node($act, $sec29, 'SUBSECTION', '(1)', null,
                'Every trader shall display in a conspicuous place in his premises a notice board showing the name under which the trader carries on business, the address of his principal place of business, and such other information as may be prescribed.',
                3, 1);
            $this->node($act, $sec29, 'SUBSECTION', '(2)', null,
                'Any trader who fails to comply with subsection (1) shall be guilty of an offence.',
                3, 2);
            $this->nodeIndex['29'] = $sec29->node_id;

            // § 30 Misleading conduct
            $sec30 = $this->node($act, $partII, 'SECTION', '30', 'Misleading conduct', null, 2, 30);
            $this->node($act, $sec30, 'SUBSECTION', '(1)', null,
                'It shall be an offence for any trader to engage in conduct that is misleading or deceptive or is likely to mislead or deceive any consumer in relation to the supply of any goods or the provision of any service.',
                3, 1);
            $this->node($act, $sec30, 'SUBSECTION', '(2)', null,
                'For the purposes of this section, conduct that is misleading or deceptive includes conduct relating to— (a) the price of any goods or services; (b) the nature, characteristics, quantity, quality, grade or composition of any goods; (c) the terms and conditions upon which any goods are supplied or services are provided.',
                3, 2);
            $this->nodeIndex['30'] = $sec30->node_id;

            // § 31 False representations
            $sec31 = $this->node($act, $partII, 'SECTION', '31', 'False representations', null, 2, 31);
            $this->node($act, $sec31, 'SUBSECTION', '(1)', null,
                'It shall be an offence for any trader to make any false or misleading representation in connection with the supply of goods or the provision of a service.',
                3, 1);
            $this->node($act, $sec31, 'SUBSECTION', '(2)', null,
                'For the purposes of this section, "representation" includes any representation made orally, in writing, in any advertisement, or by the display of any goods.',
                3, 2);
            $this->nodeIndex['31'] = $sec31->node_id;

            // § 32 Warranties and guarantees
            $sec32 = $this->node($act, $partII, 'SECTION', '32', 'Obligations relating to warranties and guarantees', null, 2, 32);
            $this->node($act, $sec32, 'SUBSECTION', '(1)', null,
                'Every trader who supplies goods shall be responsible for ensuring that such goods comply with any warranty or guarantee given at the time of supply.',
                3, 1);
            $this->node($act, $sec32, 'SUBSECTION', '(2)', null,
                'Where any goods do not comply with such a warranty or guarantee, the consumer may— (a) require the trader to repair or replace such goods; or (b) require the trader to refund the price paid.',
                3, 2);
            $this->nodeIndex['32'] = $sec32->node_id;

            // § 33 Publication of reports on efficiency of traders
            $sec33 = $this->node($act, $partII, 'SECTION', '33', 'Publication of reports on efficiency of traders', null, 2, 33);
            $this->node($act, $sec33, 'SUBSECTION', '(1)', null,
                'The Authority may publish reports on the efficiency of traders in any sector of trade for the purpose of promoting competition and consumer awareness.',
                3, 1);
            $this->nodeIndex['33'] = $sec33->node_id;

            // ══════════════════════════════════════════════════════════════════════
            // PART III — Promotion of Competition and Consumer Interest (ss. 34–38)
            // ══════════════════════════════════════════════════════════════════════
            $partIII = $this->node($act, null, 'PART', 'III', 'Promotion of Competition and Consumer Interest', null, 1, 4);

            // § 34 Investigation of anti-competitive practices
            $sec34 = $this->node($act, $partIII, 'SECTION', '34', 'Investigation of anti-competitive practices', null, 2, 34);
            $s34_1 = $this->node($act, $sec34, 'SUBSECTION', '(1)', null,
                'The Authority may investigate any practice which the Authority has reason to believe is an anti-competitive practice.',
                3, 1);
            $this->node($act, $sec34, 'SUBSECTION', '(2)', null,
                'For the purpose of conducting an investigation under subsection (1), the Authority may— (a) require any trader to furnish such information as the Authority may specify; (b) require any trader to produce such documents as the Authority may specify; (c) examine any person on oath or affirmation.',
                3, 2);
            $this->node($act, $sec34, 'SUBSECTION', '(3)', null,
                'Any person who, without reasonable cause, fails to furnish information or produce any document required under subsection (2) shall be guilty of an offence.',
                3, 3);
            $this->nodeIndex['34'] = $sec34->node_id;
            $this->nodeIndex['34.(1)'] = $s34_1->node_id;

            // § 35 Exemptions
            $sec35 = $this->node($act, $partIII, 'SECTION', '35', 'Exemptions', null, 2, 35);
            $this->node($act, $sec35, 'SUBSECTION', '(1)', null,
                'Nothing in this Part shall apply to— (a) any agreement which is authorised or required by any other written law; (b) any agreement relating to the terms and conditions of employment.',
                3, 1);
            $this->node($act, $sec35, 'SUBSECTION', '(2)', null,
                'The Minister may, by Order published in the Gazette, exempt from the application of this Part any agreement or class of agreements if the Minister is satisfied that the public interest requires such exemption.',
                3, 2);
            $this->nodeIndex['35'] = $sec35->node_id;

            // § 36 Application to court
            $sec36 = $this->node($act, $partIII, 'SECTION', '36', 'Application to court', null, 2, 36);
            $s36_1 = $this->node($act, $sec36, 'SUBSECTION', '(1)', null,
                'Where, following an investigation under section 34, the Authority is of the opinion that an anti-competitive practice exists, the Authority may make an application to the Court of Appeal for an order restraining such practice.',
                3, 1);
            $this->node($act, $sec36, 'SUBSECTION', '(2)', null,
                'The Court of Appeal may, on an application made under subsection (1), make such order as it considers just and equitable for the purpose of restraining or preventing the continuance of the anti-competitive practice.',
                3, 2);
            $this->nodeIndex['36'] = $sec36->node_id;
            $this->nodeIndex['36.(1)'] = $s36_1->node_id;

            // § 37 Authority may decide not to apply to court
            $sec37 = $this->node($act, $partIII, 'SECTION', '37', 'Authority may decide not to apply to court', null, 2, 37);
            $s37_1 = $this->node($act, $sec37, 'SUBSECTION', '(1)', null,
                'Where, following an investigation under section 34, the Authority decides not to make an application under section 36, the Authority shall publish in the Gazette the reasons for such decision.',
                3, 1);
            $this->nodeIndex['37'] = $sec37->node_id;
            $this->nodeIndex['37.(1)'] = $s37_1->node_id;

            // § 38 Prohibited agreements
            $sec38 = $this->node($act, $partIII, 'SECTION', '38', 'Prohibited agreements', null, 2, 38);
            $s38_1 = $this->node($act, $sec38, 'SUBSECTION', '(1)', null,
                'Any agreement entered into between traders which has the object or effect of—',
                3, 1);
            $s38_1a = $this->node($act, $s38_1, 'PARAGRAPH', '(a)', null,
                'fixing the prices of goods or services;', 4, 1);
            $s38_1b = $this->node($act, $s38_1, 'PARAGRAPH', '(b)', null,
                'dividing markets for goods or services between traders;', 4, 2);
            $this->node($act, $s38_1, 'PARAGRAPH', '(c)', null,
                'limiting or controlling the production, supply or distribution of goods or services; or', 4, 3);
            $this->node($act, $s38_1, 'PARAGRAPH', '(d)', null,
                'applying dissimilar conditions to equivalent transactions with other trading parties, thereby placing them at a competitive disadvantage—', 4, 4);
            $this->node($act, $sec38, 'SUBSECTION', '(2)', null,
                'shall be null and void.',
                3, 2);
            $this->node($act, $sec38, 'SUBSECTION', '(3)', null,
                'Any trader who enters into or gives effect to any agreement referred to in subsection (1) shall be guilty of an offence.',
                3, 3);
            $this->nodeIndex['38'] = $sec38->node_id;
            $this->nodeIndex['38.(1)'] = $s38_1->node_id;
            $this->nodeIndex['38.(1).(a)'] = $s38_1a->node_id;
            $this->nodeIndex['38.(1).(b)'] = $s38_1b->node_id;

            // ══════════════════════════════════════════════════════════════════════
            // PART IV — Consumer Affairs Council (ss. 39–48)
            // ══════════════════════════════════════════════════════════════════════
            $partIV = $this->node($act, null, 'PART', 'IV', 'Consumer Affairs Council', null, 1, 5);

            // § 39 Establishment of Council
            $sec39 = $this->node($act, $partIV, 'SECTION', '39', 'Establishment and constitution of Consumer Affairs Council', null, 2, 39);
            $this->node($act, $sec39, 'SUBSECTION', '(1)', null,
                'There shall be established a Consumer Affairs Council (hereinafter referred to as "the Council") which shall consist of not less than five and not more than nine members appointed by the Minister.',
                3, 1);
            $this->node($act, $sec39, 'SUBSECTION', '(2)', null,
                'The members of the Council shall be appointed from among persons who have knowledge of, or experience in, commerce, industry, consumer affairs, economics, law, accountancy or any related field.',
                3, 2);
            $this->node($act, $sec39, 'SUBSECTION', '(3)', null,
                'Every member of the Council shall hold office for a period of three years from the date of his appointment.',
                3, 3);
            $this->node($act, $sec39, 'SUBSECTION', '(4)', null,
                'A member of the Council shall be paid such remuneration and allowances as the Minister, with the concurrence of the Minister in charge of the subject of Finance, may determine.',
                3, 4);
            $this->nodeIndex['39'] = $sec39->node_id;

            // § 40 Chairman of Council
            $sec40 = $this->node($act, $partIV, 'SECTION', '40', 'Chairman of the Council', null, 2, 40);
            $this->node($act, $sec40, 'SUBSECTION', '(1)', null,
                'The Minister shall designate one of the members of the Council to be the Chairman of the Council.',
                3, 1);
            $this->nodeIndex['40'] = $sec40->node_id;

            // § 41 Functions of Council
            $sec41 = $this->node($act, $partIV, 'SECTION', '41', 'Functions of the Council', null, 2, 41);
            $s41_1 = $this->node($act, $sec41, 'SUBSECTION', '(1)', null,
                'The functions of the Council shall be—',
                3, 1);
            $this->node($act, $s41_1, 'PARAGRAPH', '(a)', null,
                'to inquire into and report upon any matter referred to the Council by the Authority or the Minister under this Act;', 4, 1);
            $this->node($act, $s41_1, 'PARAGRAPH', '(b)', null,
                'to advise the Authority on matters relating to consumer affairs and competition;', 4, 2);
            $this->node($act, $s41_1, 'PARAGRAPH', '(c)', null,
                'to perform such other functions as are assigned to the Council by or under this Act.', 4, 3);
            $this->nodeIndex['41'] = $sec41->node_id;
            $this->nodeIndex['41.(1)'] = $s41_1->node_id;

            // § 42 Meetings of Council
            $sec42 = $this->node($act, $partIV, 'SECTION', '42', 'Meetings of the Council', null, 2, 42);
            $this->node($act, $sec42, 'SUBSECTION', '(1)', null,
                'The Council shall meet at such times and places as the Chairman of the Council may determine.',
                3, 1);
            $this->node($act, $sec42, 'SUBSECTION', '(2)', null,
                'The quorum for a meeting of the Council shall be three members.',
                3, 2);
            $this->node($act, $sec42, 'SUBSECTION', '(3)', null,
                'The Chairman of the Council shall preside at every meeting. In his absence, the members present shall elect one of their number to preside.',
                3, 3);
            $this->node($act, $sec42, 'SUBSECTION', '(4)', null,
                'All decisions of the Council shall be by a majority of the members present and voting. In the event of equality of votes, the person presiding shall have a casting vote in addition to his deliberative vote.',
                3, 4);
            $this->nodeIndex['42'] = $sec42->node_id;

            // § 43 Inquiry procedure
            $sec43 = $this->node($act, $partIV, 'SECTION', '43', 'Procedure of inquiry', null, 2, 43);
            $this->node($act, $sec43, 'SUBSECTION', '(1)', null,
                'When the Council conducts an inquiry under this Act, the Council shall give notice of the inquiry to such persons as appear to it to be affected by the outcome.',
                3, 1);
            $this->node($act, $sec43, 'SUBSECTION', '(2)', null,
                'The Council shall afford to every person who has been given notice under subsection (1) a reasonable opportunity to be heard.',
                3, 2);
            $this->nodeIndex['43'] = $sec43->node_id;

            // § 44 Powers of Council
            $sec44 = $this->node($act, $partIV, 'SECTION', '44', 'Powers of the Council', null, 2, 44);
            $this->node($act, $sec44, 'SUBSECTION', '(1)', null,
                'The Council shall, for the purpose of conducting any inquiry, have the same powers as are vested in a District Court for the purpose of— (a) compelling the attendance of witnesses; (b) examining witnesses on oath or affirmation; (c) compelling the production of documents.',
                3, 1);
            $this->nodeIndex['44'] = $sec44->node_id;

            // § 45 Report of Council
            $sec45 = $this->node($act, $partIV, 'SECTION', '45', 'Report of the Council', null, 2, 45);
            $this->node($act, $sec45, 'SUBSECTION', '(1)', null,
                'Upon completing an inquiry under this Act, the Council shall submit a written report of its findings and recommendations to the Authority or the Minister, as the case may be.',
                3, 1);
            $this->node($act, $sec45, 'SUBSECTION', '(2)', null,
                'Every report submitted under subsection (1) shall contain— (a) a statement of the matters referred to the Council; (b) the findings of the Council; (c) the recommendations of the Council.',
                3, 2);
            $this->nodeIndex['45'] = $sec45->node_id;

            // § 46 Action on report
            $sec46 = $this->node($act, $partIV, 'SECTION', '46', 'Action on report', null, 2, 46);
            $this->node($act, $sec46, 'SUBSECTION', '(1)', null,
                'The Authority shall, upon receipt of a report under section 45, consider the report and take such action as it deems necessary or appropriate.',
                3, 1);
            $this->node($act, $sec46, 'SUBSECTION', '(2)', null,
                'A summary of every report received by the Authority under section 45 and the action taken thereon shall be published in the Gazette.',
                3, 2);
            $this->nodeIndex['46'] = $sec46->node_id;

            // § 47 Delegation by Council
            $sec47 = $this->node($act, $partIV, 'SECTION', '47', 'Delegation of functions by Council', null, 2, 47);
            $this->node($act, $sec47, 'SUBSECTION', '(1)', null,
                'The Council may, by resolution, delegate to any member of the Council any of its functions, other than the function of submitting a report under section 45.',
                3, 1);
            $this->nodeIndex['47'] = $sec47->node_id;

            // § 48 Annual report of Council
            $sec48 = $this->node($act, $partIV, 'SECTION', '48', 'Annual report of Council', null, 2, 48);
            $this->node($act, $sec48, 'SUBSECTION', '(1)', null,
                'The Council shall, at the end of each year, submit to the Minister a report of its activities during that year.',
                3, 1);
            $this->nodeIndex['48'] = $sec48->node_id;

            // ══════════════════════════════════════════════════════════════════════
            // PART V — Fund of the Authority (ss. 49–51)
            // ══════════════════════════════════════════════════════════════════════
            $partV = $this->node($act, null, 'PART', 'V', 'Fund of the Authority', null, 1, 6);

            // § 49 Fund of the Authority
            $sec49 = $this->node($act, $partV, 'SECTION', '49', 'Fund of the Authority', null, 2, 49);
            $this->node($act, $sec49, 'SUBSECTION', '(1)', null,
                'There shall be established a fund called the "Consumer Affairs Authority Fund" (hereinafter referred to as "the Fund") for the purposes of the Authority.',
                3, 1);
            $this->node($act, $sec49, 'SUBSECTION', '(2)', null,
                'The Fund shall consist of— (a) all sums of money paid to the Authority by way of fees, charges or other payments under this Act; (b) all sums of money appropriated by Parliament for the purposes of the Authority; (c) all sums of money received by the Authority from any other source.',
                3, 2);
            $this->nodeIndex['49'] = $sec49->node_id;

            // § 50 Application of Fund
            $sec50 = $this->node($act, $partV, 'SECTION', '50', 'Application of the Fund', null, 2, 50);
            $this->node($act, $sec50, 'SUBSECTION', '(1)', null,
                'The Fund shall be applied for the following purposes— (a) the payment of remuneration and allowances of members of the Authority and its staff; (b) the payment of all expenses incurred by the Authority in the exercise of its powers and performance of its functions; (c) the payment of any other amounts required to be paid under this Act.',
                3, 1);
            $this->nodeIndex['50'] = $sec50->node_id;

            // § 51 Accounts and audit
            $sec51 = $this->node($act, $partV, 'SECTION', '51', 'Accounts and audit', null, 2, 51);
            $this->node($act, $sec51, 'SUBSECTION', '(1)', null,
                'The Authority shall cause to be kept proper accounts of all income and expenditure and shall cause such accounts to be audited annually by the Auditor-General.',
                3, 1);
            $this->node($act, $sec51, 'SUBSECTION', '(2)', null,
                'The audited accounts of the Authority, together with the report of the Auditor-General thereon, shall be submitted to the Minister who shall cause the same to be laid before Parliament.',
                3, 2);
            $this->nodeIndex['51'] = $sec51->node_id;

            // ══════════════════════════════════════════════════════════════════════
            // PART VI — Staff of the Authority (ss. 52–54)
            // ══════════════════════════════════════════════════════════════════════
            $partVI = $this->node($act, null, 'PART', 'VI', 'Staff of the Authority', null, 1, 7);

            // § 52 Officers and servants
            $sec52 = $this->node($act, $partVI, 'SECTION', '52', 'Officers and servants of the Authority', null, 2, 52);
            $this->node($act, $sec52, 'SUBSECTION', '(1)', null,
                'The Authority may appoint such officers and servants as may be necessary for the efficient performance of its functions.',
                3, 1);
            $this->node($act, $sec52, 'SUBSECTION', '(2)', null,
                'The terms and conditions of service of the officers and servants of the Authority shall be determined by the Authority.',
                3, 2);
            $this->nodeIndex['52'] = $sec52->node_id;

            // § 53 Consumer Affairs Inspectors
            $sec53 = $this->node($act, $partVI, 'SECTION', '53', 'Consumer Affairs Inspectors', null, 2, 53);
            $this->node($act, $sec53, 'SUBSECTION', '(1)', null,
                'The Authority may appoint such officers of the Authority as it considers fit to be Consumer Affairs Inspectors for the purposes of this Act.',
                3, 1);
            $this->node($act, $sec53, 'SUBSECTION', '(2)', null,
                'Every Consumer Affairs Inspector shall be furnished by the Authority with a certificate of appointment which shall be produced by such Inspector on demand.',
                3, 2);
            $this->nodeIndex['53'] = $sec53->node_id;

            // § 54 Public officers on secondment
            $sec54 = $this->node($act, $partVI, 'SECTION', '54', 'Public officers on secondment', null, 2, 54);
            $this->node($act, $sec54, 'SUBSECTION', '(1)', null,
                'The Authority may, with the approval of the Secretary to the Treasury, request any Ministry or Department to second any public officer to the service of the Authority for such period as may be agreed.',
                3, 1);
            $s54_2 = $this->node($act, $sec54, 'SUBSECTION', '(2)', null,
                'A public officer seconded to the service of the Authority shall, during the period of secondment, be subject to the direction and control of the Authority but shall remain a public officer for all other purposes.',
                3, 2);
            $this->nodeIndex['54'] = $sec54->node_id;
            $this->nodeIndex['54.(2)'] = $s54_2->node_id;

            // ══════════════════════════════════════════════════════════════════════
            // PART VII — General (ss. 55–77)
            // ══════════════════════════════════════════════════════════════════════
            $partVII = $this->node($act, null, 'PART', 'VII', 'General', null, 1, 8);

            // § 55 Warning notice before prosecution
            $sec55 = $this->node($act, $partVII, 'SECTION', '55', 'Warning notice before prosecution', null, 2, 55);
            $this->node($act, $sec55, 'SUBSECTION', '(1)', null,
                'No prosecution shall be instituted for an offence under this Act unless the Authority has first served on the person concerned a notice warning him that the practice complained of is an offence and requiring him to discontinue such practice.',
                3, 1);
            $this->node($act, $sec55, 'SUBSECTION', '(2)', null,
                'The provisions of subsection (1) shall not apply where the Director-General is of the opinion that the interests of consumers require that an immediate prosecution be commenced.',
                3, 2);
            $this->nodeIndex['55'] = $sec55->node_id;

            // § 56 Obligation to maintain records
            $sec56 = $this->node($act, $partVII, 'SECTION', '56', 'Obligation to maintain records', null, 2, 56);
            $this->node($act, $sec56, 'SUBSECTION', '(1)', null,
                'Every trader shall maintain such records and accounts as may be prescribed and shall retain such records and accounts for a period of not less than five years.',
                3, 1);
            $this->node($act, $sec56, 'SUBSECTION', '(2)', null,
                'Any trader who fails to comply with subsection (1) shall be guilty of an offence.',
                3, 2);
            $this->nodeIndex['56'] = $sec56->node_id;

            // § 57 Obligation to furnish information
            $sec57 = $this->node($act, $partVII, 'SECTION', '57', 'Obligation to furnish information', null, 2, 57);
            $this->node($act, $sec57, 'SUBSECTION', '(1)', null,
                'The Authority may require any trader to furnish, within such time and in such manner as the Authority may specify, such information relating to the business of the trader as the Authority considers necessary for the performance of its functions.',
                3, 1);
            $this->node($act, $sec57, 'SUBSECTION', '(2)', null,
                'Any trader who fails to comply with a requirement under subsection (1) or who furnishes information that is false or misleading shall be guilty of an offence.',
                3, 2);
            $this->nodeIndex['57'] = $sec57->node_id;

            // § 58 Powers of entry and inspection
            $sec58 = $this->node($act, $partVII, 'SECTION', '58', 'Powers of entry and inspection', null, 2, 58);
            $this->node($act, $sec58, 'SUBSECTION', '(1)', null,
                'Any Consumer Affairs Inspector may, at any reasonable time and on production of his certificate of appointment, enter and inspect any premises used or reasonably believed to be used by a trader for the purposes of his trade.',
                3, 1);
            $this->node($act, $sec58, 'SUBSECTION', '(2)', null,
                'A Consumer Affairs Inspector exercising the power of entry and inspection under subsection (1) may— (a) examine any goods found on such premises; (b) inspect and take copies of any record or document found thereon; (c) seize any goods, record or document which the Inspector has reason to believe is evidence of an offence under this Act.',
                3, 2);
            $this->node($act, $sec58, 'SUBSECTION', '(3)', null,
                'Any person who obstructs a Consumer Affairs Inspector in the exercise of his powers under this section shall be guilty of an offence.',
                3, 3);
            $this->nodeIndex['58'] = $sec58->node_id;

            // § 59 Disposal of perishable goods
            $sec59 = $this->node($act, $partVII, 'SECTION', '59', 'Disposal of perishable goods', null, 2, 59);
            $this->node($act, $sec59, 'SUBSECTION', '(1)', null,
                'Where any goods seized under this Act are of a perishable nature, the Authority may sell such goods and hold the proceeds of sale pending the determination of any proceedings relating to such goods.',
                3, 1);
            $this->nodeIndex['59'] = $sec59->node_id;

            // § 60 Offences and Penalties
            $sec60 = $this->node($act, $partVII, 'SECTION', '60', 'Offences and Penalties', null, 2, 60);
            $this->node($act, $sec60, 'SUBSECTION', '(1)', null,
                'Every person who commits any offence under this Act shall, on conviction after summary trial by a Magistrate, be liable to a fine not exceeding fifty thousand rupees or to imprisonment for a term not exceeding two years, or to both such fine and imprisonment.',
                3, 1);
            $s60_2 = $this->node($act, $sec60, 'SUBSECTION', '(2)', null,
                'Notwithstanding the provisions of subsection (1), every person who commits an offence under—',
                3, 2);
            $s60_2a = $this->node($act, $s60_2, 'PARAGRAPH', '(a)', null,
                'section 30 or section 31 of this Act shall, on conviction after summary trial by a Magistrate, be liable to a fine not exceeding two hundred and fifty thousand rupees or to imprisonment for a term not exceeding five years, or to both such fine and imprisonment;', 4, 1);
            $this->node($act, $s60_2, 'PARAGRAPH', '(b)', null,
                'section 38 of this Act shall, on conviction after summary trial by a Magistrate, be liable to a fine not exceeding five hundred thousand rupees or to imprisonment for a term not exceeding five years, or to both such fine and imprisonment.', 4, 2);
            $this->node($act, $sec60, 'SUBSECTION', '(3)', null,
                'Where an offence under this Act is committed by a body corporate, every director and officer of such body corporate who authorised, consented to or knowingly acquiesced in the commission of the offence shall be guilty of that offence and liable to the same punishment.',
                3, 3);
            $this->node($act, $sec60, 'SUBSECTION', '(4)', null,
                'Where an offence under this Act is committed by a firm, every partner of such firm shall be guilty of that offence and liable to the same punishment, unless such partner proves the offence was committed without his knowledge or consent.',
                3, 4);
            $this->nodeIndex['60'] = $sec60->node_id;
            $this->nodeIndex['60.(2)'] = $s60_2->node_id;
            $this->nodeIndex['60.(2).(a)'] = $s60_2a->node_id;

            // § 61 Taking of samples
            $sec61 = $this->node($act, $partVII, 'SECTION', '61', 'Taking of samples', null, 2, 61);
            $this->node($act, $sec61, 'SUBSECTION', '(1)', null,
                'A Consumer Affairs Inspector may, for the purpose of analysis, take samples of any goods offered or stored for sale.',
                3, 1);
            $this->node($act, $sec61, 'SUBSECTION', '(2)', null,
                'Where a sample is taken under subsection (1), the Consumer Affairs Inspector shall— (a) divide the sample into three parts; (b) seal and mark each part; (c) give one part to the trader, retain one part for analysis and retain one part for reference.',
                3, 2);
            $this->nodeIndex['61'] = $sec61->node_id;

            // § 62 Forfeiture
            $sec62 = $this->node($act, $partVII, 'SECTION', '62', 'Forfeiture', null, 2, 62);
            $this->node($act, $sec62, 'SUBSECTION', '(1)', null,
                'Where any person is convicted of an offence under this Act, the court may, in addition to any other punishment, order the forfeiture of any goods in respect of which the offence was committed.',
                3, 1);
            $this->node($act, $sec62, 'SUBSECTION', '(2)', null,
                'Goods ordered to be forfeited under subsection (1) shall be disposed of in such manner as the court may direct.',
                3, 2);
            $this->nodeIndex['62'] = $sec62->node_id;

            // § 63 Offences triable by Magistrate
            $sec63 = $this->node($act, $partVII, 'SECTION', '63', 'Offences triable by Magistrate', null, 2, 63);
            $this->node($act, $sec63, 'SUBSECTION', '(1)', null,
                'Notwithstanding any provision in the Code of Criminal Procedure Act, every offence under this Act shall be triable by a Magistrate\'s Court.',
                3, 1);
            $this->nodeIndex['63'] = $sec63->node_id;

            // § 64 Protection of action taken under Act
            $sec64 = $this->node($act, $partVII, 'SECTION', '64', 'Protection of action taken under Act', null, 2, 64);
            $this->node($act, $sec64, 'SUBSECTION', '(1)', null,
                'No action or other legal proceedings shall lie against the Authority, the Chairman, any member of the Authority, the Director-General, or any officer or servant of the Authority, for any act done in good faith in the exercise or purported exercise of any power or the performance of any function under this Act.',
                3, 1);
            $this->nodeIndex['64'] = $sec64->node_id;

            // § 65 Expenses
            $sec65 = $this->node($act, $partVII, 'SECTION', '65', 'Expenses', null, 2, 65);
            $this->node($act, $sec65, 'SUBSECTION', '(1)', null,
                'The expenses of the Authority shall be defrayed out of the Fund of the Authority.',
                3, 1);
            $this->nodeIndex['65'] = $sec65->node_id;

            // § 66 Members and officers deemed to be public servants
            $sec66 = $this->node($act, $partVII, 'SECTION', '66', 'Members and officers deemed to be public servants', null, 2, 66);
            $this->node($act, $sec66, 'SUBSECTION', '(1)', null,
                'Every member of the Authority, the Director-General, and every officer and servant of the Authority shall be deemed to be a public servant within the meaning of the Penal Code.',
                3, 1);
            $this->nodeIndex['66'] = $sec66->node_id;

            // § 67 Bribery Act to apply
            $sec67 = $this->node($act, $partVII, 'SECTION', '67', 'Bribery Act to apply', null, 2, 67);
            $this->node($act, $sec67, 'SUBSECTION', '(1)', null,
                'For the purposes of the Bribery Act, every member of the Authority, the Director-General, and every officer and servant of the Authority shall be deemed to be a public servant within the meaning of that Act.',
                3, 1);
            $this->nodeIndex['67'] = $sec67->node_id;

            // § 68 Application to agents
            $sec68 = $this->node($act, $partVII, 'SECTION', '68', 'Application to agents', null, 2, 68);
            $this->node($act, $sec68, 'SUBSECTION', '(1)', null,
                'The provisions of this Act shall apply to any agent acting on behalf of a trader as they apply to the trader himself.',
                3, 1);
            $this->nodeIndex['68'] = $sec68->node_id;

            // § 69 Rewards to informers
            $sec69 = $this->node($act, $partVII, 'SECTION', '69', 'Rewards to informers', null, 2, 69);
            $this->node($act, $sec69, 'SUBSECTION', '(1)', null,
                'The Authority may, in its discretion, pay to any person who furnishes information leading to the detection of an offence under this Act such reward as the Authority considers appropriate.',
                3, 1);
            $this->nodeIndex['69'] = $sec69->node_id;

            // § 70 Cognizable offences
            $sec70 = $this->node($act, $partVII, 'SECTION', '70', 'Cognizable offences', null, 2, 70);
            $this->node($act, $sec70, 'SUBSECTION', '(1)', null,
                'All offences under this Act shall be cognizable offences within the meaning of the Code of Criminal Procedure Act.',
                3, 1);
            $this->nodeIndex['70'] = $sec70->node_id;

            // § 71 Consumer Affairs Inspectors deemed peace officers
            $sec71 = $this->node($act, $partVII, 'SECTION', '71', 'Consumer Affairs Inspectors deemed to be peace officers', null, 2, 71);
            $this->node($act, $sec71, 'SUBSECTION', '(1)', null,
                'For the purposes of the Code of Criminal Procedure Act, every Consumer Affairs Inspector shall be deemed to be a peace officer.',
                3, 1);
            $this->nodeIndex['71'] = $sec71->node_id;

            // § 72 Regulations
            $sec72 = $this->node($act, $partVII, 'SECTION', '72', 'Regulations', null, 2, 72);
            $this->node($act, $sec72, 'SUBSECTION', '(1)', null,
                'The Minister may make regulations in respect of all matters which are required or authorised by this Act to be prescribed or which are necessary or expedient for giving effect to the provisions of this Act.',
                3, 1);
            $this->node($act, $sec72, 'SUBSECTION', '(2)', null,
                'Every regulation made under this section shall be published in the Gazette and shall come into force on the date of such publication or on such later date as may be specified in the regulation.',
                3, 2);
            $this->node($act, $sec72, 'SUBSECTION', '(3)', null,
                'Every regulation made under this section shall, as soon as convenient after its publication in the Gazette, be brought before Parliament for approval.',
                3, 3);
            $this->nodeIndex['72'] = $sec72->node_id;

            // § 73 Repeal
            $sec73 = $this->node($act, $partVII, 'SECTION', '73', 'Repeal', null, 2, 73);
            $s73_1 = $this->node($act, $sec73, 'SUBSECTION', '(1)', null,
                'The Consumer Protection Act, No. 1 of 1979 is hereby repealed.',
                3, 1);
            $s73_2 = $this->node($act, $sec73, 'SUBSECTION', '(2)', null,
                'The Fair Trading Commission Act, No. 1 of 1987 is hereby repealed.',
                3, 2);
            $s73_3 = $this->node($act, $sec73, 'SUBSECTION', '(3)', null,
                'The Control of Prices Act (Chapter 173) is hereby repealed.',
                3, 3);
            $this->nodeIndex['73'] = $sec73->node_id;
            $this->nodeIndex['73.(1)'] = $s73_1->node_id;
            $this->nodeIndex['73.(2)'] = $s73_2->node_id;
            $this->nodeIndex['73.(3)'] = $s73_3->node_id;

            // § 74 Reference to Price Control Inspector
            $sec74 = $this->node($act, $partVII, 'SECTION', '74', 'Reference to Price Control Inspector', null, 2, 74);
            $this->node($act, $sec74, 'SUBSECTION', '(1)', null,
                'Any reference to a Price Control Inspector in any written law shall, with effect from the commencement of this Act, be read and construed as a reference to a Consumer Affairs Inspector appointed under this Act.',
                3, 1);
            $this->nodeIndex['74'] = $sec74->node_id;

            // § 75 Interpretation
            $sec75 = $this->node($act, $partVII, 'SECTION', '75', 'Interpretation', null, 2, 75);
            $s75_1 = $this->node($act, $sec75, 'SUBSECTION', '(1)', null,
                'In this Act, unless the context otherwise requires— "anti-competitive practice" means any practice or agreement which prevents, restricts or distorts competition in trade; "Authority" means the Consumer Affairs Authority established under section 2; "Chairman" means the Chairman of the Authority appointed under section 3; "consumer" means a person who acquires goods or services from a trader for personal, domestic or household use or consumption; "Council" means the Consumer Affairs Council established under section 39; "Director-General" means the Director-General of the Authority appointed under section 4; "Fund" means the Consumer Affairs Authority Fund established under section 49; "goods" includes all personal chattels other than money and things in action; "internal trade" means trade in goods or services within Sri Lanka; "Inspector" means a Consumer Affairs Inspector appointed under section 53; "Minister" means the Minister to whom the subject of internal trade has been assigned; "services" includes any services provided in the course of a commercial activity; "trade" includes the business of buying, selling, supplying or distributing goods and the provision of services in the course of a commercial activity; "trader" means a person who, in the course of a business, supplies goods or provides services to consumers.',
                3, 1);
            $this->nodeIndex['75'] = $sec75->node_id;
            $this->nodeIndex['75.(1)'] = $s75_1->node_id;

            // § 76 Sinhala text to prevail
            $sec76 = $this->node($act, $partVII, 'SECTION', '76', 'Sinhala text to prevail in case of inconsistency', null, 2, 76);
            $this->node($act, $sec76, 'SUBSECTION', '(1)', null,
                'In the event of any inconsistency between the Sinhala and Tamil texts of this Act, the Sinhala text shall prevail.',
                3, 1);
            $this->nodeIndex['76'] = $sec76->node_id;

            // § 77 Savings for agreements existing at commencement
            $sec77 = $this->node($act, $partVII, 'SECTION', '77', 'Savings for agreements in existence at commencement', null, 2, 77);
            $s77_1 = $this->node($act, $sec77, 'SUBSECTION', '(1)', null,
                'Nothing in Part III of this Act shall apply to any agreement which was in existence on the date of commencement of this Act, for a period of one year from such date.',
                3, 1);
            $this->nodeIndex['77'] = $sec77->node_id;
            $this->nodeIndex['77.(1)'] = $s77_1->node_id;

            // ══════════════════════════════════════════════════════════════════════
            // SCHEDULE — Section 3(4) (11 paragraphs)
            // ══════════════════════════════════════════════════════════════════════
            $schedule = $this->node($act, null, 'SCHEDULE', null, 'SCHEDULE — Section 3(4)', null, 1, 9);

            // Para 1 — Disqualifications
            $sched1 = $this->node($act, $schedule, 'PARAGRAPH', '1', 'Disqualification', null, 2, 1);
            $this->node($act, $sched1, 'SUBPARAGRAPH', '(1)', null,
                'A person shall be disqualified from being appointed as, or from continuing to hold office as, a member of the Authority if such person—',
                3, 1);
            $this->node($act, $sched1, 'SUBPARAGRAPH', '(a)', null,
                'is or becomes a Member of Parliament;', 3, 2);
            $this->node($act, $sched1, 'SUBPARAGRAPH', '(b)', null,
                'is or becomes a member of a Provincial Council;', 3, 3);
            $this->node($act, $sched1, 'SUBPARAGRAPH', '(c)', null,
                'is adjudged or declared an insolvent;', 3, 4);
            $this->node($act, $sched1, 'SUBPARAGRAPH', '(d)', null,
                'is convicted of any offence involving moral turpitude;', 3, 5);
            $this->node($act, $sched1, 'SUBPARAGRAPH', '(e)', null,
                'is permanently incapacitated by physical or mental infirmity from the performance of his duties.', 3, 6);

            // Para 2 — Term of office
            $sched2 = $this->node($act, $schedule, 'PARAGRAPH', '2', 'Term of office', null, 2, 2);
            $this->node($act, $sched2, 'SUBPARAGRAPH', '(1)', null,
                'Every member of the Authority shall hold office for a period of three years from the date of appointment and shall be eligible for re-appointment.',
                3, 1);
            $this->node($act, $sched2, 'SUBPARAGRAPH', '(2)', null,
                'A member appointed to fill a casual vacancy shall hold office only for the unexpired portion of the term of the member in whose place he is appointed.',
                3, 2);

            // Para 3 — Removal from office
            $sched3 = $this->node($act, $schedule, 'PARAGRAPH', '3', 'Removal from office', null, 2, 3);
            $this->node($act, $sched3, 'SUBPARAGRAPH', '(1)', null,
                'The Minister may remove any member of the Authority from office if such member— (a) becomes subject to a disqualification specified in paragraph 1; (b) is absent from three consecutive meetings of the Authority without the permission of the Chairman; or (c) is guilty of misconduct in the discharge of his duties.',
                3, 1);

            // Para 4 — Resignation
            $sched4 = $this->node($act, $schedule, 'PARAGRAPH', '4', 'Resignation', null, 2, 4);
            $this->node($act, $sched4, 'SUBPARAGRAPH', '(1)', null,
                'A member of the Authority may resign from office by letter addressed to the Minister and such resignation shall take effect from the date on which the letter is received by the Minister.',
                3, 1);

            // Para 5 — Vacation of office
            $sched5 = $this->node($act, $schedule, 'PARAGRAPH', '5', 'Vacation of office', null, 2, 5);
            $this->node($act, $sched5, 'SUBPARAGRAPH', '(1)', null,
                'Where a member of the Authority vacates office before the expiry of his term of office, the Minister may appoint another person to fill the vacancy for the unexpired period of the term of the member in whose place he is appointed.',
                3, 1);

            // Para 6 — Leave of absence
            $sched6 = $this->node($act, $schedule, 'PARAGRAPH', '6', 'Leave of absence', null, 2, 6);
            $this->node($act, $sched6, 'SUBPARAGRAPH', '(1)', null,
                'The Minister may grant leave of absence to the Chairman and the Chairman may grant leave of absence to any other member of the Authority.',
                3, 1);

            // Para 7 — Termination of appointment of Chairman
            $sched7 = $this->node($act, $schedule, 'PARAGRAPH', '7', 'Termination of appointment of Chairman', null, 2, 7);
            $this->node($act, $sched7, 'SUBPARAGRAPH', '(1)', null,
                'The Minister may terminate the appointment of the Chairman if the Chairman— (a) is guilty of misconduct in the performance of his duties; or (b) becomes subject to any of the disqualifications specified in paragraph 1.',
                3, 1);

            // Para 8 — Meetings
            $sched8 = $this->node($act, $schedule, 'PARAGRAPH', '8', 'Meetings', null, 2, 8);
            $this->node($act, $sched8, 'SUBPARAGRAPH', '(1)', null,
                'The Authority shall meet at such times and places as the Chairman may determine.',
                3, 1);
            $this->node($act, $sched8, 'SUBPARAGRAPH', '(2)', null,
                'The quorum for a meeting of the Authority shall be six members.',
                3, 2);
            $this->node($act, $sched8, 'SUBPARAGRAPH', '(3)', null,
                'The Chairman shall preside at every meeting of the Authority. In the absence of the Chairman, the Deputy Chairman shall preside. In the absence of both, the members present shall elect one of their number to preside.',
                3, 3);
            $this->node($act, $sched8, 'SUBPARAGRAPH', '(4)', null,
                'The decisions of the Authority shall be by a majority of the members present and voting. In the event of equality of votes, the Chairman or presiding member shall have a casting vote in addition to his deliberative vote.',
                3, 4);

            // Para 9 — Validity of proceedings
            $sched9 = $this->node($act, $schedule, 'PARAGRAPH', '9', 'Validity of proceedings', null, 2, 9);
            $this->node($act, $sched9, 'SUBPARAGRAPH', '(1)', null,
                'No act or proceeding of the Authority shall be deemed to be invalid by reason of the existence of any vacancy in the Authority or any defect in the appointment of any member thereof.',
                3, 1);

            // Para 10 — Seal
            $sched10 = $this->node($act, $schedule, 'PARAGRAPH', '10', 'Seal of the Authority', null, 2, 10);
            $this->node($act, $sched10, 'SUBPARAGRAPH', '(1)', null,
                'The seal of the Authority shall be in such form as the Authority may determine and shall not be affixed to any instrument except in the presence of the Chairman or some other member authorised by the Authority to act in that behalf.',
                3, 1);

            // Para 11 — Disclosure of interest
            $sched11 = $this->node($act, $schedule, 'PARAGRAPH', '11', 'Disclosure of interest', null, 2, 11);
            $this->node($act, $sched11, 'SUBPARAGRAPH', '(1)', null,
                'A member of the Authority who is directly or indirectly interested in any matter coming before the Authority for determination shall disclose the nature of his interest at the meeting at which such matter is first considered.',
                3, 1);
            $this->node($act, $sched11, 'SUBPARAGRAPH', '(2)', null,
                'A member who makes a disclosure under subparagraph (1) shall not vote on or take part in any deliberation of the Authority relating to such matter.',
                3, 2);

            $this->nodeIndex['SCHEDULE'] = $schedule->node_id;

            // Cross-references
            $this->seedCrossReferences();
        });
    }

    private function node(Act $act, ?LegalNode $parent, string $type, ?string $no, ?string $heading, ?string $text, int $level, int $sort): LegalNode
    {
        $node = LegalNode::create([
            'act_id'         => $act->act_id,
            'parent_node_id' => $parent?->node_id,
            'node_type'      => $type,
            'node_no'        => $no,
            'heading'        => $heading,
            'text_content'   => $text,
            'sort_order'     => $sort,
            'level'          => $level,
            'has_children'   => false,
            'status'         => 'active',
            'version_no'     => 1,
        ]);

        if ($parent) {
            $parent->update(['has_children' => true]);
        }

        return $node;
    }

    private function ref(string $sourceKey, string $targetKey, string $type, string $text): void
    {
        $sourceId = $this->nodeIndex[$sourceKey] ?? null;
        $targetId = $this->nodeIndex[$targetKey] ?? null;

        if (!$sourceId || !$targetId || $sourceId === $targetId) {
            return;
        }

        CrossReference::firstOrCreate(
            ['source_node_id' => $sourceId, 'target_node_id' => $targetId],
            ['reference_type' => $type, 'reference_text' => $text]
        );
    }

    private function seedCrossReferences(): void
    {
        // Part I
        $this->ref('2.(1)', '3.(1)', 'refers_to', 'members of the Authority under section 3');
        $this->ref('3.(4)', 'SCHEDULE', 'refers_to', 'provisions of the Schedule to this Act');

        // Part II
        $this->ref('19.(1)', '39', 'refers_to', 'the Council established under Part IV');
        $this->ref('20.(1)', '19', 'refers_to', 'a reference being made under section 19');
        $this->ref('21', '20', 'refers_to', 'investigation under section 20');
        $this->ref('22.(2)', '20', 'refers_to', 'provisions of section 20 shall apply');
        $this->ref('22.(2)', '21', 'refers_to', 'provisions of section 21 shall apply');

        // Part III
        $this->ref('36.(1)', '34', 'refers_to', 'following an investigation under section 34');
        $this->ref('37.(1)', '34', 'refers_to', 'following an investigation under section 34');
        $this->ref('37.(1)', '36', 'refers_to', 'decides not to make an application under section 36');

        // Part IV
        $this->ref('46', '45', 'refers_to', 'report under section 45');
        $this->ref('47', '45', 'refers_to', 'function of submitting a report under section 45');

        // Part VII
        $this->ref('60.(2).(a)', '30', 'refers_to', 'offence under section 30');
        $this->ref('60.(2).(a)', '31', 'refers_to', 'offence under section 31');
        $this->ref('73.(1)', 'PREAMBLE', 'repeals', 'Consumer Protection Act, No. 1 of 1979 is hereby repealed');
        $this->ref('73.(2)', 'PREAMBLE', 'repeals', 'Fair Trading Commission Act, No. 1 of 1987 is hereby repealed');
        $this->ref('73.(3)', 'PREAMBLE', 'repeals', 'Control of Prices Act (Chapter 173) is hereby repealed');
        $this->ref('77.(1)', '35', 'saves', 'Nothing in Part III shall apply to agreements in existence at commencement');
        $this->ref('74', '53', 'refers_to', 'Consumer Affairs Inspector appointed under this Act');
    }
}
