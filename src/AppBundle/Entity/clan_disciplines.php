<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * clan_disciplines
 *
 * @ORM\Table(name="clan_disciplines")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\clan_disciplinesRepository")
 */
class clan_disciplines implements JsonSerializable
{
    public function jsonSerialize(): string
    {
        $a = [];
        foreach ($this as $k => $v) {
            $a[$k] = $v;
        }
        return $a;
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="trait", type="integer")
     */
    private $trait;

    /**
     * @var int
     *
     * @ORM\Column(name="clan", type="integer")
     */
    private $clan;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set trait.
     *
     * @param int $trait
     *
     * @return clan_disciplines
     */
    public function setTrait($trait)
    {
        $this->trait = $trait;

        return $this;
    }

    /**
     * Get trait.
     *
     * @return int
     */
    public function getTrait()
    {
        return $this->trait;
    }

    /**
     * Set clan.
     *
     * @param int $clan
     *
     * @return clan_disciplines
     */
    public function setClan($clan)
    {
        $this->clan = $clan;

        return $this;
    }

    /**
     * Get clan.
     *
     * @return int
     */
    public function getClan()
    {
        return $this->clan;
    }

    public function toAnon()
    {
        $a = new \stdClass();
        foreach ($this as $k => $v) {
            $a->$k = $v;
        }
        return $a;
    }
}
