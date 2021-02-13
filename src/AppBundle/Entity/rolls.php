<?php
namespace AppBundle\Entity;

use JsonSerializable;

class rolls implements JsonSerializable
{
    private $rolls;
    private $success;
    private $botch;
    private $failure;
    private $result;

    public function getRolls(): array
    {
        return $this->rolls;
    }

    public function setRolls(array $rolls): rolls 
    {
        $this->rolls = $rolls;
        return $this;
    }

    public function appendRolls(roll $roll): rolls
    {
        $this->rolls[] = $roll;
        return $this;
    }

    public function getSuccess(): int
    {
        return $this->success;
    }

    public function setSuccess(int $success): rolls
    {
        $this->success = $success;
        return $this;
    }

    public function getBotch(): int
    {
        return $this->botch;
    }

    public function setBotch(int $botch): rolls
    {
        $this->botch = $botch;
        return $this;
    }

    public function getFailure(): int
    {
        return $this->failure;
    }

    public function setFailure(int $failure): rolls
    {
        $this->failure = $failure;
        return $this;
    }

    public function getResult(): int
    {
        return $this->result;
    }

    public function setResult(int $result): rolls
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