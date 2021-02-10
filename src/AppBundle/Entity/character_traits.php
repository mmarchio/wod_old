<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * character_traits
 *
 * @ORM\Table(name="character_traits")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\character_traitsRepository")
 */
class character_traits implements JsonSerializable
{
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
     * @ORM\Column(name="value", type="integer")
     */
    private $value;

    /**
     * @var int
     *
     * @ORM\Column(name="character_profile", type="integer")
     */
    private $characterProfile;

    public function jsonSerialize(): string
    {
        $a = [];
        foreach ($this as $k => $v) {
            $a[$k] = $v;
        }
        return $a;
    }

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
     * @return character_traits
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
     * Set value.
     *
     * @param int $value
     *
     * @return character_traits
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value.
     *
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set characterProfile.
     *
     * @param int $characterProfile
     *
     * @return character_traits
     */
    public function setCharacterProfile($characterProfile)
    {
        $this->characterProfile = $characterProfile;

        return $this;
    }

    /**
     * Get characterProfile.
     *
     * @return int
     */
    public function getCharacterProfile()
    {
        return $this->characterProfile;
    }
}
