<?php
namespace AppBundle\Entity;

class abilities 
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
}