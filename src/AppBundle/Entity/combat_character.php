<?php

namespace AppBundle\Entity;

class combat_character implements JsonSerializable
{
    private $id;
    private $strength;
    private $dexterity;
    private $stamina;
    private $perception;
    private $brawl;
    private $dodge;
    private $firearms;
    private $melee;
    private $disciplines;
    private $modifier;
    private $turns;
    private $brawlHitRoll;
    private $brawlDmgRoll;
    private $initRoll;
    private $firearmsHitRoll;
    private $firearmsDmgRoll;
    private $dodgeRoll;
    private $meleeHitRoll;
    private $meleeDmgRoll;
    private $health;
    private $healthModifier;
    private $soakRoll;
    private $name;

    public function jsonSerialize(): string
    {
        $a = [];
        foreach ($this as $k => $v) {
            $a[$k] = $v;
        }
        return $a;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getSoakRoll()
    {
        return $this->soakRoll;
    }

    /**
     * @param mixed $soakRoll
     */
    public function setSoakRoll($soakRoll)
    {
        $this->soakRoll = $soakRoll;
    }

    /**
     * @return mixed
     */
    public function getHealth()
    {
        return $this->health;
    }

    /**
     * @param mixed $health
     */
    public function setHealth($health)
    {
        $this->health = $health;
    }

    /**
     * @return mixed
     */
    public function getHealthModifier($id)
    {
        return $this->healthModifier[$id];
    }

    /**
     * @param mixed $healthModifier
     */
    public function setHealthModifier($healthModifier)
    {
        $this->healthModifier = $healthModifier;
    }

    /**
     * @return mixed
     */
    public function getBrawlHitRoll()
    {
        return $this->brawlHitRoll;
    }

    /**
     * @param mixed $brawlHitRoll
     */
    public function setBrawlHitRoll($brawlHitRoll)
    {
        $this->brawlHitRoll = $brawlHitRoll;
    }

    /**
     * @return mixed
     */
    public function getBrawlDmgRoll()
    {
        return $this->brawlDmgRoll;
    }

    /**
     * @param mixed $brawlDmgRoll
     */
    public function setBrawlDmgRoll($brawlDmgRoll)
    {
        $this->brawlDmgRoll = $brawlDmgRoll;
    }

    /**
     * @return mixed
     */
    public function getFirearmsHitRoll()
    {
        return $this->firearmsHitRoll;
    }

    /**
     * @param mixed $firearmsHitRoll
     */
    public function setFirearmsHitRoll($firearmsHitRoll)
    {
        $this->firearmsHitRoll = $firearmsHitRoll;
    }

    /**
     * @return mixed
     */
    public function getFirearmsDmgRoll()
    {
        return $this->firearmsDmgRoll;
    }

    /**
     * @param mixed $firearmsDmgRoll
     */
    public function setFirearmsDmgRoll($firearmsDmgRoll)
    {
        $this->firearmsDmgRoll = $firearmsDmgRoll;
    }

    /**
     * @return mixed
     */
    public function getMeleeHitRoll()
    {
        return $this->meleeHitRoll;
    }

    /**
     * @param mixed $meleeHitRoll
     */
    public function setMeleeHitRoll($meleeHitRoll)
    {
        $this->meleeHitRoll = $meleeHitRoll;
    }

    /**
     * @return mixed
     */
    public function getMeleeDmgRoll()
    {
        return $this->meleeDmgRoll;
    }

    /**
     * @param mixed $meleeDmgRoll
     */
    public function setMeleeDmgRoll($meleeDmgRoll)
    {
        $this->meleeDmgRoll = $meleeDmgRoll;
    }

    /**
     * @return mixed
     */
    public function getStrength()
    {
        return $this->strength;
    }

    /**
     * @param mixed $strength
     */
    public function setStrength($strength)
    {
        $this->strength = $strength;
    }

    /**
     * @return mixed
     */
    public function getDexterity()
    {
        return $this->dexterity;
    }

    /**
     * @param mixed $dexterity
     */
    public function setDexterity($dexterity)
    {
        $this->dexterity = $dexterity;
    }

    /**
     * @return mixed
     */
    public function getStamina()
    {
        return $this->stamina;
    }

    /**
     * @param mixed $stamina
     */
    public function setStamina($stamina)
    {
        $this->stamina = $stamina;
    }

    /**
     * @return mixed
     */
    public function getPerception()
    {
        return $this->perception;
    }

    /**
     * @param mixed $perception
     */
    public function setPerception($perception)
    {
        $this->perception = $perception;
    }

    /**
     * @return mixed
     */
    public function getBrawl()
    {
        return $this->brawl;
    }

    /**
     * @param mixed $brawl
     */
    public function setBrawl($brawl)
    {
        $this->brawl = $brawl;
    }

    /**
     * @return mixed
     */
    public function getDodge()
    {
        return $this->dodge;
    }

    /**
     * @param mixed $dodge
     */
    public function setDodge($dodge)
    {
        $this->dodge = $dodge;
    }

    /**
     * @return mixed
     */
    public function getFirearms()
    {
        return $this->firearms;
    }

    /**
     * @param mixed $firearms
     */
    public function setFirearms($firearms)
    {
        $this->firearms = $firearms;
    }

    /**
     * @return mixed
     */
    public function getMelee()
    {
        return $this->melee;
    }

    /**
     * @param mixed $melee
     */
    public function setMelee($melee)
    {
        $this->melee = $melee;
    }

    /**
     * @return mixed
     */
    public function getDisciplines($id)
    {
        return $this->disciplines[$id];
    }

    /**
     * @param mixed $disciplines
     */
    public function setDisciplines($disciplines)
    {
        $this->disciplines = $disciplines;
    }

    /**
     * @return mixed
     */
    public function getModifier()
    {
        return $this->modifier;
    }

    /**
     * @param mixed $modifier
     */
    public function setModifier($modifier)
    {
        $this->modifier = $modifier;
    }

    /**
     * @return mixed
     */
    public function getTurns()
    {
        return $this->turns;
    }

    /**
     * @param mixed $turns
     */
    public function setTurns($turns)
    {
        $this->turns = $turns;
    }

    /**
     * @return mixed
     */
    public function getBrawlRoll()
    {
        return $this->brawlRoll;
    }

    /**
     * @param mixed $brawlRoll
     */
    public function setBrawlRoll($brawlRoll)
    {
        $this->brawlRoll = $brawlRoll;
    }

    /**
     * @return mixed
     */
    public function getInitRoll()
    {
        return $this->initRoll;
    }

    /**
     * @param mixed $initRoll
     */
    public function setInitRoll($initRoll)
    {
        $this->initRoll = $initRoll;
    }

    /**
     * @return mixed
     */
    public function getFirearmsRoll()
    {
        return $this->firearmsRoll;
    }

    /**
     * @param mixed $firearmsRoll
     */
    public function setFirearmsRoll($firearmsRoll)
    {
        $this->firearmsRoll = $firearmsRoll;
    }

    /**
     * @return mixed
     */
    public function getDodgeRoll()
    {
        return $this->dodgeRoll;
    }

    /**
     * @param mixed $dodgeRoll
     */
    public function setDodgeRoll($dodgeRoll)
    {
        $this->dodgeRoll = $dodgeRoll;
    }

    /**
     * @return mixed
     */
    public function getMeleeRoll()
    {
        return $this->meleeRoll;
    }

    /**
     * @param mixed $meleeRoll
     */
    public function setMeleeRoll($meleeRoll)
    {
        $this->meleeRoll = $meleeRoll;
    }
}
