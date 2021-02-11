<?php 
namespace AppBundle\Entity;

class attributes 
{
    private $physical;
    private $social;
    private $mental;

    public function getPhysical()
    {
        return $this->physical;
    }

    public function setPhysical($physical): attributes 
    {
        $this->physical = $physical;
        return $this;
    }

    public function getSocial()
    {
        return $this->social;
    }

    public function setSocial($social): attributes 
    {
        $this->social = $social;
        return $this;
    }

    public function getMental()
    {
        return $this->mental;
    }

    public function setMental($mental): attributes 
    {
        $this->mental = $mental;
        return $this;
    }
}