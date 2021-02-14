<?php
namespace AppBundle\tests;

use AppBundle\Controller\CombatController;
use AppBundle\Entity\character_traits;
use AppBundle\Entity\combat_character;
use AppBundle\Entity\trait_entity;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class CombatControllerTest extends TestCase
{
    public function testFindTraitValue(): void 
    {
        $trait = new character_traits;
        $trait->setTrait('test');
        $trait->setValue(1);
        $traits = [$trait];

        $refl = new ReflectionClass(CombatController::class);
        $refl->getMethod('findTraitValue')->setAccessible(true);

        $this->assertEquals(1, $refl->findTraitValue(1, $traits));

        $this->assertEquals(null, $refl->findTraitValue(2, $traits));
    }

    public function testGetHealthModifier(): void 
    {
        $refl = new ReflectionClass(CombatController::class);
        $refl->getMethod('getHealthModifier')->setAccessible(true);
        $this->assertInteger($refl->getHealthModifier(1));
        $this->assertEquals(-20, $refl->getHealthModifier(0));
    }

    public function testHeal(): void 
    {
        $refl = new ReflectionClass(CombatController::class);
        $refl->getMethod('heal')->setAccessible(true);

        $test = new combat_character;
        $test->setHealth(6);
        $this->assertEquals(7, $refl->heal($test));
    }

    public function testOther(): void 
    {
        $refl = new ReflectionClass(CombatController::class);
        $refl->getMethod('other')->setAccessible(true);

        $this->assertEquals(1, $refl->other(0));
        $this->assertEquals(0, $refl->other(1));
    }
}