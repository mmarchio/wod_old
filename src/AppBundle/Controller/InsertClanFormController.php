<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\clan;

class InsertClanFormController extends Controller 
{
    /**
     * @Route("/insert/clan")
     */
    public function insertClanFormAction(Request $request)
    {
        if (!empty($request->getContent())) {
            $clanName = $request->get("clanName");
            $clanWeakness = $request->get("clanWeakness");
            $clanDescription = $request->get("clanDescription");

            $em = $this->getDoctrine()->getManager();

            $clan = new clan;
            $clan->setName($clanName);
            $clan->setWeakness($clanWeakness);
            $clan->setDescription($clanDescription);

            $em->persist($clan);
            $em->flush();
        }

        $data = new \stdClass();
        $data->url = $request->getRequestUri();
        return $this->render('default/insertClan.html.twig',["data" => $data]);
    }
}