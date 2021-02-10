<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * character_profile
 *
 * @ORM\Table(name="character_profile")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\character_profileRepository")
 */
class character_profile implements JsonSerializable
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
     * @ORM\Column(name="player", type="string", length=255)
     */
    private $player;

    /**
     * @var string
     *
     * @ORM\Column(name="chronicle", type="string", length=255)
     */
    private $chronicle;

    /**
     * @var string
     *
     * @ORM\Column(name="nature", type="integer")
     */
    private $nature;

    /**
     * @var string
     *
     * @ORM\Column(name="demeanor", type="integer")
     */
    private $demeanor;

    /**
     * @var string
     *
     * @ORM\Column(name="concept", type="string", length=255)
     */
    private $concept;

    /**
     * @var int
     *
     * @ORM\Column(name="clan", type="integer")
     */
    private $clan;

    /**
     * @var int
     *
     * @ORM\Column(name="generation", type="integer")
     */
    private $generation;

    /**
     * @var int
     *
     * @ORM\Column(name="sire", type="integer")
     */
    private $sire;

    /**
     * @var int
     *
     * @ORM\Column(name="freebies", type="integer")
     */
    private $freebies;

    /**
     * @return int
     */
    public function getFreebies()
    {
        return $this->freebies;
    }

    /**
     * @param int $freebies
     *
     * @return character_profile
     */
    public function setFreebies(int $freebies)
    {
        $this->freebies = $freebies;

        return $this;
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
     * Set name.
     *
     * @param string $name
     *
     * @return character_profile
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
     * Set player.
     *
     * @param string $player
     *
     * @return character_profile
     */
    public function setPlayer($player)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player.
     *
     * @return string
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set chronicle.
     *
     * @param string $chronicle
     *
     * @return character_profile
     */
    public function setChronicle($chronicle)
    {
        $this->chronicle = $chronicle;

        return $this;
    }

    /**
     * Get chronicle.
     *
     * @return string
     */
    public function getChronicle()
    {
        return $this->chronicle;
    }

    /**
     * Set nature.
     *
     * @param int $nature
     *
     * @return character_profile
     */
    public function setNature($nature)
    {
        $this->nature = $nature;

        return $this;
    }

    /**
     * Get nature.
     *
     * @return string
     */
    public function getNature()
    {
        return $this->nature;
    }

    /**
     * Set demeanor.
     *
     * @param int $demeanor
     *
     * @return character_profile
     */
    public function setDemeanor($demeanor)
    {
        $this->demeanor = $demeanor;

        return $this;
    }

    /**
     * Get demeanor.
     *
     * @return string
     */
    public function getDemeanor()
    {
        return $this->demeanor;
    }

    /**
     * Set concept.
     *
     * @param string $concept
     *
     * @return character_profile
     */
    public function setConcept($concept)
    {
        $this->concept = $concept;

        return $this;
    }

    /**
     * Get concept.
     *
     * @return string
     */
    public function getConcept()
    {
        return $this->concept;
    }

    /**
     * Set clan.
     *
     * @param int $clan
     *
     * @return character_profile
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

    /**
     * Set generation.
     *
     * @param int $generation
     *
     * @return character_profile
     */
    public function setGeneration($generation)
    {
        $this->generation = $generation;

        return $this;
    }

    /**
     * Get generation.
     *
     * @return int
     */
    public function getGeneration()
    {
        return $this->generation;
    }

    /**
     * Set sire.
     *
     * @param int $sire
     *
     * @return character_profile
     */
    public function setSire($sire)
    {
        $this->sire = $sire;

        return $this;
    }

    /**
     * Get sire.
     *
     * @return int
     */
    public function getSire()
    {
        return $this->sire;
    }
}
