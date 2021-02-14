<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\character_profile;
use AppBundle\Entity\character_traits;
use AppBundle\Service\CharacterUtils;

class InsertCharacterFormController extends Controller 
{
    /**
     * @Route("/insert/character")
     */
    public function insertCharacterFormAction(Request $request)
    {
        $doctrine = $this->getDoctrine();
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

            $em = $doctrine->getManager();

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

        $data->clans = $doctrine
            ->getRepository(clan::class)
            ->findAll();

        $data->clan_disciplines = json_encode(CharacterUtils::getClanDisciplines($data->clans, $doctrine));

        $data->attributes->physical = $doctrine
            ->getRepository(trait_entity::class)
            ->findBy(["sub_category" => 1],["trait" => "ASC"]);
        $data->attributes->social = $doctrine
            ->getRepository(trait_entity::class)
            ->findBy(["sub_category" => 2],["trait" => "ASC"]);
        $data->attributes->mental = $doctrine
            ->getRepository(trait_entity::class)
            ->findBy(["sub_category" => 3],["trait" => "ASC"]);

        $data->abilities->talents = $doctrine
            ->getRepository(trait_entity::class)
            ->findBy(["sub_category" => 4],["trait" => "ASC"]);

        $data->abilities->skills = $doctrine
            ->getRepository(trait_entity::class)
            ->findBy(["sub_category" => 5],["trait" => "ASC"]);

        $data->abilities->knowledges = $doctrine
            ->getRepository(trait_entity::class)
            ->findBy(["sub_category" => 6],["trait" => "ASC"]);

        $disciplinesHeader = new \stdClass();
        $disciplinesHeader->id = 0;
        $disciplinesHeader->trait = "Select Discipline";
        $disciplines = $doctrine
            ->getRepository(trait_entity::class)
            ->findBy(["category" => 3], ["trait" => "ASC"]);

        array_unshift($disciplines, $disciplinesHeader);
        $data->disciplines = json_encode($this->toAnon($disciplines));

        $backgrounds = $doctrine
            ->getRepository(trait_entity::class)
            ->findBy(["category" => 4], ["trait" => "ASC"]);

        $backgroundsHeader = new \stdClass();
        $backgroundsHeader->id = 0;
        $backgroundsHeader->trait = "Select Background";

        array_unshift($backgrounds, $backgroundsHeader);
        $data->backgrounds = json_encode($this->toAnon($backgrounds));


        return $this->render('default/insertCharacter.html.twig',["data" => $data]);
    }
}