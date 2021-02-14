<?php
namespace AppBundle\Entity;

use AppBundle\Interfaces\TraitGroupInterface;
use JsonSerializable;

class trait_allocation implements TraitGroupInterface, JsonSerializable

{
    private $type;
    private $cost;
    private $group;
    private $items;
    private $item;

    public function __construct(string $type, int $cost, ?array $group, ?array $items, $item = null)
    {
        $this->setType($type)
            ->setCost($cost)
            ->setGroup($group)
            ->setItems($items)
            ->setItem($item);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): trait_allocation
    {
        $this->type = $type;
        return $this;
    }

    public function getCost(): int
    {
        return $this->cost;
    }

    public function setCost(int $cost): trait_allocation
    {
        $this->cost = $cost;
        return $this;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup(?array $group ): trait_allocation
    {
        $this->group = $group;
        return $this;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function setItems(?array $items): trait_allocation
    {
        $this->items = $items;
        return $this;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function setItem($item = null): trait_allocation
    {
        $this->item = $item;
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