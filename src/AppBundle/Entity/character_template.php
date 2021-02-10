<?php

namespace AppBundle\Entity;


class character_template implements JsonSerializable
{
    private $name;
    private $player;
    private $chronicle;
    private $nature;
    private $demeanor;
    private $concept;
    private $clan;
    private $clanId;
    private $generation;
    private $sire;
    private $attributes;
    private $abilities;
    private $advantages;
    private $path;
    private $willpower;
    private $freebies;
    private $traits;

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
    public function getFreebies()
    {
        return $this->freebies;
    }

    /**
     * @param mixed $freebies
     */
    public function setFreebies($freebies)
    {
        $this->freebies = $freebies;
    }

    /**
     * @return mixed
     */
    public function getClanId()
    {
        return $this->clanId;
    }

    /**
     * @param mixed $clanId
     */
    public function setClanId($clanId)
    {
        $this->clanId = $clanId;
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
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param mixed $player
     */
    public function setPlayer($player)
    {
        $this->player = $player;
    }

    /**
     * @return mixed
     */
    public function getChronicle()
    {
        return $this->chronicle;
    }

    /**
     * @param mixed $chronicle
     */
    public function setChronicle($chronicle)
    {
        $this->chronicle = $chronicle;
    }

    /**
     * @return mixed
     */
    public function getNature()
    {
        return $this->nature;
    }

    /**
     * @param mixed $nature
     */
    public function setNature($nature)
    {
        $this->nature = $nature;
    }

    /**
     * @return mixed
     */
    public function getDemeanor()
    {
        return $this->demeanor;
    }

    /**
     * @param mixed $demeanor
     */
    public function setDemeanor($demeanor)
    {
        $this->demeanor = $demeanor;
    }

    /**
     * @return mixed
     */
    public function getConcept()
    {
        return $this->concept;
    }

    /**
     * @param mixed $concept
     */
    public function setConcept($concept)
    {
        $this->concept = $concept;
    }

    /**
     * @return mixed
     */
    public function getClan()
    {
        return $this->clan;
    }

    /**
     * @param mixed $clan
     */
    public function setClan($clan)
    {
        $this->clan = $clan;
    }

    /**
     * @return mixed
     */
    public function getGeneration()
    {
        return $this->generation;
    }

    /**
     * @param mixed $generation
     */
    public function setGeneration($generation)
    {
        $this->generation = $generation;
    }

    /**
     * @return mixed
     */
    public function getSire()
    {
        return $this->sire;
    }

    /**
     * @param mixed $sire
     */
    public function setSire($sire)
    {
        $this->sire = $sire;
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param mixed $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return mixed
     */
    public function getAbilities()
    {
        return $this->abilities;
    }

    /**
     * @param mixed $abilities
     */
    public function setAbilities($abilities)
    {
        $this->abilities = $abilities;
    }

    /**
     * @return mixed
     */
    public function getAdvantages()
    {
        return $this->advantages;
    }

    /**
     * @param mixed $advantages
     */
    public function setAdvantages($advantages)
    {
        $this->advantages = $advantages;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getWillpower()
    {
        return $this->willpower;
    }

    /**
     * @param mixed $willpower
     */
    public function setWillpower($willpower)
    {
        $this->willpower = $willpower;
    }

    /**
     * @return mixed
     */
    public function getTraits()
    {
        $this->setTraits();
        return $this->traits;
    }

    /**
     * @param mixed $traits
     */
    public function setTraits()
    {
        $traits = [];
        $this->flattenTraitGroup($this->attributes, $traits);
        $this->flattenTraitGroup($this->abilities, $traits);
        $this->flattenTraitGroup($this->advantages, $traits);
        $path = new \stdClass();
        $path->id = 64;
        $path->value = $this->path;

        $wp = new \stdClass();
        $wp->id = 63;
        $wp->value = $this->willpower;

        $traits[] = $wp;
        $traits[] = $path;

        $this->traits = $traits;
    }

    protected function flattenTraitGroup($group, &$traits)
    {
        foreach ($group as $key => $value) {
            foreach ($value as $k => $v) {
                if ($k === "total" || $k === "target") {
                    continue;
                }
                $temp = new \stdClass();
                $temp->id = $v->id;
                $temp->value = $v->value;
                $traits[] = $temp;
            }
        }
    }

}
