<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * actions
 *
 * @ORM\Table(name="actions")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\actionsRepository")
 */
class actions implements JsonSerializable
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
     * @var string
     *
     * @ORM\Column(name="action", type="text")
     */
    private $action;

    /**
     * @var string
     *
     * @ORM\Column(name="match_id", type="string")
     */
    private $matchId;

    /**
     * @var int
     *
     * @ORM\Column(name="cid", type="integer")
     */
    private $cid;

    /**
     * @return int
     */
    public function getCid()
    {
        return $this->cid;
    }

    /**
     * @param int $cid
     */
    public function setCid(int $cid)
    {
        $this->cid = $cid;
    }

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
     * Set action.
     *
     * @param string $action
     *
     * @return actions
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
}
