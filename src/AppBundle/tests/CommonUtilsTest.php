<?php
namespace AppBundle\tests;

use AppBundle\Entity\combat_character;
use AppBundle\Service\CombatUtils;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Service\CommonUtils;

class CommonUtilsTest extends WebTestCase 
{
    public static function testGenUuid(): void
    {
        self::assertString(CommonUtils::gen_uuid());
    }

    public static function testOther(): void
    {
        self::assertEquals(0, CommonUtils::other(1));
        self::assertEquals(1, CommonUtils::other(0));
    }

    public static function testHeal(): void
    {
        $t = new combat_character();
        $t->setHealth(6);
        CombatUtils::heal($t);
        self::assertEquals(7, $t->getHealth());
    }

    public static function testGetHealthModifier(): void
    {
        $t = new combat_character;
        $t->setHealth(0);
        self::assertEquals(-20, CommonUtils::getHealthModifier($t));
    }

    public static function testCombatAction(): void
    {
        $c = new combat_character;
        $o = new combat_character;
        $c->setStrength(1);
        $c->setDexterity(1);
        $c->setStamina(1);
        $c->setPerception(1);
        $c->setBrawl(1);
        $c->setDodge(1);
        $c->setFirearms(1);
        $c->setMelee(1);
        $c->setBrawlDmgRoll($c->getStrength());
        $c->setBrawlHitRoll($c->getDexterity() + $c->getBrawl());
        $c->setMeleeDmgRoll($c->getDexterity() + $c->getMelee());
        $c->setFirearmsHitRoll($c->getPerception() + $c->getFirearms());
        $c->setSoakRoll($c->getStamina());
        $c->setHealth(7);

        $o->setStrength(1);
        $o->setDexterity(1);
        $o->setStamina(1);
        $o->setPerception(1);
        $o->setBrawl(1);
        $o->setDodge(1);
        $o->setFirearms(1);
        $o->setMelee(1);
        $o->setBrawlDmgRoll($o->getStrength());
        $o->setBrawlHitRoll($o->getDexterity() + $o->getBrawl());
        $o->setMeleeDmgRoll($o->getDexterity() + $o->getMelee());
        $o->setFirearmsHitRoll($o->getPerception() + $o->getFirearms());
        $o->setSoakRoll($o->getStamina());
        $o->setHealth(7);

        $test = CommonUtils::combatAction($c, $o);

        self::assertIsArray($test);
        self::assertIsInt($test[0]['hit']);
        self::assertIsInt($test[0]['dmg']);
        self::assertIsInt($test[0]['o_soak']);
    }

    public static function testRoll(): void 
    {
        $test = CommonUtils::roll(6, 5);
        self::assertIsObject($test);
        self::assertIsArray($test->rolls);
        self::assertIsInt($test->success);
        self::assertIsInt($test->failure);
        self::assertIsInt($test->botch);
        self::assertIsInt($test->result);
        self::assertIsObject($test->rolls[0]);
        self::assertIsInt($test->rolls[0]->result);
        self::assertIsString($test->rolls[0]->status);
    }

    public static function testExtraTurns(): void
    {
        $c = new combat_character;
        $o = new combat_character;
        $c->setStrength(1);
        $c->setDexterity(1);
        $c->setStamina(1);
        $c->setPerception(1);
        $c->setBrawl(1);
        $c->setDodge(1);
        $c->setFirearms(1);
        $c->setMelee(1);
        $c->setBrawlDmgRoll($c->getStrength());
        $c->setBrawlHitRoll($c->getDexterity() + $c->getBrawl());
        $c->setMeleeDmgRoll($c->getDexterity() + $c->getMelee());
        $c->setFirearmsHitRoll($c->getPerception() + $c->getFirearms());
        $c->setSoakRoll($c->getStamina());
        $c->setHealth(7);
        $c->setDisciplines([42 => 1]);

        $o->setStrength(1);
        $o->setDexterity(1);
        $o->setStamina(1);
        $o->setPerception(1);
        $o->setBrawl(1);
        $o->setDodge(1);
        $o->setFirearms(1);
        $o->setMelee(1);
        $o->setBrawlDmgRoll($o->getStrength());
        $o->setBrawlHitRoll($o->getDexterity() + $o->getBrawl());
        $o->setMeleeDmgRoll($o->getDexterity() + $o->getMelee());
        $o->setFirearmsHitRoll($o->getPerception() + $o->getFirearms());
        $o->setSoakRoll($o->getStamina());
        $o->setHealth(7);
        $o->setDisciplines([42 => 1]);

        $test = CommonUtils::extraTurns($c, $o);

        self::assertIsArray($test);
        self::assertIsInt($test[0]['hit']);
        self::assertIsInt($test[0]['dmg']);
        self::assertIsInt($test[0]['o_soak']);
    }

    public static function testGetKeyLists(): void 
    {
        $group = ['a' => 'a', 'target' => 1, 'total' => 1];
        $test = CommonUtils::getKeyLists($group);
        self::assertEquals(['a'], $test);
    }

    public static function testPstSetter(): void
    {
        $test = CommonUtils::pst_setter();
        self::assertIsObject($test);
        self::assertIsInt($test->p);
        self::assertIsInt($test->s);
        self::assertIsInt($test->t);
    }

    public static function testGetInit(): void
    {
        $c = new combat_character;
        $o = new combat_character;
        $c->setInitRoll(1);
        $o->setInitRoll(1);
        $test = CombatUtils::getInit($c, $o);
        self::assertIsInt($test);
    }
}