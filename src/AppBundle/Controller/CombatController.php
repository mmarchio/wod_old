<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 9/14/18
 * Time: 3:21 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\actions;
use AppBundle\Entity\clan;
use AppBundle\Entity\combat_record;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\combat_character;
use AppBundle\Entity\character_traits;
use AppBundle\Entity\character_profile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Acl\Exception\Exception;
use AppBundle\Service\CommonUtils;

class CombatController extends Controller
{
    /**
     * @Route("/arena")
     */
    public function arenaAction(Request $request)
    {
    }

    /**
     * Route("/arena/random/{series}", defaults={"series":1})
     */
    public function arenaRandomAction(Request $request, $series)
    {
        $r = "both died";
        for ($i=0; $i<$series; $i++) {
            try {
                $match = $this->combat();
            } catch (\Exception $e) {
                continue;
            }
            if ($match) {
                if (!empty($match->win)) {
                    $r = $match->win_name. " won";
                    $em = $this->getDoctrine()->getManager();

                    $cr = new combat_record();
                    $cr->setWin($match->win);
                    $cr->setLose($match->lose);
                    $cr->setMatchId($match->matchId);

                    $em->persist($cr);
                    $em->flush();
                }
            }
        }

        return new Response($r);
    }

    /**
     * @Route("/arena/{p1}/{p2}")
     */
    public function arenaSpecificAction(Request $request, $p1, $p2)
    {
        $match = $this->combat($p1, $p2);
        $r = "both died";
        if ($match) {
            if (!empty($match->win)) {
                $r = $match->win_name. " won";
                $em = $this->getDoctrine()->getManager();

                $cr = new combat_record();
                $cr->setWin($match->win);
                $cr->setLose($match->lose);
                $cr->setMatchId($match->matchId);

                $em->persist($cr);
                $em->flush();
            }
        }

        return new Response($r);
    }

    /**
     * @Route("/arena/stats")
     */
    public function arenaStatsAction(Request $request)
    {
        $record = $this->getDoctrine()
            ->getRepository(combat_record::class)
            ->findAll();

        $clans = $this->getDoctrine()
            ->getRepository(clan::class)
            ->findAll();

        $clan_count = count($clans);
        $clan_names = [];
        for ($i=0; $i<$clan_count; $i++) {
            $clan_names[$clans[$i]->getId()] = $clans[$i]->getName();
        }

        $record_count = count($record);
        $data = new \stdClass();
        $data->wins = [];
        $data->losses = [];
        $data->clan_win = [];
        $data->clan_lose = [];
        $data->characters = [];
        $data->match_count = $record_count;
        for ($i=0; $i<$record_count; $i++) {
            $wcp = $this->getDoctrine()
                ->getRepository(character_profile::class)
                ->find($record[$i]->getWin());
            $lcp = $this->getDoctrine()
                ->getRepository(character_profile::class)
                ->find($record[$i]->getLose());
            $data->wins[] = $wcp;
            if (empty($data->characters[$wcp->getId()])) {
                $this->matchStats($data->characters[$wcp->getId()]);
            }
            if (empty($data->characters[$lcp->getId()])) {
                $this->matchStats($data->characters[$lcp->getId()]);
            }
            if (empty($data->clan_win[$clan_names[$wcp->getClan()]])) {
                $data->clan_win[$clan_names[$wcp->getClan()]] = new \stdClass();
                $data->clan_win[$clan_names[$wcp->getClan()]]->wins = 1;
                $data->clan_win[$clan_names[$wcp->getClan()]]->percentage = number_format($data->clan_win[$clan_names[$wcp->getClan()]]->wins / $record_count * 100,2);
            } else {
                $data->clan_win[$clan_names[$wcp->getClan()]]->wins++;
                $data->clan_win[$clan_names[$wcp->getClan()]]->percentage = number_format($data->clan_win[$clan_names[$wcp->getClan()]]->wins / $record_count * 100,2);
            }
            $data->characters[$wcp->getId()]->win++;

            $data->losses[] = $lcp;
            if (empty($data->clan_lose[$clan_names[$lcp->getClan()]])) {
                $data->clan_lose[$clan_names[$lcp->getClan()]] = new \stdClass();
                $data->clan_lose[$clan_names[$lcp->getClan()]]->losses = 1;
                $data->clan_lose[$clan_names[$lcp->getClan()]]->percentage = number_format($data->clan_lose[$clan_names[$lcp->getClan()]]->losses / $record_count * 100,2);
            } else {
                $data->clan_lose[$clan_names[$lcp->getClan()]]->losses++;
                $data->clan_lose[$clan_names[$lcp->getClan()]]->percentage = number_format($data->clan_lose[$clan_names[$lcp->getClan()]]->losses / $record_count * 100,2);
            }

            $data->characters[$lcp->getId()]->lose++;
            $actions = $this->getDoctrine()
                ->getRepository(actions::class)
                ->findBy(["matchId" => $record[$i]->getMatchId()]);
            $this->analyzeMatch($actions, $data);
        }
        return new Response();
    }

