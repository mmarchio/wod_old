<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * point_schemas
 *
 * @ORM\Table(name="point_schemas")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\point_schemasRepository")
 */
class point_schemas implements JsonSerializable
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
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="sub_type", type="integer")
     */
    private $subType;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="attribute_primary", type="integer")
     */
    private $attributePrimary;

    /**
     * @var int
     *
     * @ORM\Column(name="attribute_secondary", type="integer")
     */
    private $attributeSecondary;

    /**
     * @var int
     *
     * @ORM\Column(name="attribute_tertiary", type="integer")
     */
    private $attributeTertiary;

    /**
     * @var int
     *
     * @ORM\Column(name="ability_primary", type="integer")
     */
    private $abilityPrimary;

    /**
     * @var int
     *
     * @ORM\Column(name="ability_secondary", type="integer")
     */
    private $abilitySecondary;

    /**
     * @var int
     *
     * @ORM\Column(name="ability_tertiary", type="integer")
     */
    private $abilityTertiary;

    /**
     * @var int
     *
     * @ORM\Column(name="advantages_special", type="integer")
     */
    private $advantagesSpecial;

    /**
     * @var int
     *
     * @ORM\Column(name="advantages_backgrounds", type="integer")
     */
    private $advantagesBackgrounds;

    /**
     * @var int
     *
     * @ORM\Column(name="advantages_virtues", type="integer")
     */
    private $advantagesVirtues;

    /**
     * @var int
     *
     * @ORM\Column(name="freebies", type="integer")
     */
    private $freebies;


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
     * Set type.
     *
     * @param int $type
     *
     * @return point_schemas
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set subType.
     *
     * @param int $subType
     *
     * @return point_schemas
     */
    public function setSubType($subType)
    {
        $this->subType = $subType;

        return $this;
    }

    /**
     * Get subType.
     *
     * @return int
     */
    public function getSubType()
    {
        return $this->subType;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return point_schemas
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set attributePrimary.
     *
     * @param int $attributePrimary
     *
     * @return point_schemas
     */
    public function setAttributePrimary($attributePrimary)
    {
        $this->attributePrimary = $attributePrimary;

        return $this;
    }

    /**
     * Get attributePrimary.
     *
     * @return int
     */
    public function getAttributePrimary()
    {
        return $this->attributePrimary;
    }

    /**
     * Set attributeSecondary.
     *
     * @param int $attributeSecondary
     *
     * @return point_schemas
     */
    public function setAttributeSecondary($attributeSecondary)
    {
        $this->attributeSecondary = $attributeSecondary;

        return $this;
    }

    /**
     * Get attributeSecondary.
     *
     * @return int
     */
    public function getAttributeSecondary()
    {
        return $this->attributeSecondary;
    }

    /**
     * Set attributeTertiary.
     *
     * @param int $attributeTertiary
     *
     * @return point_schemas
     */
    public function setAttributeTertiary($attributeTertiary)
    {
        $this->attributeTertiary = $attributeTertiary;

        return $this;
    }

    /**
     * Get attributeTertiary.
     *
     * @return int
     */
    public function getAttributeTertiary()
    {
        return $this->attributeTertiary;
    }

    /**
     * Set abilityPrimary.
     *
     * @param int $abilityPrimary
     *
     * @return point_schemas
     */
    public function setAbilityPrimary($abilityPrimary)
    {
        $this->abilityPrimary = $abilityPrimary;

        return $this;
    }

    /**
     * Get abilityPrimary.
     *
     * @return int
     */
    public function getAbilityPrimary()
    {
        return $this->abilityPrimary;
    }

    /**
     * Set abilitySecondary.
     *
     * @param int $abilitySecondary
     *
     * @return point_schemas
     */
    public function setAbilitySecondary($abilitySecondary)
    {
        $this->abilitySecondary = $abilitySecondary;

        return $this;
    }

    /**
     * Get abilitySecondary.
     *
     * @return int
     */
    public function getAbilitySecondary()
    {
        return $this->abilitySecondary;
    }

    /**
     * Set abilityTertiary.
     *
     * @param int $abilityTertiary
     *
     * @return point_schemas
     */
    public function setAbilityTertiary($abilityTertiary)
    {
        $this->abilityTertiary = $abilityTertiary;

        return $this;
    }

    /**
     * Get abilityTertiary.
     *
     * @return int
     */
    public function getAbilityTertiary()
    {
        return $this->abilityTertiary;
    }

    /**
     * Set advantagesSpecial.
     *
     * @param int $advantagesSpecial
     *
     * @return point_schemas
     */
    public function setAdvantagesSpecial($advantagesSpecial)
    {
        $this->advantagesSpecial = $advantagesSpecial;

        return $this;
    }

    /**
     * Get advantagesSpecial.
     *
     * @return int
     */
    public function getAdvantagesSpecial()
    {
        return $this->advantagesSpecial;
    }

    /**
     * Set advantagesBackgrounds.
     *
     * @param int $advantagesBackgrounds
     *
     * @return point_schemas
     */
    public function setAdvantagesBackgrounds($advantagesBackgrounds)
    {
        $this->advantagesBackgrounds = $advantagesBackgrounds;

        return $this;
    }

    /**
     * Get advantagesBackgrounds.
     *
     * @return int
     */
    public function getAdvantagesBackgrounds()
    {
        return $this->advantagesBackgrounds;
    }

    /**
     * Set advantagesVirtues.
     *
     * @param int $advantagesVirtues
     *
     * @return point_schemas
     */
    public function setAdvantagesVirtues($advantagesVirtues)
    {
        $this->advantagesVirtues = $advantagesVirtues;

        return $this;
    }

    /**
     * Get advantagesVirtues.
     *
     * @return int
     */
    public function getAdvantagesVirtues()
    {
        return $this->advantagesVirtues;
    }

    /**
     * Set freebies.
     *
     * @param int $freebies
     *
     * @return point_schemas
     */
    public function setFreebies($freebies)
    {
        $this->freebies = $freebies;

        return $this;
    }

    /**
     * Get freebies.
     *
     * @return int
     */
    public function getFreebies()
    {
        return $this->freebies;
    }
}
