<?php
namespace AppBundle\Controller;

use AppBundle\Entity\character_profile;
use AppBundle\Entity\character_traits;
use AppBundle\Entity\clan_disciplines;
use AppBundle\Entity\point_schemas;
use AppBundle\Entity\trait_entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Service\CharacterUtils;

class FreebiesController extends Controller 
{
    /**
     * @Route("/freebies/{id}")
     */
    public function freebiesAction(Request $request, $id)
    {
        $doctrine = $this->getDoctrine();
        $data = CharacterUtils::getCharacterById(
            $request, 
            $id, 
            $doctrine
        );
        $data->character->attributes = new \stdClass();
        $data->character->attributes->physical = $data->character->physical;
        $data->character->attributes->social = $data->character->social;
        $data->character->attributes->mental = $data->character->mental;
        $data->character->abilities = new \stdClass();
        $data->character->abilities->talents = $data->character->talents;
        $data->character->abilities->skills = $data->character->skills;
        $data->character->abilities->knowledges = $data->character->knowledges;
        $data->character->advantages = new \stdClass();
        $data->character->advantages->disciplines = $data->character->disciplines;
        $data->character->advantages->backgrounds = $data->character->backgrounds;
        $data->character->advantages->virtues = [];
        $conscience = new \stdClass();
        $conscience->id = 60;
        $conscience->value = $data->character->virtues->conscience;
        $conscience->name = "conscience";
        $self_control = new \stdClass();
        $self_control->id = 61;
        $self_control->value = $data->character->virtues->self_control;
        $self_control->name = "selfControl";
        $courage = new \stdClass();
        $courage->id = 62;
        $courage->value = $data->character->virtues->courage;
        $courage->name = "courage";
        $data->character->advantages->virtues[] = $conscience;
        $data->character->advantages->virtues[] = $self_control;
        $data->character->advantages->virtues[] = $courage;


        unset($data->character->physical);
        unset($data->character->social);
        unset($data->character->mental);
        unset($data->character->talents);
        unset($data->character->skills);
        unset($data->character->knowledges);
        unset($data->character->disciplines);
        unset($data->character->backgrounds);
        unset($data->character->virtues);
        $creation = CharacterUtils::setCreation(
            $data->character->clan->id, 
            $doctrine
        );

        CharacterUtils::freebies($data->character, $creation);
        $data->character->freebies = $data->character->freebies->target - $data->character->freebies->total;
        if (!empty($data->character->backgrounds->generation)) {
            $data->character->generation = 13 - $data->character->backgrounds->generation->value;
        }
        $em = $doctrine->getManager();
        $cp = $doctrine->getRepository(character_profile::class)
            ->find($id);
        $cp->setName($data->character->name);
        $cp->setPlayer($data->character->player);
        $cp->setChronicle($data->character->chronicle);
        $cp->setNature($data->character->nature);
        $cp->setDemeanor($data->character->demeanor);
        $cp->setConcept($data->character->concept);
        $cp->setClan($data->character->clan->id);
        $cp->setGeneration($data->character->generation);
        $cp->setSire($data->character->sire);
        $cp->setFreebies($data->character->freebies);

        $em->flush();

        foreach ($data->character->attributes as $attribute) {
            foreach ($attribute as $trait) {
                CharacterUtils::persistCharacterTrait($id, $trait->id, $trait->value, $em, $doctrine);
            }
        }
        foreach ($data->character->abilities as $ability) {
            foreach ($ability as $trait) {
                CharacterUtils::persistCharacterTrait($id, $trait->id, $trait->value, $em, $doctrine);
            }
        }
        foreach ($data->character->advantages as $advantage) {
            foreach ($advantage as $trait) {
                CharacterUtils::persistCharacterTrait($id, $trait->id, $trait->value, $em, $doctrine);
            }
        }

        return $this->redirect("/app_dev.php/edit");

    }
}