    protected function matchStats(&$c)
    {
        $c = new \stdClass();
        $c->total_turns = 0;
        $c->average_turns = 0;
        $c->highest_damage = 0;
        $c->lowest_damage = 100;
        $c->total_damage = 0;
        $c->average_damage = 0;
        $c->highest_soak = 0;
        $c->lowest_soak = 100;
        $c->average_soak = 0;
        $c->total_soak = 0;
        $c->botches = 0;
        $c->rolls = 0;
        $c->failures = 0;
        $c->successes = 0;
        $c->win = 0;
        $c->lose = 0;
        $c->total_matches = 1;
        $c->hits = 0;
    }

    protected function analyzeMatch($match, $data)
    {
        $c = count($match);
        $c1 = null;
        $c2 = null;
        $c1_match = false;
        $c2_match = false;
        $match_id = null;
        for ($i=0; $i<$c; $i++) {
            $action = unserialize($match[$i]->getAction());
            if (!empty($action[0])) {
                if (!$match_id) {
                    $match_id = $match[$i]->getMatchId();
                }
                if (empty($match_id)) {
                }
                if (empty($c1) || empty($c2)) {
                    $c1 = $action[0][0]["id"];
                    $c2 = $action[0][1]["id"];
                }
                if ($c1_match === false) {
                    $data->characters[$c1]->total_matches++;
                    $c1_match = true;
                }
                if ($c2_match === false) {
                    $data->characters[$c2]->total_matches++;
                    $c2_match = true;
                }

                $this->getTotalTurns($data->characters[$c1]);
                $this->getTotalTurns($data->characters[$c2]);
                $this->getHighestDamage($data->characters[$c1], $action, 0);
                $this->getHighestDamage($data->characters[$c2], $action, 1);
                $this->getLowestDamage($data->characters[$c1], $action, 0);
                $this->getLowestDamage($data->characters[$c2], $action, 1);
                $this->getTotalDamage($data->characters[$c1], $action, 0);
                $this->getTotalDamage($data->characters[$c2], $action, 1);
                $this->getHighestSoak($data->characters[$c1], $action, 0);
                $this->getHighestSoak($data->characters[$c2], $action, 1);
                $this->getLowestSoak($data->characters[$c1], $action, 0);
                $this->getLowestSoak($data->characters[$c2], $action, 1);
                $this->getTotalSoak($data->characters[$c1], $action, 0);
                $this->getTotalSoak($data->characters[$c2], $action, 1);
                $this->getBotches($data->characters[$c1], $action, 0);
                $this->getBotches($data->characters[$c2], $action, 1);
                $this->getRolls($data->characters[$c1], $action, 0);
                $this->getRolls($data->characters[$c2], $action, 1);
                $this->getFailures($data->characters[$c1], $action, 0);
                $this->getFailures($data->characters[$c2], $action, 1);
                $this->getSuccesses($data->characters[$c1], $action, 0);
                $this->getSuccesses($data->characters[$c2], $action, 1);
                $this->getHits($data->characters[$c1], $action, 0);
                $this->getHits($data->characters[$c2], $action, 1);
            }
        }
        $this->getAverageDamage($data->characters[$c1]);
        $this->getAverageDamage($data->characters[$c2]);
        $this->getAverageSoak($data->characters[$c1]);
        $this->getAverageSoak($data->characters[$c2]);
        $this->getAverageTurns($data->characters[$c1]);
        $this->getAverageTurns($data->characters[$c2]);

        $em = $this->getDoctrine()->getManager();
        $cr = $em->getRepository(combat_record::class)
            ->findBy(["matchId" => $match_id]);
        $cr[0]->setRecord(serialize($data));

        $em->flush();

        dump($data);
    }
    
