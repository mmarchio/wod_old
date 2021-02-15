<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\combat_record;
use AppBundle\Service\CommonUtils;

class ArenaRandomController extends Controller
{
    /**
     * @Route("/arena/random/{series}", defaults={"series":1})
     */
    public function arenaRandomAction(Request $request, $series)
    {
        $doctrine = $this->getDoctrine();
        $r = "both died";
        for ($i=0; $i<$series; $i++) {
                $match = CommonUtils::combat($doctrine);
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
        }

        return new Response($r);
    }
}