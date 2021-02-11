<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class InsertTypeController extends Controller 
{
    /**
     * @Route("/insert/type")
     */
    public function insertTypeAction(Request $request)
    {
        $content = $request->getContent();
        if (!empty($content)) {
            $em = $this->getDoctrine()->getManager();

            $type = new types();
            $type->setName($request->get("name"));
            $type->setType($request->get("type"));
            $type->setSubType($request->get("sub_type"));

            $em->persist($type);
            $em->flush();
        }

        $data = new \stdClass();
        $data->url = $request->getRequestUri();
        $data->host = $request->getSchemeAndHttpHost();

        return $this->render('default/insertTypes.html.twig',["data" => $data]);
    }
}