    protected function getTotalTurns(&$cd)
    {
        $cd->total_turns++;
    }

    protected function getAverageTurns(&$cd)
    {
        $cd->average_turns = $cd->total_turns / $cd->total_matches;
    }

    protected function getHighestDamage(&$cd, $action, $i)
    {
        try {
            if ($action[0][$i]['action'][0]["dmg"]->result > $cd->highest_damage) {
                $cd->highest_damage = $action[0][$i]['action'][0]["dmg"]->result;
            }
        } catch (\Exception $e) {
            dump($e->getMessage());
            dump($action);
            dump($cd);
        }
    }
    
    protected function getLowestDamage(&$cd, $action, $i)
    {
        if ($action[0][$i]['action'][0]["dmg"]->result < $cd->lowest_damage) {
            $cd->lowest_damage = $action[0][$i]['action'][0]["dmg"]->result;
        }
    }
    
    protected function getTotalDamage(&$cd, $action, $i) {
        $cd->total_damage = $cd->total_damage + $action[0][$i]['action'][0]["dmg"]->result;
    }
    
    protected function getAverageDamage(&$cd) {
        $cd->average_damage = $cd->total_damage / $cd->total_turns;
    }

    protected function getHighestSoak(&$cd, $action, $i)
    {
        $o = $this->other($i);
        if (!empty($action[0][$o]['action'][0]["o_soak"]->result)) {
            if ($action[0][$o]['action'][0]["o_soak"]->result > $cd->highest_soak) {
                $cd->highest_soak = $action[0][$o]['action'][0]["o_soak"]->result;
            }
        }
    }

    protected function getLowestSoak(&$cd, $action, $i)
    {
        $o = $this->other($i);
        if (!empty($action[0][$o]['action'][0]["o_soak"]->result)) {
            if ($action[0][$o]['action'][0]["o_soak"]->result < $cd->lowest_soak) {
                $cd->lowest_soak = $action[0][$o]['action'][0]["o_soak"]->result;
            }
        }
    }

    protected function getTotalSoak(&$cd, $action, $i) {
        $o = $this->other($i);
        if (!empty($action[0][$o]['action'][0]["o_soak"]->result)) {
            $cd->total_soak = $cd->total_soak + $action[0][$o]['action'][0]["o_soak"]->result;
        }
    }

    protected function getAverageSoak(&$cd) {
        $cd->average_soak = $cd->total_soak / $cd->total_turns;
    }

    protected function getBotches(&$cd, $action, $i)
    {
        $o = $this->other($i);
        $totals = $action[0][$i]['action'][0];
        $cd->botches = $cd->botches + $totals["hit"]->botch + $totals['dmg']->botch;
        if (is_object($action[0][$o]['action'][0]['o_soak'])) {
            $cd->botches = $cd->botches + $action[0][$o]['action'][0]['o_soak']->botch;
        }
    }
    
    protected function getRolls(&$cd, $action, $i)
    {
        $o = $this->other($i);
        $totals = $action[0][$i]['action'][0];
        $cd->rolls = $cd->botches + count($totals["hit"]->rolls) + count($totals['dmg']->rolls);
        if (is_object($action[0][$o]['action'][0]['o_soak'])) {
            $cd->rolls = $cd->rolls + count($action[0][$o]['action'][0]['o_soak']->rolls);
        }
    }
    
    protected function getFailures(&$cd, $action, $i)
    {
        $o = $this->other($i);
        $totals = $action[0][$i]['action'][0];
        $cd->failures = $cd->failures + $totals["hit"]->failure + $totals['dmg']->failure;
        if (is_object($action[0][$o]['action'][0]['o_soak'])) {
            $cd->failures = $cd->failures + $action[0][$o]['action'][0]['o_soak']->failure;
        }
    }
    
    protected function getSuccesses(&$cd, $action, $i)
    {
        $o = $this->other($i);
        $totals = $action[0][$i]['action'][0];
        $cd->successes = $cd->successes + $totals["hit"]->success + $totals['dmg']->success;
        if (is_object($action[0][$o]['action'][0]['o_soak'])) {
            $cd->successes = $cd->successes + $action[0][$o]['action'][0]['o_soak']->success;
        }
    }
    
    protected function getHits(&$cd, $action, $i)
    {
        $cd->hits = $cd->hits + $totals = $action[0][$i]['action'][0]["hit"]->result;
    }

