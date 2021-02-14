<?php
namespace AppBundle\Service;

use AppBundle\Entity\combat_character;

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

}