<?php
namespace AppBundle\Service;

use AppBundle\Entity\combat_character;
use AppBundle\Entity\character_profile;
use AppBundle\Entity\character_traits;
use AppBundle\Entity\actions;

class CommonUtils 
{
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

    public static function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    public static function getHealthModifier(combat_character $c)
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

    public static function roll($diff, $pool)
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

    public static function extraTurns(combat_character $c, combat_character $o)
    {
        $action = [];
        if ($c->getDisciplines(42) > 0) {
            for ($i=0; $i<$c->getDisciplines(42); $i++) {
                $temp = self::combatAction($c, $o)[0];
                $action[] = $temp;
            }
        }
        return $action;
    }

    public static function getKeyLists($group)
    {
        $a = [];
        foreach ($group as $k => $_) {
            if ($k !== "total" && $k !== "target") {
                $a[] = $k;
            }
        }
        return $a;
    }

    public static function pst_setter()
    {
        $pst = [];
        $temp = new \stdClass();
        $temp->p = 0;
        $temp->s = 1;
        $temp->t = 2;
        $pst[] = $temp;
        $temp = new \stdClass();
        $temp->p = 0;
        $temp->s = 2;
        $temp->t = 1;
        $pst[] = $temp;
        $temp = new \stdClass();
        $temp->p = 1;
        $temp->s = 0;
        $temp->t = 2;
        $pst[] = $temp;
        $temp = new \stdClass();
        $temp->p = 1;
        $temp->s = 2;
        $temp->t = 0;
        $pst[] = $temp;
        $temp = new \stdClass();
        $temp->p = 2;
        $temp->s = 0;
        $temp->t = 1;
        $pst[] = $temp;
        $temp = new \stdClass();
        $temp->p = 2;
        $temp->s = 1;
        $temp->t = 0;
        $pst[] = $temp;

        $r = rand(0,5);
        return $pst[$r];
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

    public static function combat($doctrine, $p1 = null, $p2 = null)
    {
        $match_id = self::gen_uuid();
        dump($match_id);

        $characters = new \stdClass();
        $characters->p1 = 1;
        $characters->p2 = 2;

        if ($p1 && $p2) {
            $characters->p1 = $p1;
            $characters->p2 = $p2;
        } else {
            $characters = $doctrine
                ->getRepository(character_profile::class)
                ->findAll();

            $character_count = count($characters) - 1;
            $characters = self::getRandomCharacters($character_count);
        }
        $characters->p1 = self::getCombatCharacter($doctrine, $characters->p1);
        $characters->p2 = self::getCombatCharacter($doctrine, $characters->p2);

        $turn = [];
        $end = false;
        while ($end === false) {
            $turn = self::combatTurn($characters->p1, $characters->p2);
            dump($turn);
            $turn[] = $turn;
            $a = new actions();
            $a->setMatchId($match_id);
            $a->setAction(serialize($turn));
            $em = $doctrine->getManager();
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

    public static function getCombatCharacter($doctrine, $id)
    {
        $ct = $doctrine
            ->getRepository(character_traits::class)
            ->findBy(["characterProfile" => $id]);
        $cp = $doctrine
            ->getRepository(character_profile::class)
            ->find($id);
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

    public static function findTraitValue($id, $traits)
    {
        $c = count($traits);
        for ($i=0; $i<$c; $i++) {
            if ($traits[$i]->getTrait() == $id) {
                return $traits[$i]->getValue();
            }
        }
        return null;
    }

    public static function combatTurn(combat_character $c1, combat_character $c2)
    {
        $turn = [];
        $players = [
            $c1,
            $c2
        ];
        $init = CommonUtils::getInit($c1, $c2);
        $init_winner = $players[$init];
        $init_loser = $players[self::other($init)];

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
            $p1['extra'] = self::extraTurns($init_winner, $init_loser);
        }
        if ($init_loser->getHealth() > 0) {
            $p2['extra'] = self::extraTurns($init_loser, $init_winner);
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

}