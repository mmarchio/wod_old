<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\trait_category;

class InsertCategoryController extends Controller 
{
    /**
     * @Route("/insert/category")
     */
    public function insertCategoryFormAction(Request $request)
    {
        if (!empty($request->getContent())) {
            $categoryName = $request->get("categoryName");
            $em = $this->getDoctrine()->getManager();

            $category = new trait_category();
            $category->setName($categoryName);

            $em->persist($category);
            $em->flush();

        }
        $data = new \stdClass();
        $data->url = $request->getRequestUri();
        return $this->render('default/insertCategory.html.twig',["data" => $data]);
    }
}