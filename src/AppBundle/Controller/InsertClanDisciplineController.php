<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\clan_disciplines;

class InsertClanDisciplineController extends Controller 
{
    /**
     * @Route("/insert/clandiscipline")
     */
    public function insertClanDiscipline(Request $request)
    {
        $doctrine = $this->getDoctrine();
        if (!empty($request->getContent())) {
            $clanName = $request->get("clanName");
            $disciplineName = $request->get("disciplineName");

            $em = $doctrine->getManager();

            $cd = new clan_disciplines;
            $cd->setClan($clanName);
            $cd->setTrait($disciplineName);

            $em->persist($cd);
            $em->flush();
        }

        $data = new \stdClass();
        $data->url = $request->getRequestUri();
        $data->clans = $doctrine
            ->getRepository(clan::class)
            ->findAll();
        $data->disciplines = $doctrine
            ->getRepository(trait_entity::class)
            ->findBy(["category" => '3'],["trait" => "ASC"]);
        return $this->render('default/insertClanDiscipline.html.twig',["data" => $data]);
    }
}