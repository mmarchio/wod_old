<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class GenerateCharacterController extends Controller 
{
    /**
     * @Route("/generate/character/{type}/{count}")
     */
    public function generateCharacterAction(Request $request, string $type, int $count)
    {
        $count = intval($count);
        if (!empty($count) && is_int($count)) {
            $em = $this->getDoctrine()->getManager();
            for ($i=0; $i<$count; $i++) {
                /** @var  character_template */
                $ct = $this->generateCharacter($type);
                $cp = new character_profile();
                $cp->setName($ct->getName());
                $cp->setPlayer($ct->getPlayer());
                $cp->setChronicle($ct->getChronicle());
                $cp->setNature($ct->getNature());
                $cp->setDemeanor($ct->getDemeanor());
                $cp->setConcept($ct->getConcept());
                $cp->setClan($ct->getClanId());
                $cp->setGeneration($ct->getGeneration());
                $cp->setSire($ct->getSire());
                $cp->setFreebies($ct->getFreebies());
                $em->persist($cp);
                $em->flush();
                $id = $cp->getId();
                $traits = $ct->getTraits();
                $this->persistTraits($traits, $id, $em);
            }
        }
        $string = $count. " character generated";
        if ($count > 1) {
            $string = $count. " characters generated";
        }
        return new Response($string);
    }
}