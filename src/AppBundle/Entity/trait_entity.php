<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * trait_entity
 *
 * @ORM\Table(name="trait_entity")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\trait_entityRepository")
 */
class trait_entity implements JsonSerializable
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
     * @var string
     *
     * @ORM\Column(name="trait", type="string", length=255)
     */
    private $trait;

    /**
     * @var int
     *
     * @ORM\Column(name="category", type="integer")
     */
    private $category;

    /**
     * @var int
     *
     * @ORM\Column(name="sub_category", type="integer")
     */
    private $sub_category;

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
     * @param string $trait
     *
     * @return trait_entity
     */
    public function setTrait($trait)
    {
        $this->trait = $trait;

        return $this;
    }

    /**
     * Get trait.
     *
     * @return string
     */
    public function getTrait()
    {
        return $this->trait;
    }

    /**
     * Set category.
     *
     * @param int $category
     *
     * @return trait_entity
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set sub_category.
     *
     * @param int $sub_category
     *
     * @return trait_entity
     */
    public function setSubCategory($sub_category)
    {
        $this->sub_category = $sub_category;

        return $this;
    }

    /**
     * Get sub_category.
     *
     * @return int
     */
    public function getSubCategory()
    {
        return $this->sub_category;
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
