<?php
namespace AppBundle\Entity;

use JsonSerializable;

class trait_value implements JsonSerializable
{
    private $id;
    private $value;

    public function getId(): int 
    {
        return $this->id;
    }

    public function setId(int $id): trait_value
    {
        $this->id = $id;
        return $this;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): trait_value
    {
        $this->value = $value;
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