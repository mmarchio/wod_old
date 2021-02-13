<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Service\CharacterUtils;

class EditCharacterFormController extends Controller 
{
    /**
     * @Route("/edit/character/{id}")
     */
    public function editCharacterFormAction(Request $request, string $id)
    {
        if (!empty($request->getContent())) {

        }
        $doctrine = $this->getDoctrine();

        $data = CharacterUtils::getCharacterById(
            $request, 
            $id,
            $doctrine
        );

        return $this->render('default/editCharacter.html.twig',["data" => $data]);
    }
}