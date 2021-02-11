<?php
namespace AppBundle\Entity;

class creation 
{
    private $points;
    private $traits;
    private $attributes;
    private $abilities;
    private $clans;
    private $backgrounds;
    private $virtues;
    private $clanDisciplines;
    private $selected;

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): creation
    {
        $this->points = $points;
        return $this;
    }

    public function getTraits()
    {
        return $this->traits;
    }

    public function setTraits($traits): creation
    {
        $this->traits = $traits;
        return $this;
    }

    public function getAttributes(): attributes 
    {
        return $this->attributes;
    }

    public function setAttributes(attributes $attributes): creation 
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function getAbilities(): abilities 
    {
        return $this->abilities;
    }

    public function setAbilities(abilities $abilities): creation 
    {
        $this->abilities = $abilities;
        return $this;
    }

    public function getClans($clans)
    {
        return $this->clans;
    }

    public function setClans($clans): creation 
    {
        $this->clans = $clans;
        return $this;
    }

    public function getBackgrounds()
    {
        return $this->backgrounds;
    }

    public function setBackgrounds($backgrounds): creation
    {
        $this->backgrounds = $backgrounds;
        return $this;
    }

    public function getVirtues()
    {
        return $this->virtues;
    }

    public function setVirtues($virtues): creation
    {
        $this->virtues = $virtues;
        return $this;
    }

    public function getClanDisciplines()
    {
        return $this->clanDisciplines;
    }

    public function setClanDisciplines($clanDisciplines): creation
    {
        $this->clanDisciplines = $clanDisciplines;
        return $this;
    }

    public function getSelected()
    {
        return $this->selected;
    }

    public function setSelected($selected): creation 
    {
        $this->creation = $selected;
        return $this;
    }
}