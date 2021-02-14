<?php
namespace AppBundle\Entity;

use JsonSerializable;

class roll implements JsonSerializable 
{
    private $status;
    private $result;

    public function getStatus(): string 
    {
        return $this->status;
    }

    public function setStatus(string $status): roll
    {
        $this->status = $status;
        return $this;
    }

    public function getResult(): int
    {
        return $this->result;
    }

    public function setResult(int $result): roll
    {
        $this->result = $result;
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