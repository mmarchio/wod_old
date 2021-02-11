<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * combat_record
 *
 * @ORM\Table(name="combat_record")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\combat_recordRepository")
 */
class combat_record implements JsonSerializable
{
    public function jsonSerialize(): array
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
     * @ORM\Column(name="win", type="integer")
     */
    private $win;

    /**
     * @var int
     *
     * @ORM\Column(name="lose", type="integer")
     */
    private $lose;

    /**
     * @var string
     *
     * @ORM\Column(name="record", type="text")
     */
    private $record;

    /**
     * @var string
     *
     * @ORM\Column(name="match_id", type="text")
     */
    private $matchId;

    /**
     * @return mixed
     */
    public function getMatchId()
    {
        return $this->matchId;
    }

    /**
     * @param mixed $matchId
     */
    public function setMatchId($matchId)
    {
        $this->matchId = $matchId;
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
     * Set win.
     *
     * @param int $win
     *
     * @return combat_record
     */
    public function setWin($win)
    {
        $this->win = $win;

        return $this;
    }

    /**
     * Get win.
     *
     * @return int
     */
    public function getWin()
    {
        return $this->win;
    }

    /**
     * Set lose.
     *
     * @param int $lose
     *
     * @return combat_record
     */
    public function setLose($lose)
    {
        $this->lose = $lose;

        return $this;
    }

    /**
     * Get lose.
     *
     * @return int
     */
    public function getLose()
    {
        return $this->lose;
    }

    /**
     * Set record.
     *
     * @param string $record
     *
     * @return combat_record
     */
    public function setRecord($record)
    {
        $this->record = $record;

        return $this;
    }

    /**
     * Get record.
     *
     * @return string
     */
    public function getRecord()
    {
        return $this->record;
    }
}
