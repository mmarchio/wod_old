<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class InsertCharacterFormController extends Controller 
{
    /**
     * @Route("/insert/character")
     */
    public function insertCharacterFormAction(Request $request)
    {
        if (!empty($request->getContent())) {
            $character = new \stdClass();
            $character->name = $request->get("characterName");
            $character->player = $request->get("characterPlayer");
            $character->chronicle = $request->get("characterChronicle");
            $character->nature = $request->get("characterNature");
            $character->demeanor = $request->get("characterDemeanor");
            $character->concept = $request->get("characterConcept");
            $character->clan = $request->get("characterClan");
            $character->generation = $request->get("characterGeneration");
            $character->sire = $request->get("characterSire");

            $em = $this->getDoctrine()->getManager();

            $cp = new character_profile();
            $cp->setName($character->name);
            $cp->setPlayer($character->player);
            $cp->setChronicle($character->chronicle);
            $cp->setNature($character->nature);
            $cp->setDemeanor($character->demeanor);
            $cp->setConcept($character->concept);
            $cp->setClan($character->clan);
            $cp->setGeneration($character->generation);
            $cp->setSire($character->sire);

            $em->persist($cp);
            $em->flush();

            $cid = $cp->getId();

            $content = $request->getContent();
            $fields = explode("&", $content);
            $c = count($fields);
            for ($i=0; $i<$c; $i++) {
                if (strpos($fields[$i],"trait_") === 0) {
                    $kv = explode("=", $fields[$i]);
                    $k = explode("_",$kv[0]);
                    $ct = new character_traits();
                    $ct->setTrait($k[1]);
                    $ct->setCharacterProfile($cid);
                    $ct->setValue($kv[1]);
                    $em->persist($ct);
                }
            }
            $em->flush();

        }
        $data = new \stdClass();
        $data->url = $request->getRequestUri();
        $data->stylesUrl = $request->getSchemeAndHttpHost()."/css/styles.css";
        $data->scriptsUrl = $request->getSchemeAndHttpHost()."/js/scripts.js";
        $data->namesUrl = $request->getSchemeAndHttpHost()."/js/names.js";
        $data->attributes = new \stdClass();
        $data->abilities = new \stdClass();
        $data->traits = new \stdClass();

        $data->clans = $this->getDoctrine()
            ->getRepository(clan::class)
            ->findAll();

        $data->clan_disciplines = json_encode($this->getClanDisciplines($data->clans));

        $data->attributes->physical = $this->getDoctrine()
            ->getRepository(trait_entity::class)
            ->findBy(["sub_category" => "1"],["trait" => "ASC"]);
        $data->attributes->social = $this->getDoctrine()
            ->getRepository(trait_entity::class)
            ->findBy(["sub_category" => "2"],["trait" => "ASC"]);
        $data->attributes->mental = $this->getDoctrine()
            ->getRepository(trait_entity::class)
            ->findBy(["sub_category" => "3"],["trait" => "ASC"]);

        $data->abilities->talents = $this->getDoctrine()
            ->getRepository(trait_entity::class)
            ->findBy(["sub_category" => "4"],["trait" => "ASC"]);

        $data->abilities->skills = $this->getDoctrine()
            ->getRepository(trait_entity::class)
            ->findBy(["sub_category" => "5"],["trait" => "ASC"]);

        $data->abilities->knowledges = $this->getDoctrine()
            ->getRepository(trait_entity::class)
            ->findBy(["sub_category" => "6"],["trait" => "ASC"]);

        $disciplinesHeader = new \stdClass();
        $disciplinesHeader->id = 0;
        $disciplinesHeader->trait = "Select Discipline";
        $disciplines = $this->getDoctrine()
            ->getRepository(trait_entity::class)
            ->findBy(["category" => "3"], ["trait" => "ASC"]);

        array_unshift($disciplines, $disciplinesHeader);
        $data->disciplines = json_encode($this->toAnon($disciplines));

        $backgrounds = $this->getDoctrine()
            ->getRepository(trait_entity::class)
            ->findBy(["category" => "4"], ["trait" => "ASC"]);

        $backgroundsHeader = new \stdClass();
        $backgroundsHeader->id = 0;
        $backgroundsHeader->trait = "Select Background";

        array_unshift($backgrounds, $backgroundsHeader);
        $data->backgrounds = json_encode($this->toAnon($backgrounds));


        return $this->render('default/insertCharacter.html.twig',["data" => $data]);
    }
}