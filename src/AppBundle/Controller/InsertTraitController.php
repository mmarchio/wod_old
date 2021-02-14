<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\trait_entity;

class InsertTraitController extends Controller 
{
    /**
     * @Route("/insert/trait")
     */
    public function insertTraitFormAction(Request $request)
    {
        $doctrine = $this->getDoctrine();
        if (!empty($request->getContent())) {
            $traitName = $request->get("traitName");
            $traitCategory = $request->get("traitCategory");
            $traitSubCategory = $request->get("traitSubCategory");
            $em = $doctrine->getManager();

            $trait = new trait_entity();
            $trait->setTrait($traitName);
            $trait->setCategory($traitCategory);
            $trait->setSubCategory($traitSubCategory);

            $em->persist($trait);
            $em->flush();
        }
        $data = new \stdClass();
        $data->url = $request->getRequestUri();
        $data->categories = $doctrine
            ->getRepository(trait_category::class)
            ->findAll();
        $data->subcategories = $doctrine
            ->getRepository(trait_sub_category::class)
            ->findAll();
        return $this->render('default/insertTrait.html.twig',["data" => $data]);
    }

}