    protected function combat($p1 = null, $p2 = null)
    {
        $match_id = CommonUtils::gen_uuid();
        dump($match_id);

        $characters = new \stdClass();
        $characters->p1 = 1;
        $characters->p2 = 2;

        if ($p1 && $p2) {
            $characters->p1 = $p1;
            $characters->p2 = $p2;
        } else {
            $characters = $this->getDoctrine()
                ->getRepository(character_profile::class)
                ->findAll();

            $character_count = count($characters) - 1;
            $characters = $this->getRandomCharacters($character_count);
        }
        $characters->p1 = $this->getCombatCharacter($characters->p1);
        $characters->p2 = $this->getCombatCharacter($characters->p2);

        $turn = [];
        $end = false;
        while ($end === false) {
            $turn = $this->combatTurn($characters->p1, $characters->p2);
            dump($turn);
            $turn[] = $turn;
            $a = new actions();
            $a->setMatchId($match_id);
            $a->setAction(serialize($turn));
            $em = $this->getDoctrine()->getManager();
            $em->persist($a);
            $em->flush();
            if ($characters->p1->getHealth() < 1 || $characters->p2->getHealth() < 1) {
                $end = true;
            }
        }
        $characters->record = json_encode($turn);
        if ($characters->p1->getHealth() > 0 || $characters->p2->getHealth() > 0) {
            if ($characters->p1->getHealth() > 0) {
                $characters->win = $characters->p1->getId();
                $characters->win_name = $characters->p1->getName();
                $characters->lose = $characters->p2->getId();
                $characters->lose_name = $characters->p2->getName();
            } else {
                $characters->win = $characters->p2->getId();
                $characters->win_name = $characters->p2->getName();
                $characters->lose = $characters->p1->getId();
                $characters->lose_name = $characters->p1->getName();
            }
        }
        $characters->matchId = $match_id;
        return $characters;
    }

    protected function getRandomCharacters($character_count) {
        $p1 = rand(0, $character_count);
        $p2 = rand(0, $character_count);
        if ($p1 === $p2) {
            if ($p1 === $character_count) {
                $p2 = $character_count - 1;
            } elseif ($p1 === 0) {
                $p2 = $p1 + 1;
            } else {
                $p2 = $p1 + 1;
            }
        }
        $c = new \stdClass();
        $c->p1 = $p1;
        $c->p2 = $p2;

        return $c;
    }

    protected function getCombatCharacter($id)
    {
        $ct = $this->getDoctrine()
            ->getRepository(character_traits::class)
            ->findBy(["characterProfile" => $id]);
        $cp = $this->getDoctrine()
            ->getRepository(character_profile::class)
            ->find($id);
        $c = new combat_character();
        $c->setId($cp->getId());
        $c->setModifier(0);
        $c->setName($cp->getName());
        $c->setStrength($this->findTraitValue(1, $ct));
        $c->setDexterity($this->findTraitValue(2, $ct));
        $c->setStamina($this->findTraitValue(3, $ct));
        $c->setPerception($this->findTraitValue(7, $ct));
        $c->setBrawl($this->findTraitValue(13, $ct));
        $c->setDodge($this->findTraitValue(14, $ct));
        $c->setFirearms($this->findTraitValue(23, $ct));
        $c->setMelee($this->findTraitValue(24, $ct));
        $d = [
            40 => $this->findTraitValue(40, $ct) ?? 0,
            41 => $this->findTraitValue(41, $ct) ?? 0,
            42 => $this->findTraitValue(42, $ct) ?? 0,
            43 => $this->findTraitValue(43, $ct) ?? 0,
            44 => $this->findTraitValue(44, $ct) ?? 0,
            45 => $this->findTraitValue(45, $ct) ?? 0,
            46 => $this->findTraitValue(46, $ct) ?? 0,
            47 => $this->findTraitValue(47, $ct) ?? 0,
            48 => $this->findTraitValue(48, $ct) ?? 0,
            49 => $this->findTraitValue(49, $ct) ?? 0
        ];
        $c->setDisciplines($d);
        $c->setBrawlHitRoll($c->getDexterity() + $c->getBrawl());
        $c->setBrawlDmgRoll($c->getStrength() + $c->getDisciplines(45));
        $c->setMeleeHitRoll($c->getDexterity() + $c->getMelee());
        $c->setMeleeDmgRoll($c->getStrength() + $c->getDisciplines(45));
        $c->setFirearmsHitRoll($c->getPerception() + $c->getFirearms() + $c->getDisciplines(40));
        $c->setDodgeRoll($c->getDexterity() + $c->getDodgeRoll());
        $c->setInitRoll($c->getPerception() + $c->getBrawl() + $c->getDisciplines(45));
        $c->setTurns(1 + $c->getDisciplines(42));
        $c->setHealth(7);
        $c->setHealthModifier([
            7 => 0,
            6 => 1,
            5 => 1,
            4 => 2,
            3 => 2,
            2 => 5,
            1 => 7,
            0 => 20
        ]);
        $c->setSoakRoll($c->getStamina() + $c->getDisciplines(44));

        return $c;
    }

