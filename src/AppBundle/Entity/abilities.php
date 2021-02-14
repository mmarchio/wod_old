<?php
namespace AppBundle\Entity;

use JsonSerializable;

class abilities implements JsonSerializable
{
    private $talents;
    private $skills;
    private $knowledges;

    public function getTalents()
    {
        return $this->talents;
    }

    public function setTalents($talents): abilities 
    {
        $this->talents = $talents;
        return $this;
    }

    public function getSkills()
    {
        return $this->skills;
    }

    public function setSkills($skills): abilities
    {
        $this->skills = $skills;
        return $this;
    }

    public function getKnowledges()
    {
        return $this->knowledges;
    }

    public function setKnowledges($knowledges): abilities 
    {
        $this->knowledges = $knowledges;
        return $this;
    }

    public function jsonSerialize()
    {
        $a = [];
        foreach ($this as $k => &$v) {
            $a[$k] = $v;
        }
        return $a;
    }
}