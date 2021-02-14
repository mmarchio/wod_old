<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Service\CombatUtils;

class CombatArenaStatsController extends Controller
{
    /**
     * @Route("/arena/stats")
     */
    public function arenaStatsAction(Request $request)
    {
        $characterProfileRepository = $this->getDoctrine()->getRepository(character_profile::class);
        $record = $this->getDoctrine()
            ->getRepository(combat_record::class)
            ->findAll();

        $clans = $this->getDoctrine()
            ->getRepository(clan::class)
            ->findAll();

        $clan_count = count($clans);
        $clan_names = [];
        for ($i=0; $i<$clan_count; $i++) {
            $clan_names[$clans[$i]->getId()] = $clans[$i]->getName();
        }

        $record_count = count($record);
        $data = new \stdClass();
        $data->wins = [];
        $data->losses = [];
        $data->clan_win = [];
        $data->clan_lose = [];
        $data->characters = [];
        $data->match_count = $record_count;
        for ($i=0; $i<$record_count; $i++) {
            $wcp = $characterProfileRepository->find($record[$i]->getWin());
            $lcp = $characterProfileRepository->find($record[$i]->getLose());
            $data->wins[] = $wcp;
            if (empty($data->characters[$wcp->getId()])) {
                CombatUtils::matchStats($data->characters[$wcp->getId()]);
            }
            if (empty($data->characters[$lcp->getId()])) {
                CombatUtils::matchStats($data->characters[$lcp->getId()]);
            }
            if (empty($data->clan_win[$clan_names[$wcp->getClan()]])) {
                $data->clan_win[$clan_names[$wcp->getClan()]] = new \stdClass();
                $data->clan_win[$clan_names[$wcp->getClan()]]->wins = 1;
                $data->clan_win[$clan_names[$wcp->getClan()]]->percentage = number_format($data->clan_win[$clan_names[$wcp->getClan()]]->wins / $record_count * 100,2);
            } else {
                $data->clan_win[$clan_names[$wcp->getClan()]]->wins++;
                $data->clan_win[$clan_names[$wcp->getClan()]]->percentage = number_format($data->clan_win[$clan_names[$wcp->getClan()]]->wins / $record_count * 100,2);
            }
            $data->characters[$wcp->getId()]->win++;

            $data->losses[] = $lcp;
            if (empty($data->clan_lose[$clan_names[$lcp->getClan()]])) {
                $data->clan_lose[$clan_names[$lcp->getClan()]] = new \stdClass();
                $data->clan_lose[$clan_names[$lcp->getClan()]]->losses = 1;
                $data->clan_lose[$clan_names[$lcp->getClan()]]->percentage = number_format($data->clan_lose[$clan_names[$lcp->getClan()]]->losses / $record_count * 100,2);
            } else {
                $data->clan_lose[$clan_names[$lcp->getClan()]]->losses++;
                $data->clan_lose[$clan_names[$lcp->getClan()]]->percentage = number_format($data->clan_lose[$clan_names[$lcp->getClan()]]->losses / $record_count * 100,2);
            }

            $data->characters[$lcp->getId()]->lose++;
            $actions = $this->getDoctrine()
                ->getRepository(actions::class)
                ->findBy(["matchId" => $record[$i]->getMatchId()]);
            CombatUtils::analyzeMatch($actions, $data, $this->em);
        }
        return new Response();
    }
} 