    protected function findTraitValue($id, $traits)
    {
        $c = count($traits);
        for ($i=0; $i<$c; $i++) {
            if ($traits[$i]->getTrait() == $id) {
                return $traits[$i]->getValue();
            }
        }
        return null;
    }

    protected function combatTurn(combat_character $c1, combat_character $c2)
    {
        $turn = [];
        $players = [
            $c1,
            $c2
        ];
        $init = CommonUtils::getInit($c1, $c2);
        $init_winner = $players[$init];
        $init_loser = $players[$this->other($init)];

        $p1 = [
            'name' => $init_winner->getName(),
            'id' => $init_winner->getId(),
            'action' => CommonUtils::combatAction($init_winner, $init_loser),
            'extra' => []
        ];
        if ($init_loser->getHealth() > 0) {
            $p2 = [
                'name' => $init_loser->getName(),
                'id' => $init_loser->getId(),
                'action' => CommonUtils::combatAction($init_loser, $init_winner),
                'extra' => []
            ];
        } else {
            return $turn;
        }
        if ($init_winner->getHealth() > 0) {
            $p1['extra'] = $this->extraTurns($init_winner, $init_loser);
        }
        if ($init_loser->getHealth() > 0) {
            $p2['extra'] = $this->extraTurns($init_loser, $init_winner);
        }
        if ($init_winner->getHealth() > 0) {
            CommonUtils::heal($init_winner);
        }
        if ($init_loser->getHealth() > 0) {
            CommonUtils::heal($init_loser);
        }
        $turn[] = [
            $p1,
            $p2
        ];
        return $turn;
    }

    protected function getHealthModifier($c)
    {
        if ($c->getHealth() >= 1) {
            return $c->getHealthModifier($c->getHealth());
        }
        return -20;
    }

    protected function extraTurns(combat_character $c, combat_character $o)
    {
        $action = [];
        if ($c->getDisciplines(42) > 0) {
            for ($i=0; $i<$c->getDisciplines(42); $i++) {
                $temp = [
                    'hit' => $this->roll(6, ($c->getBrawlHitRoll() - $this->getHealthModifier($c))),
                    'dmg' => $this->roll(6, ($c->getBrawlDmgRoll() - $this->getHealthModifier($c))),
                    'o_soak' => 0
                ];
                if ($temp['dmg']->result > 0 && $temp["hit"]->result > 1) {
                    $temp['o_soak'] = $this->roll(6, ($o->getSoakRoll() - $this->getHealthModifier($o)));
                    if ($temp['o_soak']->result < $temp["dmg"]->result) {
                        $dmg = $temp["dmg"]->result - $temp["o_soak"]->result;
                        $o->setHealth($o->getHealth() - $dmg);
                    }
                }
                $action[] = $temp;
            }
        }
        return $action;
    }

    protected function other($val)
    {
        switch ($val) {
            case 0:
                return 1;
                break;
            case 1:
                return 0;
                break;
        }
    }

    protected function roll($diff, $pool)
    {
        $r = new \stdClass();
        $r->rolls = [];
        $r->success = 0;
        $r->botch = 0;
        $r->failure = 0;
        if ($pool > 0) {
            for ($i=0; $i<$pool; $i++) {
                $roll = new \stdClass();
                $roll->status = "failure";
                $roll->result = rand(0,9);
                if ($roll->result === 0 || $roll->result >= $diff) {
                    $roll->status = "success";
                    $r->success++;
                } elseif ($roll->result === 1) {
                    $roll->status = "botch";
                    $r->botch++;
                }
                $r->rolls[] = $roll;
            }
            $r->result = $r->success - $r->botch;
            return $r;
        }
        $r->result = 0;
        return $r;
    }
}