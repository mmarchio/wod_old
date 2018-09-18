<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * trait_level
 *
 * @ORM\Table(name="trait_level")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\trait_levelRepository")
 */
class trait_level
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
     * @ORM\Column(name="level", type="integer")
     */
    private $level;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="trait", type="integer")
     */
    private $trait;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="system", type="text")
     */
    private $system;


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
     * Set level.
     *
     * @param int $level
     *
     * @return trait_level
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level.
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return trait_level
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
     * Set trait.
     *
     * @param int $trait
     *
     * @return trait_level
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
     * Set description.
     *
     * @param string $description
     *
     * @return trait_level
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set system.
     *
     * @param string $system
     *
     * @return trait_level
     */
    public function setSystem($system)
    {
        $this->system = $system;

        return $this;
    }

    /**
     * Get system.
     *
     * @return string
     */
    public function getSystem()
    {
        return $this->system;
    }
}
