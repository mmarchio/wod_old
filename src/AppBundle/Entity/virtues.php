<?php
namespace AppBundle\Entity;

use JsonSerializable;

class virtues implements JsonSerializable 
{
    private $conscience;
    private $selfControl;
    private $courage;

    public function getConscience(): int
    {
        return $this->conscience;
    }

    public function setConscience(int $conscience): virtues 
    {
        $this->conscience = $conscience;
        return $this;
    }

    public function getSelfControl(): int
    {
        return $this->selfControl;
    }

    public function setSelfControl(int $selfControl): virtues 
    {
        $this->selfControl = $selfControl;
        return $this;
    }

    public function getCourage(): int
    {
        return $this->courage;
    }

    public function setCourage(int $courage): virtues 
    {
        $this->courage = $courage;
        return $this;
    }

    public function jsonSerialize(): array
    {
        $a = [];
        foreach ($this as $k => &$v) {
            $a[$k] = $v;
        }
        return $a;
    }
}