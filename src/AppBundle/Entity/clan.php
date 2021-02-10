<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * clan
 *
 * @ORM\Table(name="clan")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\clanRepository")
 */
class clan implements JsonSerializable
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="weakness", type="text")
     */
    private $weakness;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;


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
     * Set name.
     *
     * @param string $name
     *
     * @return clan
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
     * Set weakness.
     *
     * @param string $weakness
     *
     * @return clan
     */
    public function setWeakness($weakness)
    {
        $this->weakness = $weakness;

        return $this;
    }

    /**
     * Get weakness.
     *
     * @return string
     */
    public function getWeakness()
    {
        return $this->weakness;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return clan
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
}
