<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\combat_record;
use AppBundle\Service\CombatUtils;

class CombatArenaSpecificController extends Controller
{
    private $em;
    private $characterProfileRepository;
    private $characterTraitsRepository;

    public function __construct()
    {
        $this->em = $this->getDoctrine()->getManager();
        $this->characterProfileRepository = $this->getDoctrine()->getRepository(character_profile::class);
        $this->characterTraitsRepository = $this->getDoctrine()->getRepository(character_traits::class);
    }
    /**
     * @Route("/arena/{p1}/{p2}")
     */
    public function arenaSpecificAction(Request $request, $p1, $p2)
    {
        $match = CombatUtils::combat(
            $this->em, 
            $this->characterProfileRepository, 
            $this->characterTraitsRepository, 
            $p1, 
            $p2
        );
        $r = "both died";
        if ($match) {
            if (!empty($match->win)) {
                $r = $match->win_name. " won";
                $em = $this->getDoctrine()->getManager();

                $cr = new combat_record();
                $cr->setWin($match->win);
                $cr->setLose($match->lose);
                $cr->setMatchId($match->matchId);

                $em->persist($cr);
                $em->flush();
            }
        }

        return new Response($r);
    }
} 
