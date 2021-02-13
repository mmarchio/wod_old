<?php
namespace AppBundle\Service;

use AppBundle\Entity\actions;
use AppBundle\Entity\clan;
use AppBundle\Entity\combat_record;
use AppBundle\Entity\combat_character;
use AppBundle\Entity\character_traits;
use AppBundle\Entity\character_profile;

class CombatUtils
{
    public static function matchStats(&$c)
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

    public static function analyzeMatch($match, $data, $em)
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

                self::getTotalTurns($data->characters[$c1]);
                self::getTotalTurns($data->characters[$c2]);
                self::getHighestDamage($data->characters[$c1], $action, 0);
                self::getHighestDamage($data->characters[$c2], $action, 1);
                self::getLowestDamage($data->characters[$c1], $action, 0);
                self::getLowestDamage($data->characters[$c2], $action, 1);
                self::getTotalDamage($data->characters[$c1], $action, 0);
                self::getTotalDamage($data->characters[$c2], $action, 1);
                self::getHighestSoak($data->characters[$c1], $action, 0);
                self::getHighestSoak($data->characters[$c2], $action, 1);
                self::getLowestSoak($data->characters[$c1], $action, 0);
                self::getLowestSoak($data->characters[$c2], $action, 1);
                self::getTotalSoak($data->characters[$c1], $action, 0);
                self::getTotalSoak($data->characters[$c2], $action, 1);
                self::getBotches($data->characters[$c1], $action, 0);
                self::getBotches($data->characters[$c2], $action, 1);
                self::getRolls($data->characters[$c1], $action, 0);
                self::getRolls($data->characters[$c2], $action, 1);
                self::getFailures($data->characters[$c1], $action, 0);
                self::getFailures($data->characters[$c2], $action, 1);
                self::getSuccesses($data->characters[$c1], $action, 0);
                self::getSuccesses($data->characters[$c2], $action, 1);
                self::getHits($data->characters[$c1], $action, 0);
                self::getHits($data->characters[$c2], $action, 1);
            }
        }
        self::getAverageDamage($data->characters[$c1]);
        self::getAverageDamage($data->characters[$c2]);
        self::getAverageSoak($data->characters[$c1]);
        self::getAverageSoak($data->characters[$c2]);
        self::getAverageTurns($data->characters[$c1]);
        self::getAverageTurns($data->characters[$c2]);

        $cr = $em->getRepository(combat_record::class)
            ->findBy(["matchId" => $match_id]);
        $cr[0]->setRecord(serialize($data));

        $em->flush();
    }
    
    public static function getTotalTurns(&$cd)
    {
        $cd->total_turns++;
    }

    public static function getAverageTurns(&$cd)
    {
        $cd->average_turns = $cd->total_turns / $cd->total_matches;
    }

    public static function getHighestDamage(&$cd, $action, $i)
    {
        try {
            if ($action[0][$i]['action'][0]["dmg"]->result > $cd->highest_damage) {
                $cd->highest_damage = $action[0][$i]['action'][0]["dmg"]->result;
            }
        } catch (\Exception $e) {

        }
    }
    
    public static function getLowestDamage(&$cd, $action, $i)
    {
        if ($action[0][$i]['action'][0]["dmg"]->result < $cd->lowest_damage) {
            $cd->lowest_damage = $action[0][$i]['action'][0]["dmg"]->result;
        }
    }
    
    public static function getTotalDamage(&$cd, $action, $i) {
        $cd->total_damage = $cd->total_damage + $action[0][$i]['action'][0]["dmg"]->result;
    }
    
    public static function getAverageDamage(&$cd) {
        $cd->average_damage = $cd->total_damage / $cd->total_turns;
    }

    public static function getHighestSoak(&$cd, $action, $i)
    {
        $o = self::other($i);
        if (!empty($action[0][$o]['action'][0]["o_soak"]->result)) {
            if ($action[0][$o]['action'][0]["o_soak"]->result > $cd->highest_soak) {
                $cd->highest_soak = $action[0][$o]['action'][0]["o_soak"]->result;
            }
        }
    }

    public static function getLowestSoak(&$cd, $action, $i)
    {
        $o = self::other($i);
        if (!empty($action[0][$o]['action'][0]["o_soak"]->result)) {
            if ($action[0][$o]['action'][0]["o_soak"]->result < $cd->lowest_soak) {
                $cd->lowest_soak = $action[0][$o]['action'][0]["o_soak"]->result;
            }
        }
    }

    public static function getTotalSoak(&$cd, $action, $i) {
        $o = self::other($i);
        if (!empty($action[0][$o]['action'][0]["o_soak"]->result)) {
            $cd->total_soak = $cd->total_soak + $action[0][$o]['action'][0]["o_soak"]->result;
        }
    }

    public static function getAverageSoak(&$cd) {
        $cd->average_soak = $cd->total_soak / $cd->total_turns;
    }

    public static function getBotches(&$cd, $action, $i)
    {
        $o = self::other($i);
        $totals = $action[0][$i]['action'][0];
        $cd->botches = $cd->botches + $totals["hit"]->botch + $totals['dmg']->botch;
        if (is_object($action[0][$o]['action'][0]['o_soak'])) {
            $cd->botches = $cd->botches + $action[0][$o]['action'][0]['o_soak']->botch;
        }
    }
    
    public static function getRolls(&$cd, $action, $i)
    {
        $o = self::other($i);
        $totals = $action[0][$i]['action'][0];
        $cd->rolls = $cd->botches + count($totals["hit"]->rolls) + count($totals['dmg']->rolls);
        if (is_object($action[0][$o]['action'][0]['o_soak'])) {
            $cd->rolls = $cd->rolls + count($action[0][$o]['action'][0]['o_soak']->rolls);
        }
    }
    
    public static function getFailures(&$cd, $action, $i)
    {
        $o = self::other($i);
        $totals = $action[0][$i]['action'][0];
        $cd->failures = $cd->failures + $totals["hit"]->failure + $totals['dmg']->failure;
        if (is_object($action[0][$o]['action'][0]['o_soak'])) {
            $cd->failures = $cd->failures + $action[0][$o]['action'][0]['o_soak']->failure;
        }
    }
    
    public static function getSuccesses(&$cd, $action, $i)
    {
        $o = self::other($i);
        $totals = $action[0][$i]['action'][0];
        $cd->successes = $cd->successes + $totals["hit"]->success + $totals['dmg']->success;
        if (is_object($action[0][$o]['action'][0]['o_soak'])) {
            $cd->successes = $cd->successes + $action[0][$o]['action'][0]['o_soak']->success;
        }
    }
    
    public static function getHits(&$cd, $action, $i)
    {
        $cd->hits = $cd->hits + $totals = $action[0][$i]['action'][0]["hit"]->result;
    }

    public static function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    public static function combat(
        $em, 
        $characterProfileRepository, 
        $characterTraitsRepository, 
        $p1 = null, 
        $p2 = null
    ) 
    {
        $match_id = self::gen_uuid();

        $characters = new \stdClass();
        $characters->p1 = 1;
        $characters->p2 = 2;

        if ($p1 && $p2) {
            $characters->p1 = $p1;
            $characters->p2 = $p2;
        } else {
            $character_count = count($characterProfileRepository->findAll()) - 1;
            $characters = self::getRandomCharacters($character_count);
        }
        $characters->p1 = self::getCombatCharacter(
            $characters->p1, 
            $characterProfileRepository, 
            $characterTraitsRepository
        );
        
        $characters->p2 = self::getCombatCharacter(
            $characters->p2, 
            $characterProfileRepository, 
            $characterTraitsRepository
        );

        $turn = [];
        $end = false;
        while ($end === false) {
            $turn = self::combatTurn($characters->p1, $characters->p2);
            $turn[] = $turn;
            $a = new actions();
            $a->setMatchId($match_id);
            $a->setAction(serialize($turn));
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

    public static function getRandomCharacters($character_count) {
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

    public static function getCombatCharacter(
        $id, 
        $characterTraitsRepository, 
        $characterProfileRepository
    )
    {
        $ct = $characterTraitsRepository->findBy(["characterProfile" => $id]);
        $cp = $characterProfileRepository->find($id);
        $c = new combat_character();
        $c->setId($cp->getId());
        $c->setModifier(0);
        $c->setName($cp->getName());
        $c->setStrength(self::findTraitValue(1, $ct));
        $c->setDexterity(self::findTraitValue(2, $ct));
        $c->setStamina(self::findTraitValue(3, $ct));
        $c->setPerception(self::findTraitValue(7, $ct));
        $c->setBrawl(self::findTraitValue(13, $ct));
        $c->setDodge(self::findTraitValue(14, $ct));
        $c->setFirearms(self::findTraitValue(23, $ct));
        $c->setMelee(self::findTraitValue(24, $ct));
        $d = [
            40 => self::findTraitValue(40, $ct) ?? 0,
            41 => self::findTraitValue(41, $ct) ?? 0,
            42 => self::findTraitValue(42, $ct) ?? 0,
            43 => self::findTraitValue(43, $ct) ?? 0,
            44 => self::findTraitValue(44, $ct) ?? 0,
            45 => self::findTraitValue(45, $ct) ?? 0,
            46 => self::findTraitValue(46, $ct) ?? 0,
            47 => self::findTraitValue(47, $ct) ?? 0,
            48 => self::findTraitValue(48, $ct) ?? 0,
            49 => self::findTraitValue(49, $ct) ?? 0
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

    public static function combatTurn(combat_character $c1, combat_character $c2)
    {
        $turn = [];
        $players = [
            $c1,
            $c2
        ];
        $init = self::getInit($c1, $c2);
        $init_winner = $players[$init];
        $init_loser = $players[self::other($init)];

        $p1 = [
            'name' => $init_winner->getName(),
            'id' => $init_winner->getId(),
            'action' => self::combatAction($init_winner, $init_loser),
            'extra' => []
        ];
        if ($init_loser->getHealth() > 0) {
            $p2 = [
                'name' => $init_loser->getName(),
                'id' => $init_loser->getId(),
                'action' => self::combatAction($init_loser, $init_winner),
                'extra' => []
            ];
        } else {
            return $turn;
        }
        if ($init_winner->getHealth() > 0) {
            $p1['extra'] = self::extraTurns($init_winner, $init_loser);
        }
        if ($init_loser->getHealth() > 0) {
            $p2['extra'] = self::extraTurns($init_loser, $init_winner);
        }
        if ($init_winner->getHealth() > 0) {
            self::heal($init_winner);
        }
        if ($init_loser->getHealth() > 0) {
            self::heal($init_loser);
        }
        $turn[] = [
            $p1,
            $p2
        ];
        return $turn;
    }

    public static function getHealthModifier($c)
    {
        if ($c->getHealth() >= 1) {
            return $c->getHealthModifier($c->getHealth());
        }
        return -20;
    }

    public static function combatAction(combat_character $c, combat_character $o)
    {
        $action = [];
        $temp = [
            'hit' => self::roll(6, ($c->getBrawlHitRoll() - self::getHealthModifier($c))),
            'dmg' => self::roll(6, ($c->getBrawlDmgRoll() - self::getHealthModifier($c))),
            'o_soak' => 0
        ];
        if ($temp['dmg']->result > 0 && $temp["hit"]->result > 1) {
            $temp['o_soak'] = self::roll(6, ($o->getSoakRoll() - self::getHealthModifier($o)));
            if ($temp['o_soak']->result < $temp["dmg"]->result) {
                $dmg = $temp["dmg"]->result - $temp["o_soak"]->result;
                $o->setHealth($o->getHealth() - $dmg);
            }
        }
        $action[] = $temp;
        return $action;
    }

    public static function extraTurns(combat_character $c, combat_character $o)
    {
        $action = [];
        if ($c->getDisciplines(42) > 0) {
            for ($i=0; $i<$c->getDisciplines(42); $i++) {
                $temp = [
                    'hit' => self::roll(6, ($c->getBrawlHitRoll() - self::getHealthModifier($c))),
                    'dmg' => self::roll(6, ($c->getBrawlDmgRoll() - self::getHealthModifier($c))),
                    'o_soak' => 0
                ];
                if ($temp['dmg']->result > 0 && $temp["hit"]->result > 1) {
                    $temp['o_soak'] = self::roll(6, ($o->getSoakRoll() - self::getHealthModifier($o)));
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

    public static function heal(combat_character $c)
    {
        if ($c->getHealth() < 7) {
            $c->setHealth($c->getHealth() + 1);
        }
    }

    public static function other($val)
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

    public static function getInit(combat_character $c1, combat_character $c2)
    {
        $init1 = self::roll(6, $c1->getInitRoll());
        $init2 = self::roll(6, $c2->getInitRoll());
        switch ($init1 <=> $init2) {
            case 1:
                return 0;
                break;
            case 0:
                self::getInit($c1, $c2);
                break;
            case -1:
                return 1;
                break;
        }
    }

    public static static function roll($diff, $pool)
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