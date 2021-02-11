<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class InsertSubcategoryController extends Controller 
{
    /**
     * @Route("/insert/subcategory")
     */
    public function insertSubCategoryFormAction(Request $request)
    {
        if (!empty($request->getContent())) {
            $subCategoryName = $request->get("subCategoryName");
            $categoryName = $request->get("categoryName");

            $em = $this->getDoctrine()->getManager();

            $category = new trait_sub_category();
            $category->setName($subCategoryName);
            $category->setCategory($categoryName);

            $em->persist($category);
            $em->flush();
        }

        $data = new \stdClass();
        $data->url = $request->getRequestUri();
        $data->categories = $this->getDoctrine()
            ->getRepository(trait_category::class)
            ->findAll();
        return $this->render('default/insertSubCategory.html.twig',["data" => $data]);
    }
}