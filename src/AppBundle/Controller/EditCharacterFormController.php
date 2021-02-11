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
        $characterProfileRepository = $this->getDoctrine()->getRepository(character_profile::class);
        $characterTraitsRepository = $this->getDoctrine()->getRepository(character_traits::class);
        $traitEntityRepository = $this->getDoctrine()->getRepository(trait_entity::class);
        $clanRepository = $this->getDoctrine()->getRepository(clans::class);

        $data = CharacterUtils::getCharacterById(
            $request, 
            $id,
            $characterProfileRepository,
            $characterTraitsRepository,
            $traitEntityRepository,
            $clanRepository
        );

        return $this->render('default/editCharacter.html.twig',["data" => $data]);
    }
}