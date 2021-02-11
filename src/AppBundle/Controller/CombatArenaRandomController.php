<?php
namespace AppBundle\Controller;

use AppBundle\Entity\character_traits;
use AppBundle\Entity\combat_record;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Service\CombatUtils;

class CombatArenaController extends Controller 
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
     * @Route("/arena/random/{series}", name="RandomArena", defaults={"series":1})
     * @IsGranted("ROLE_ADMIN")
     */
    public function arenaRandomAction(Request $request, $series): Response
    {
        for ($i=0; $i<$series; $i++) {
            try {
                $match = CombatUtils::combat(
                    $this->em, 
                    $this->characterProfileRepository, 
                    $this->characterTraitsRepository
                );
            } catch (\Exception $e) {
                continue;
            }
            $r = "both died";
            if ($match) {
                if (!empty($match->win)) {
                    $r = $match->win_name. " won";

                    $cr = new combat_record();
                    $cr->setWin($match->win);
                    $cr->setLose($match->lose);
                    $cr->setMatchId($match->matchId);

                    $this->em->persist($cr);
                    $this->em->flush();
                }
            }
        }

        return new Response($r);
    }
}