<?php

namespace AppBundle\Controller;

use AppBundle\Entity\character_profile;
use AppBundle\Entity\character_template;
use AppBundle\Entity\character_traits;
use AppBundle\Entity\clan;
use AppBundle\Entity\clan_disciplines;
use AppBundle\Entity\combat_character;
use AppBundle\Entity\point_schemas;
use AppBundle\Entity\trait_sub_category;
use AppBundle\Entity\types;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\trait_category;
use AppBundle\Entity\trait_entity;
use Doctrine\ORM\EntityManagerInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/insert/trait")
     */
    public function insertTraitFormAction(Request $request)
    {
        if (!empty($request->getContent())) {
            $traitName = $request->get("traitName");
            $traitCategory = $request->get("traitCategory");
            $traitSubCategory = $request->get("traitSubCategory");
            $em = $this->getDoctrine()->getManager();

            $trait = new trait_entity();
            $trait->setTrait($traitName);
            $trait->setCategory($traitCategory);
            $trait->setSubCategory($traitSubCategory);

            $em->persist($trait);
            $em->flush();
        }
        $data = new \stdClass();
        $data->url = $request->getRequestUri();
        $data->categories = $this->getDoctrine()
            ->getRepository(trait_category::class)
            ->findAll();
        $data->subcategories = $this->getDoctrine()
            ->getRepository(trait_sub_category::class)
            ->findAll();
        return $this->render('default/insertTrait.html.twig',["data" => $data]);
    }

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

    /**
     * @Route("/insert/clan")
     */
    public function insertClanFormAction(Request $request)
    {
        if (!empty($request->getContent())) {
            $clanName = $request->get("clanName");
            $clanWeakness = $request->get("clanWeakness");
            $clanDescription = $request->get("clanDescription");

            $em = $this->getDoctrine()->getManager();

            $clan = new clan();
            $clan->setName($clanName);
            $clan->setWeakness($clanWeakness);
            $clan->setDescription($clanDescription);

            $em->persist($clan);
            $em->flush();
        }

        $data = new \stdClass();
        $data->url = $request->getRequestUri();
        return $this->render('default/insertClan.html.twig',["data" => $data]);
    }

    /**
     * @Route("/insert/clandiscipline")
     */
    public function insertClanDiscipline(Request $request)
    {
        if (!empty($request->getContent())) {
            $clanName = $request->get("clanName");
            $disciplineName = $request->get("disciplineName");

            $em = $this->getDoctrine()->getManager();

            $cd = new clan_disciplines();
            $cd->setClan($clanName);
            $cd->setTrait($disciplineName);

            $em->persist($cd);
            $em->flush();
        }

        $data = new \stdClass();
        $data->url = $request->getRequestUri();
        $data->clans = $this->getDoctrine()
            ->getRepository(clan::class)
            ->findAll();
        $data->disciplines = $this->getDoctrine()
            ->getRepository(trait_entity::class)
            ->findBy(["category" => '3'],["trait" => "ASC"]);
        return $this->render('default/insertClanDiscipline.html.twig',["data" => $data]);
    }

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

    protected function toAnon($a)
    {
        $o = [];
        $c = count($a);
        for ($i=0; $i<$c; $i++) {
            if (method_exists($a[$i],"toAnon")) {
                $o[] = $a[$i]->toAnon();
            } else {
                $o[] = $a[$i];
            }
        }
        return $o;
    }

    protected function getClanDisciplines($clans)
    {
        $c = count($clans);
        $a = [];
        for ($i=0; $i<$c; $i++) {
            $clan = new \stdClass();
            $clan->id = $clans[$i]->getId();
            $clan->name = $clans[$i]->getName();
            $clan->disciplines = $this->getDoctrine()
                ->getRepository(clan_disciplines::class)
                ->findBy(["clan" => $clans[$i]->getId()]);
            $clan->disciplines = $this->toAnon($clan->disciplines);
            $a[] = $clan;
        }
        return $a;
    }

    /**
     * @Route("/edit/character/{id}")
     */
    public function editCharacterFormAction(Request $request, string $id)
    {
        if (!empty($request->getContent())) {

        }
        $data = $this->getCharacterById($request, $id);

        return $this->render('default/editCharacter.html.twig',["data" => $data]);
    }

    protected function getCharacterById(Request $request, $id)
    {
        $cp = $this->getDoctrine()
            ->getRepository(character_profile::class)
            ->findBy(["id" => $id]);
        $ct = $this->getDoctrine()
            ->getRepository(character_traits::class)
            ->findBy(["characterProfile" => $id],["trait" => "ASC"]);

        $traits = $this->getDoctrine()
            ->getRepository(trait_entity::class)
            ->findAll();
        $trait_count = count($traits);

        $clans = $this->getDoctrine()
            ->getRepository(clan::class)
            ->findAll();
        $clans_count = count($clans);

        $data = new \stdClass();
        $data->character = new \stdClass();

        $data->character->name = $cp[0]->getName();
        $data->character->player = $cp[0]->getPlayer();
        $data->character->chronicle = $cp[0]->getChronicle();
        $data->character->nature = $cp[0]->getNature();
        $data->character->demeanor = $cp[0]->getDemeanor();
        $data->character->concept = $cp[0]->getConcept();
        $data->character->clan = new \stdClass();
        $data->character->clan->id = $cp[0]->getClan();
        for ($i=0; $i<$clans_count; $i++) {
            if ($clans[$i]->getId() === $data->character->clan->id) {
                $data->character->clan->name = $clans[$i]->getName();
            }
        }
        $data->character->generation = $cp[0]->getGeneration();
        $data->character->sire = $cp[0]->getSire();
        $data->character->freebies = $cp[0]->getFreebies();

        $data->character->physical = [];
        for ($i=0; $i<$trait_count; $i++) {
            if ($traits[$i]->getSubCategory() === 1) {
                $trait = new \stdClass();
                $trait->id = $traits[$i]->getId();
                $trait->value = $this->findTraitValue($trait->id, $ct)->getValue();
                $trait->trait = $traits[$i]->getTrait();
                $data->character->physical[] = $trait;
            }
        }
        $data->character->social = [];
        for ($i=0; $i<$trait_count; $i++) {
            if ($traits[$i]->getSubCategory() === 2) {
                $trait = new \stdClass();
                $trait->id = $traits[$i]->getId();
                $trait->value = $this->findTraitValue($trait->id, $ct)->getValue();
                $trait->trait = $traits[$i]->getTrait();
                $data->character->social[] = $trait;
            }
        }
        $data->character->mental = [];
        for ($i=0; $i<$trait_count; $i++) {
            if ($traits[$i]->getSubCategory() === 3) {
                $trait = new \stdClass();
                $trait->id = $traits[$i]->getId();
                $trait->value = $this->findTraitValue($trait->id, $ct)->getValue();
                $trait->trait = $traits[$i]->getTrait();
                $data->character->mental[] = $trait;
            }
        }
        $data->character->talents = [];
        for ($i=0; $i<$trait_count; $i++) {
            if ($traits[$i]->getSubCategory() === 4) {
                $trait = new \stdClass();
                $trait->id = $traits[$i]->getId();
                $trait->value = $this->findTraitValue($trait->id, $ct)->getValue();
                $trait->trait = $traits[$i]->getTrait();
                $data->character->talents[] = $trait;
            }
        }
        $data->character->skills = [];
        for ($i=0; $i<$trait_count; $i++) {
            if ($traits[$i]->getSubCategory() === 5) {
                $trait = new \stdClass();
                $trait->id = $traits[$i]->getId();
                $trait->value = $this->findTraitValue($trait->id, $ct)->getValue();
                $trait->trait = $traits[$i]->getTrait();
                $data->character->skills[] = $trait;
            }
        }
        $data->character->knowledges = [];
        for ($i=0; $i<$trait_count; $i++) {
            if ($traits[$i]->getSubCategory() === 6) {
                $trait = new \stdClass();
                $trait->id = $traits[$i]->getId();
                $trait->value = $this->findTraitValue($trait->id, $ct)->getValue();
                $trait->trait = $traits[$i]->getTrait();
                $data->character->knowledges[] = $trait;
            }
        }
        $data->character->disciplines = [];
        for ($i=0; $i<$trait_count; $i++) {
            if ($traits[$i]->getCategory() === 3) {
                $v = $this->findTraitValue($traits[$i]->getId(), $ct);
                if (!empty($v)) {
                    $trait = new \stdClass();
                    $trait->id = $traits[$i]->getId();
                    $trait->value = $v->getValue();
                    $trait->name = $traits[$i]->getTrait();
                    $data->character->disciplines[] = $trait;
                }
            }
        }
        $data->character->backgrounds = [];
        for ($i=0; $i<$trait_count; $i++) {
            if ($traits[$i]->getCategory() === 4) {
                $v = $this->findTraitValue($traits[$i]->getId(), $ct);
                if (!empty($v)) {
                    $trait = new \stdClass();
                    $trait->id = $traits[$i]->getId();
                    $trait->value = $v->getValue();
                    $trait->name = $traits[$i]->getTrait();
                    $data->character->backgrounds[] = $trait;
                }
            }
        }
        $data->character->virtues = new \stdClass();
        $data->character->virtues->conscience = $this->findTraitValue(60, $ct)->getValue();
        $data->character->virtues->self_control = $this->findTraitValue(61, $ct)->getValue();
        $data->character->virtues->courage = $this->findTraitValue(62, $ct)->getValue();
        $data->character->path = new \stdClass();
        $data->character->path->id = 0;
        $data->character->path->value = $this->findTraitValue(64, $ct)->getValue();
        $data->character->willpower = $this->findTraitValue(63, $ct)->getValue();
        $data->character->blood_pool = 0;

        $data->stylesUrl = $request->getSchemeAndHttpHost()."/css/styles.css";
        $data->url = $request->getRequestUri();

        $data->clans = $this->toAnon($clans);

        return $data;
    }

    protected function findTraitValue($id, $traits)
    {
        $trait_count = count($traits);
        for ($i=0; $i<$trait_count; $i++) {
            if ($traits[$i]->getTrait() === $id) {
                return $traits[$i];
            }
        }
        return null;
    }

    protected function findTraitByName($name, $traits)
    {
        $trait_count = count($traits);
        for($i=0; $i<$trait_count; $i++) {
            if ($traits[$i]->getTrait() === $name) {
                return $traits[$i];
            }
        }
        return null;
    }

    protected function findTraitById($id, $traits)
    {
        $trait_count = count($traits);
        for($i=0; $i<$trait_count; $i++) {
            if ($traits[$i]->getId() === $id) {
                return $traits[$i];
            }
        }
        return null;
    }

    /**
     * @Route("/edit")
     */
    public function editAction(Request $request)
    {
        $data = new \stdClass();
        $data->stylesUrl = $request->getSchemeAndHttpHost()."/css/styles.css";
        $baseUrl = null;
        if (strpos($request->getRequestUri(),"/app_dev.php") === 0) {
            $baseUrl = "/app_dev.php";
        }
        $characters = $this->getDoctrine()
            ->getRepository(character_profile::class)
            ->findAll();

        $clans = $this->getDoctrine()
            ->getRepository(clan::class)
            ->findAll();

        $clan_count = count($clans);
        $character_count = count($characters);
        $data->characters = [];
        for ($i=0; $i<$character_count; $i++) {
            $temp = new \stdClass();
            $temp->url = $request->getSchemeAndHttpHost().$baseUrl."/edit/character/".$characters[$i]->getId();
            $temp->name = $characters[$i]->getName();
            $temp->clan = "";
            $temp->freebies = $characters[$i]->getFreebies();
            $temp->freebiesUrl = $request->getSchemeAndHttpHost().$baseUrl."/freebies/".$characters[$i]->getId();
            $temp->generation = $characters[$i]->getGeneration();
            for ($a=0; $a<$clan_count; $a++) {
                if ($characters[$i]->getClan() === $clans[$a]->getId()) {
                    $temp->clan = $clans[$a]->getName();
                }
            }
            if (empty($data->characters[$characters[$i]->getClan()])) {
                $data->characters[$characters[$i]->getClan()] = [];
            }
            $data->characters[$characters[$i]->getClan()][] = $temp;
            $data->characters[$characters[$i]->getClan()]['count'] = count($data->characters[$characters[$i]->getClan()]);
            $data->characters[$characters[$i]->getClan()]['summary'] = $temp->clan;
        }

        return $this->render('default/editAll.html.twig',["data" => $data]);
    }

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

    protected function persistTraits(&$traits, $id, &$em)
    {
        $c = count($traits);
        for ($i=0; $i<$c; $i++) {
            $traits[$i]->character_profile = $id;
            $ct = new character_traits();
            $ct->setTrait($traits[$i]->id);
            $ct->setValue($traits[$i]->value);
            $ct->setCharacterProfile($id);
            $em->persist($ct);
            $em->flush();
        }
    }

    protected function generateCharacter($type)
    {
        $ct = $this->setCharacter($type);
        $creation = $this->setCreation($ct->getClanId());
        $ct = $this->setTargets($ct, $creation);
        $ct->setFreebies($creation->selected->getFreebies());
        $this->generateGroup($ct->getAttributes()->physical, $this->getKeyLists($ct->getAttributes()->physical), $creation->traits);
        if ($ct->getClan() === "nosferatu") {
            $this->generateGroup($ct->getAttributes()->social, ["charisma", "manipulation"], $creation->traits);
        } else {
            $this->generateGroup($ct->getAttributes()->social, $this->getKeyLists($ct->getAttributes()->social), $creation->traits);
        }
        $this->generateGroup($ct->getAttributes()->social, $this->getKeyLists($ct->getAttributes()->social), $creation->traits);
        $this->generateGroup($ct->getAttributes()->mental, $this->getKeyLists($ct->getAttributes()->mental), $creation->traits);
        $this->generateGroup($ct->getAbilities()->talents, $this->getKeyLists($ct->getAbilities()->talents), $creation->traits);
        $this->generateGroup($ct->getAbilities()->skills, $this->getKeyLists($ct->getAbilities()->skills), $creation->traits);
        $this->generateGroup($ct->getAbilities()->knowledges, $this->getKeyLists($ct->getAbilities()->knowledges), $creation->traits);
        if ($ct->getClan() !== "caitiff") {
            $this->generateGroup($ct->getAdvantages()->disciplines, $this->getDisciplineList($creation->clanDisciplines, $creation->traits), $creation->traits);
        }
        $this->generateGroup($ct->getAdvantages()->backgrounds, $this->getBackgroundsList($creation->backgrounds, $creation->traits), $creation->traits);
        $this->generateGroup($ct->getAdvantages()->virtues, $this->getKeyLists($ct->getAdvantages()->virtues), $creation->traits);
        $backgrounds = $ct->getAdvantages()->backgrounds;
        if (property_exists($backgrounds, "generation")) {
            $ct->setGeneration(($ct->getGeneration() - $backgrounds->generation->value));
        }
        return $ct;
    }

    protected function getKeyLists($group)
    {
        $a = [];
        foreach ($group as $k => $v) {
            if ($k !== "total" && $k !== "target") {
                $a[] = $k;
            }
        }
        return $a;
    }

    protected function getDisciplineList($group, $traits)
    {
        $a = [];
        foreach ($group as $k => $v) {
            if ($k !== "total" && $k !== "target") {
                $a[] = $this->findTraitById($v->getTrait(), $traits)->getTrait();
            }
        }
        return $a;
    }

    protected function getBackgroundsList($group, $traits)
    {
        $a = [];
        foreach ($group as $k => $v) {
            if ($k !== "total" && $k !== "target") {
                $a[] = $v->getTrait();
            }
        }
        return $a;
    }

    protected function generateGroup($group, $items, $traits)
    {
        while ($group->total < $group->target) {
            $n = rand(0, count($items)-1);
            if (empty($group->{$items[$n]})) {
                $group->{$items[$n]} = new \stdClass();
                $trait = $this->findTraitByName($items[$n],$traits);
                $group->{$items[$n]}->id = $this->findTraitByName($items[$n],$traits)->getId();
                $group->{$items[$n]}->value = 1;
                $group->{$items[$n]}->name = $items[$n];
                $group->total++;
            } else {
                if ($group->{$items[$n]}->value < 5) {
                    $group->{$items[$n]}->value++;
                    $group->total++;
                }
            }
        }
        return $group;
    }

    protected function setTargets(character_template $ct, $creation)
    {
        $ct = $this->pst_attributes($ct, $creation);
        $ct = $this->pst_abilities($ct, $creation);
        $a = $ct->getAdvantages();
        $a->disciplines->target = $creation->selected->getAdvantagesSpecial();
        $a->backgrounds->target = $creation->selected->getAdvantagesBackgrounds();
        $a->virtues->target = $creation->selected->getAdvantagesVirtues();
        $ct->setAdvantages($a);

        return $ct;
    }

    protected function pst_attributes(character_template $ct, $creation)
    {
        $attributes = [
            "physical",
            "social",
            "mental"
        ];

        $pst = $this->pst_setter();
        $pst->p = $attributes[$pst->p];
        $pst->s = $attributes[$pst->s];
        $pst->t = $attributes[$pst->t];

        $a = $ct->getAttributes();
        $a->{$pst->p}->target = $creation->selected->getAttributePrimary();
        $a->{$pst->s}->target = $creation->selected->getAttributeSecondary();
        $a->{$pst->t}->target = $creation->selected->getAttributeTertiary();

        $ct->setAttributes($a);

        return $ct;
    }
    
    protected function pst_abilities(character_template $ct, $creation)
    {
        $abilities = [
            "talents",
            "skills",
            "knowledges"
        ];

        $pst = $this->pst_setter();
        $pst->p = $abilities[$pst->p];
        $pst->s = $abilities[$pst->s];
        $pst->t = $abilities[$pst->t];

        $a = $ct->getAbilities();
        $a->{$pst->p}->target = $creation->selected->getAbilityPrimary();
        $a->{$pst->s}->target = $creation->selected->getAbilitySecondary();
        $a->{$pst->t}->target = $creation->selected->getAbilityTertiary();

        $ct->setAbilities($a);

        return $ct;
    }

    protected function pst_setter()
    {
        $pst = [];
        $temp = new \stdClass();
        $temp->p = 0;
        $temp->s = 1;
        $temp->t = 2;
        $pst[] = $temp;
        $temp = new \stdClass();
        $temp->p = 0;
        $temp->s = 2;
        $temp->t = 1;
        $pst[] = $temp;
        $temp = new \stdClass();
        $temp->p = 1;
        $temp->s = 0;
        $temp->t = 2;
        $pst[] = $temp;
        $temp = new \stdClass();
        $temp->p = 1;
        $temp->s = 2;
        $temp->t = 0;
        $pst[] = $temp;
        $temp = new \stdClass();
        $temp->p = 2;
        $temp->s = 0;
        $temp->t = 1;
        $pst[] = $temp;
        $temp = new \stdClass();
        $temp->p = 2;
        $temp->s = 1;
        $temp->t = 0;
        $pst[] = $temp;

        $r = rand(0,5);
        return $pst[$r];
    }

    protected function setCreation($clan)
    {
        $creation = new \stdClass();
        $creation->points = $this->getDoctrine()
            ->getRepository(point_schemas::class)
            ->findAll();
        $creation->traits = $this->getDoctrine()
            ->getRepository(trait_entity::class)
            ->findAll();
        $creation->attributes = new \stdClass();
        $creation->attributes->physical = $this->findTrait($creation->traits,1, 1);
        $creation->attributes->social = $this->findTrait($creation->traits,1, 2);
        $creation->attributes->mental = $this->findTrait($creation->traits,1, 3);
        $creation->abilities = new \stdClass();
        $creation->abilities->talents = $this->findTrait($creation->traits,2, 4);
        $creation->abilities->skills = $this->findTrait($creation->traits,2, 5);
        $creation->abilities->knowledges = $this->findTrait($creation->traits,2, 6);
        $creation->clans = $this->getDoctrine()
            ->getRepository(clan::class)
            ->findAll();
        $creation->backgrounds = $this->findTrait($creation->traits,4, 0);
        $creation->virtues = $this->findTrait($creation->traits,5, 0);
        $creation->clanDisciplines = $this->getDoctrine()
            ->getRepository(clan_disciplines::class)
            ->findBy(["clan" => $clan]);
        $pc = count($creation->points) - 1;
        $r = rand(0,$pc);
        $creation->selected = $creation->points[$r];

        return $creation;
    }

    protected function setCharacter($type)
    {
        $ct = new character_template();
        switch ($type) {
            case "kindred":
                $names = new \stdClass();
                $names->m = ["James","John","Robert","Michael","William","David","Richard","Charles","Joseph","Thomas","Christopher","Daniel","Paul","Mark","Donald","George","Kenneth","Steven","Edward","Brian","Ronald","Anthony","Kevin","Jason","Matthew","Gary","Timothy","Jose","Larry","Jeffrey","Frank","Scott","Eric","Stephen","Andrew","Raymond","Gregory","Joshua","Jerry","Dennis","Walter","Patrick","Peter","Harold","Douglas","Henry","Carl","Arthur","Ryan","Roger","Joe","Juan","Jack","Albert","Jonathan","Justin","Terry","Gerald","Keith","Samuel","Willie","Ralph","Lawrence","Nicholas","Roy","Benjamin","Bruce","Brandon","Adam","Harry","Fred","Wayne","Billy","Steve","Louis","Jeremy","Aaron","Randy","Howard","Eugene","Carlos","Russell","Bobby","Victor","Martin","Ernest","Phillip","Todd","Jesse","Craig","Alan","Shawn","Clarence","Sean","Philip","Chris","Johnny","Earl","Jimmy","Antonio","Danny","Bryan","Tony","Luis","Mike","Stanley","Leonard","Nathan","Dale","Manuel","Rodney","Curtis","Norman","Allen","Marvin","Vincent","Glenn","Jeffery","Travis","Jeff","Chad","Jacob","Lee","Melvin","Alfred","Kyle","Francis","Bradley","Jesus","Herbert","Frederick","Ray","Joel","Edwin","Don","Eddie","Ricky","Troy","Randall","Barry","Alexander","Bernard","Mario","Leroy","Francisco","Marcus","Micheal","Theodore","Clifford","Miguel","Oscar","Jay","Jim","Tom","Calvin","Alex","Jon","Ronnie","Bill","Lloyd","Tommy","Leon","Derek","Warren","Darrell","Jerome","Floyd","Leo","Alvin","Tim","Wesley","Gordon","Dean","Greg","Jorge","Dustin","Pedro","Derrick","Dan","Lewis","Zachary","Corey","Herman","Maurice","Vernon","Roberto","Clyde","Glen","Hector","Shane","Ricardo","Sam","Rick","Lester","Brent","Ramon","Charlie","Tyler","Gilbert","Gene","Marc","Reginald","Ruben","Brett","Angel","Nathaniel","Rafael","Leslie","Edgar","Milton","Raul","Ben","Chester","Cecil","Duane","Franklin","Andre","Elmer","Brad","Gabriel","Ron","Mitchell","Roland","Arnold","Harvey","Jared","Adrian","Karl","Cory","Claude","Erik","Darryl","Jamie","Neil","Jessie","Christian","Javier","Fernando","Clinton","Ted","Mathew","Tyrone","Darren","Lonnie","Lance","Cody","Julio","Kelly","Kurt","Allan","Nelson","Guy","Clayton","Hugh","Max","Dwayne","Dwight","Armando","Felix","Jimmie","Everett","Jordan","Ian","Wallace","Ken","Bob","Jaime","Casey","Alfredo","Alberto","Dave","Ivan","Johnnie","Sidney","Byron","Julian","Isaac","Morris","Clifton","Willard","Daryl","Ross","Virgil","Andy","Marshall","Salvador","Perry","Kirk","Sergio","Marion","Tracy","Seth","Kent","Terrance","Rene","Eduardo","Terrence","Enrique","Freddie","Wade","Austin","Stuart","Fredrick","Arturo","Alejandro","Jackie","Joey","Nick","Luther","Wendell","Jeremiah","Evan","Julius","Dana","Donnie","Otis","Shannon","Trevor","Oliver","Luke","Homer","Gerard","Doug","Kenny","Hubert","Angelo","Shaun","Lyle","Matt","Lynn","Alfonso","Orlando","Rex","Carlton","Ernesto","Cameron","Neal","Pablo","Lorenzo","Omar","Wilbur","Blake","Grant","Horace","Roderick","Kerry","Abraham","Willis","Rickey","Jean","Ira","Andres","Cesar","Johnathan","Malcolm","Rudolph","Damon","Kelvin","Rudy","Preston","Alton","Archie","Marco","Wm","Pete","Randolph","Garry","Geoffrey","Jonathon","Felipe","Bennie","Gerardo","Ed","Dominic","Robin","Loren","Delbert","Colin","Guillermo","Earnest","Lucas","Benny","Noel","Spencer","Rodolfo","Myron","Edmund","Garrett","Salvatore","Cedric","Lowell","Gregg","Sherman","Wilson","Devin","Sylvester","Kim","Roosevelt","Israel","Jermaine","Forrest","Wilbert","Leland","Simon","Guadalupe","Clark","Irving","Carroll","Bryant","Owen","Rufus","Woodrow","Sammy","Kristopher","Mack","Levi","Marcos","Gustavo","Jake","Lionel","Marty","Taylor","Ellis","Dallas","Gilberto","Clint","Nicolas","Laurence","Ismael","Orville","Drew","Jody","Ervin","Dewey","Al","Wilfred","Josh","Hugo","Ignacio","Caleb","Tomas","Sheldon","Erick","Frankie","Stewart","Doyle","Darrel","Rogelio","Terence","Santiago","Alonzo","Elias","Bert","Elbert","Ramiro","Conrad","Pat","Noah","Grady","Phil","Cornelius","Lamar","Rolando","Clay","Percy","Dexter","Bradford","Merle","Darin","Amos","Terrell","Moses","Irvin","Saul","Roman","Darnell","Randal","Tommie","Timmy","Darrin","Winston","Brendan","Toby","Van","Abel","Dominick","Boyd","Courtney","Jan","Emilio","Elijah","Cary","Domingo","Santos","Aubrey","Emmett","Marlon","Emanuel","Jerald","Edmond","Emil","Dewayne","Will","Otto","Teddy","Reynaldo","Bret","Morgan","Jess","Trent","Humberto","Emmanuel","Stephan","Louie","Vicente","Lamont","Stacy","Garland","Miles","Micah","Efrain","Billie","Logan","Heath","Rodger","Harley","Demetrius","Ethan","Eldon","Rocky","Pierre","Junior","Freddy","Eli","Bryce","Antoine","Robbie","Kendall","Royce","Sterling","Mickey","Chase","Grover","Elton","Cleveland","Dylan","Chuck","Damian","Reuben","Stan","August","Leonardo","Jasper","Russel","Erwin","Benito","Hans","Monte","Blaine","Ernie","Curt","Quentin","Agustin","Murray","Jamal","Devon","Adolfo","Harrison","Tyson","Burton","Brady","Elliott","Wilfredo","Bart","Jarrod","Vance","Denis","Damien","Joaquin","Harlan","Desmond","Elliot","Darwin","Ashley","Gregorio","Buddy","Xavier","Kermit","Roscoe","Esteban","Anton","Solomon","Scotty","Norbert","Elvin","Williams","Nolan","Carey","Rod","Quinton","Hal","Brain","Rob","Elwood","Kendrick","Darius","Moises","Son","Marlin","Fidel","Thaddeus","Cliff","Marcel","Ali","Jackson","Raphael","Bryon","Armand","Alvaro","Jeffry","Dane","Joesph","Thurman","Ned","Sammie","Rusty","Michel","Monty","Rory","Fabian","Reggie","Mason","Graham","Kris","Isaiah","Vaughn","Gus","Avery","Loyd","Diego","Alexis","Adolph","Norris","Millard","Rocco","Gonzalo","Derick","Rodrigo","Gerry","Stacey","Carmen","Wiley","Rigoberto","Alphonso","Ty","Shelby","Rickie","Noe","Vern","Bobbie","Reed","Jefferson","Elvis","Bernardo","Mauricio","Hiram","Donovan","Basil","Riley","Ollie","Nickolas","Maynard","Scot","Vince","Quincy","Eddy","Sebastian","Federico","Ulysses","Heriberto","Donnell","Cole","Denny","Davis","Gavin","Emery","Ward","Romeo","Jayson","Dion","Dante","Clement","Coy","Odell","Maxwell","Jarvis","Bruno","Issac","Mary","Dudley","Brock","Sanford","Colby","Carmelo","Barney","Nestor","Hollis","Stefan","Donny","Art","Linwood","Beau","Weldon","Galen","Isidro","Truman","Delmar","Johnathon","Silas","Frederic","Dick","Kirby","Irwin","Cruz","Merlin","Merrill","Charley","Marcelino","Lane","Harris","Cleo","Carlo","Trenton","Kurtis","Hunter","Aurelio","Winfred","Vito","Collin","Denver","Carter","Leonel","Emory","Pasquale","Mohammad","Mariano","Danial","Blair","Landon","Dirk","Branden","Adan","Numbers","Clair","Buford","German","Bernie","Wilmer","Joan","Emerson","Zachery","Fletcher","Jacques","Errol","Dalton","Monroe","Josue","Dominique","Edwardo","Booker","Wilford","Sonny","Shelton","Carson","Theron","Raymundo","Daren","Tristan","Houston","Robby","Lincoln","Jame","Genaro","Gale","Bennett","Octavio","Cornell","Laverne","Hung","Arron","Antony","Herschel","Alva","Giovanni","Garth","Cyrus","Cyril","Ronny","Stevie","Lon","Freeman","Erin","Duncan","Kennith","Carmine","Augustine","Young","Erich","Chadwick","Wilburn","Russ","Reid","Myles","Anderson","Morton","Jonas","Forest","Mitchel","Mervin","Zane","Rich","Jamel","Lazaro","Alphonse","Randell","Major","Johnie","Jarrett","Brooks","Ariel","Abdul","Dusty","Luciano","Lindsey","Tracey","Seymour","Scottie","Eugenio","Mohammed","Sandy","Valentin","Chance","Arnulfo","Lucien","Ferdinand","Thad","Ezra","Sydney","Aldo","Rubin","Royal","Mitch","Earle","Abe","Wyatt","Marquis","Lanny","Kareem","Jamar","Boris","Isiah","Emile","Elmo","Aron","Leopoldo","Everette","Josef","Gail","Eloy","Dorian","Rodrick","Reinaldo","Lucio","Jerrod","Weston","Hershel","Barton","Parker","Lemuel","Lavern","Burt","Jules","Gil","Eliseo","Ahmad","Nigel","Efren","Antwan","Alden","Margarito","Coleman","Refugio","Dino","Osvaldo","Les","Deandre","Normand","Kieth","Ivory","Andrea","Trey","Norberto","Napoleon","Jerold","Fritz","Rosendo","Milford","Sang","Deon","Christoper","Alfonzo","Lyman","Josiah","Brant","Wilton","Rico","Jamaal","Dewitt","Carol","Brenton","Yong","Olin","Foster","Faustino","Claudio","Judson","Gino","Edgardo","Berry","Alec","Tanner","Jarred","Donn","Trinidad","Tad","Shirley","Prince","Porfirio","Odis","Maria","Lenard","Chauncey","Chang","Tod","Mel","Marcelo","Kory","Augustus","Keven","Hilario","Bud","Sal","Rosario","Orval","Mauro","Dannie","Zachariah","Olen","Anibal","Milo","Jed","Frances","Thanh","Dillon","Amado","Newton","Connie","Lenny","Tory","Richie","Lupe","Horacio","Brice","Mohamed","Delmer","Dario","Reyes","Dee","Mac","Jonah","Jerrold","Robt","Hank","Sung","Rupert","Rolland","Kenton","Damion","Chi","Antone","Waldo","Fredric","Bradly","Quinn","Kip","Burl","Walker","Tyree","Jefferey","Ahmed"];
                $names->f = ["Mary","Patricia","Linda","Barbara","Elizabeth","Jennifer","Maria","Susan","Margaret","Dorothy","Lisa","Nancy","Karen","Betty","Helen","Sandra","Donna","Carol","Ruth","Sharon","Michelle","Laura","Sarah","Kimberly","Deborah","Jessica","Shirley","Cynthia","Angela","Melissa","Brenda","Amy","Anna","Rebecca","Virginia","Kathleen","Pamela","Martha","Debra","Amanda","Stephanie","Carolyn","Christine","Marie","Janet","Catherine","Frances","Ann","Joyce","Diane","Alice","Julie","Heather","Teresa","Doris","Gloria","Evelyn","Jean","Cheryl","Mildred","Katherine","Joan","Ashley","Judith","Rose","Janice","Kelly","Nicole","Judy","Christina","Kathy","Theresa","Beverly","Denise","Tammy","Irene","Jane","Lori","Rachel","Marilyn","Andrea","Kathryn","Louise","Sara","Anne","Jacqueline","Wanda","Bonnie","Julia","Ruby","Lois","Tina","Phyllis","Norma","Paula","Diana","Annie","Lillian","Emily","Robin","Peggy","Crystal","Gladys","Rita","Dawn","Connie","Florence","Tracy","Edna","Tiffany","Carmen","Rosa","Cindy","Grace","Wendy","Victoria","Edith","Kim","Sherry","Sylvia","Josephine","Thelma","Shannon","Sheila","Ethel","Ellen","Elaine","Marjorie","Carrie","Charlotte","Monica","Esther","Pauline","Emma","Juanita","Anita","Rhonda","Hazel","Amber","Eva","Debbie","April","Leslie","Clara","Lucille","Jamie","Joanne","Eleanor","Valerie","Danielle","Megan","Alicia","Suzanne","Michele","Gail","Bertha","Darlene","Veronica","Jill","Erin","Geraldine","Lauren","Cathy","Joann","Lorraine","Lynn","Sally","Regina","Erica","Beatrice","Dolores","Bernice","Audrey","Yvonne","Annette","June","Samantha","Marion","Dana","Stacy","Ana","Renee","Ida","Vivian","Roberta","Holly","Brittany","Melanie","Loretta","Yolanda","Jeanette","Laurie","Katie","Kristen","Vanessa","Alma","Sue","Elsie","Beth","Jeanne","Vicki","Carla","Tara","Rosemary","Eileen","Terri","Gertrude","Lucy","Tonya","Ella","Stacey","Wilma","Gina","Kristin","Jessie","Natalie","Agnes","Vera","Willie","Charlene","Bessie","Delores","Melinda","Pearl","Arlene","Maureen","Colleen","Allison","Tamara","Joy","Georgia","Constance","Lillie","Claudia","Jackie","Marcia","Tanya","Nellie","Minnie","Marlene","Heidi","Glenda","Lydia","Viola","Courtney","Marian","Stella","Caroline","Dora","Jo","Vickie","Mattie","Terry","Maxine","Irma","Mabel","Marsha","Myrtle","Lena","Christy","Deanna","Patsy","Hilda","Gwendolyn","Jennie","Nora","Margie","Nina","Cassandra","Leah","Penny","Kay","Priscilla","Naomi","Carole","Brandy","Olga","Billie","Dianne","Tracey","Leona","Jenny","Felicia","Sonia","Miriam","Velma","Becky","Bobbie","Violet","Kristina","Toni","Misty","Mae","Shelly","Daisy","Ramona","Sherri","Erika","Katrina","Claire","Lindsey","Lindsay","Geneva","Guadalupe","Belinda","Margarita","Sheryl","Cora","Faye","Ada","Natasha","Sabrina","Isabel","Marguerite","Hattie","Harriet","Molly","Cecilia","Kristi","Brandi","Blanche","Sandy","Rosie","Joanna","Iris","Eunice","Angie","Inez","Lynda","Madeline","Amelia","Alberta","Genevieve","Monique","Jodi","Janie","Maggie","Kayla","Sonya","Jan","Lee","Kristine","Candace","Fannie","Maryann","Opal","Alison","Yvette","Melody","Luz","Susie","Olivia","Flora","Shelley","Kristy","Mamie","Lula","Lola","Verna","Beulah","Antoinette","Candice","Juana","Jeannette","Pam","Kelli","Hannah","Whitney","Bridget","Karla","Celia","Latoya","Patty","Shelia","Gayle","Della","Vicky","Lynne","Sheri","Marianne","Kara","Jacquelyn","Erma","Blanca","Myra","Leticia","Pat","Krista","Roxanne","Angelica","Johnnie","Robyn","Francis","Adrienne","Rosalie","Alexandra","Brooke","Bethany","Sadie","Bernadette","Traci","Jody","Kendra","Jasmine","Nichole","Rachael","Chelsea","Mable","Ernestine","Muriel","Marcella","Elena","Krystal","Angelina","Nadine","Kari","Estelle","Dianna","Paulette","Lora","Mona","Doreen","Rosemarie","Angel","Desiree","Antonia","Hope","Ginger","Janis","Betsy","Christie","Freda","Mercedes","Meredith","Lynette","Teri","Cristina","Eula","Leigh","Meghan","Sophia","Eloise","Rochelle","Gretchen","Cecelia","Raquel","Henrietta","Alyssa","Jana","Kelley","Gwen","Kerry","Jenna","Tricia","Laverne","Olive","Alexis","Tasha","Silvia","Elvira","Casey","Delia","Sophie","Kate","Patti","Lorena","Kellie","Sonja","Lila","Lana","Darla","May","Mindy","Essie","Mandy","Lorene","Elsa","Josefina","Jeannie","Miranda","Dixie","Lucia","Marta","Faith","Lela","Johanna","Shari","Camille","Tami","Shawna","Elisa","Ebony","Melba","Ora","Nettie","Tabitha","Ollie","Jaime","Winifred","Kristie","Marina","Alisha","Aimee","Rena","Myrna","Marla","Tammie","Latasha","Bonita","Patrice","Ronda","Sherrie","Addie","Francine","Deloris","Stacie","Adriana","Cheri","Shelby","Abigail","Celeste","Jewel","Cara","Adele","Rebekah","Lucinda","Dorthy","Chris","Effie","Trina","Reba","Shawn","Sallie","Aurora","Lenora","Etta","Lottie","Kerri","Trisha","Nikki","Estella","Francisca","Josie","Tracie","Marissa","Karin","Brittney","Janelle","Lourdes","Laurel","Helene","Fern","Elva","Corinne","Kelsey","Ina","Bettie","Elisabeth","Aida","Caitlin","Ingrid","Iva","Eugenia","Christa","Goldie","Cassie","Maude","Jenifer","Therese","Frankie","Dena","Lorna","Janette","Latonya","Candy","Morgan","Consuelo","Tamika","Rosetta","Debora","Cherie","Polly","Dina","Jewell","Fay","Jillian","Dorothea","Nell","Trudy","Esperanza","Patrica","Kimberley","Shanna","Helena","Carolina","Cleo","Stefanie","Rosario","Ola","Janine","Mollie","Lupe","Alisa","Lou","Maribel","Susanne","Bette","Susana","Elise","Cecile","Isabelle","Lesley","Jocelyn","Paige","Joni","Rachelle","Leola","Daphne","Alta","Ester","Petra","Graciela","Imogene","Jolene","Keisha","Lacey","Glenna","Gabriela","Keri","Ursula","Lizzie","Kirsten","Shana","Adeline","Mayra","Jayne","Jaclyn","Gracie","Sondra","Carmela","Marisa","Rosalind","Charity","Tonia","Beatriz","Marisol","Clarice","Jeanine","Sheena","Angeline","Frieda","Lily","Robbie","Shauna","Millie","Claudette","Cathleen","Angelia","Gabrielle","Autumn","Katharine","Summer","Jodie","Staci","Lea","Christi","Jimmie","Justine","Elma","Luella","Margret","Dominique","Socorro","Rene","Martina","Margo","Mavis","Callie","Bobbi","Maritza","Lucile","Leanne","Jeannine","Deana","Aileen","Lorie","Ladonna","Willa","Manuela","Gale","Selma","Dolly","Sybil","Abby","Lara","Dale","Ivy","Dee","Winnie","Marcy","Luisa","Jeri","Magdalena","Ofelia","Meagan","Audra","Matilda","Leila","Cornelia","Bianca","Simone","Bettye","Randi","Virgie","Latisha","Barbra","Georgina","Eliza","Leann","Bridgette","Rhoda","Haley","Adela","Nola","Bernadine","Flossie","Ila","Greta","Ruthie","Nelda","Minerva","Lilly","Terrie","Letha","Hilary","Estela","Valarie","Brianna","Rosalyn","Earline","Catalina","Ava","Mia","Clarissa","Lidia","Corrine","Alexandria","Concepcion","Tia","Sharron","Rae","Dona","Ericka","Jami","Elnora","Chandra","Lenore","Neva","Marylou","Melisa","Tabatha","Serena","Avis","Allie","Sofia","Jeanie","Odessa","Nannie","Harriett","Loraine","Penelope","Milagros","Emilia","Benita","Allyson","Ashlee","Tania","Tommie","Esmeralda","Karina","Eve","Pearlie","Zelma","Malinda","Noreen","Tameka","Saundra","Hillary","Amie","Althea","Rosalinda","Jordan","Lilia","Alana","Gay","Clare","Alejandra","Elinor","Michael","Lorrie","Jerri","Darcy","Earnestine","Carmella","Taylor","Noemi","Marcie","Liza","Annabelle","Louisa","Earlene","Mallory","Carlene","Nita","Selena","Tanisha","Katy","Julianne","John","Lakisha","Edwina","Maricela","Margery","Kenya","Dollie","Roxie","Roslyn","Kathrine","Nanette","Charmaine","Lavonne","Ilene","Kris","Tammi","Suzette","Corine","Kaye","Jerry","Merle","Chrystal","Lina","Deanne","Lilian","Juliana","Aline","Luann","Kasey","Maryanne","Evangeline","Colette","Melva","Lawanda","Yesenia","Nadia","Madge","Kathie","Eddie","Ophelia","Valeria","Nona","Mitzi","Mari","Georgette","Claudine","Fran","Alissa","Roseann","Lakeisha","Susanna","Reva","Deidre","Chasity","Sheree","Carly","James","Elvia","Alyce","Deirdre","Gena","Briana","Araceli","Katelyn","Rosanne","Wendi","Tessa","Berta","Marva","Imelda","Marietta","Marci","Leonor","Arline","Sasha","Madelyn","Janna","Juliette","Deena","Aurelia","Josefa","Augusta","Liliana","Young","Christian","Lessie","Amalia","Savannah","Anastasia","Vilma","Natalia","Rosella","Lynnette","Corina","Alfreda","Leanna","Carey","Amparo","Coleen","Tamra","Aisha","Wilda","Karyn","Cherry","Queen","Maura","Mai","Evangelina","Rosanna","Hallie","Erna","Enid","Mariana","Lacy","Juliet","Jacklyn","Freida","Madeleine","Mara","Hester","Cathryn","Lelia","Casandra","Bridgett","Angelita","Jannie","Dionne","Annmarie","Katina","Beryl","Phoebe","Millicent","Katheryn","Diann","Carissa","Maryellen","Liz","Lauri","Helga","Gilda","Adrian","Rhea","Marquita","Hollie","Tisha","Tamera","Angelique","Francesca","Britney","Kaitlin","Lolita","Florine","Rowena","Reyna","Twila","Fanny","Janell","Ines","Concetta","Bertie","Alba","Brigitte","Alyson","Vonda","Pansy","Elba","Noelle","Letitia","Kitty","Deann","Brandie","Louella","Leta","Felecia","Sharlene","Lesa","Beverley","Robert","Isabella","Herminia","Terra","Celina"];
                $names->l = ["Smith","Johnson","Williams","Brown","Jones","Miller","Davis","Garcia","Rodriguez","Wilson","Martinez","Anderson","Taylor","Thomas","Hernandez","Moore","Martin","Jackson","Thompson","White","Lopez","Lee","Gonzalez","Harris","Clark","Lewis","Robinson","Walker","Perez","Hall","Young","Allen","Sanchez","Wright","King","Scott","Green","Baker","Adams","Nelson","Hill","Ramirez","Campbell","Mitchell","Roberts","Carter","Phillips","Evans","Turner","Torres","Parker","Collins","Edwards","Stewart","Flores","Morris","Nguyen","Murphy","Rivera","Cook","Rogers","Morgan","Peterson","Cooper","Reed","Bailey","Bell","Gomez","Kelly","Howard","Ward","Cox","Diaz","Richardson","Wood","Watson","Brooks","Bennett","Gray","James","Reyes","Cruz","Hughes","Price","Myers","Long","Foster","Sanders","Ross","Morales","Powell","Sullivan","Russell","Ortiz","Jenkins","Gutierrez","Perry","Butler","Barnes","Fisher","Henderson","Coleman","Simmons","Patterson","Jordan","Reynolds","Hamilton","Graham","Kim","Gonzales","Alexander","Ramos","Wallace","Griffin","West","Cole","Hayes","Chavez","Gibson","Bryant","Ellis","Stevens","Murray","Ford","Marshall","Owens","Mcdonald","Harrison","Ruiz","Kennedy","Wells","Alvarez","Woods","Mendoza","Castillo","Olson","Webb","Washington","Tucker","Freeman","Burns","Henry","Vasquez","Snyder","Simpson","Crawford","Jimenez","Porter","Mason","Shaw","Gordon","Wagner","Hunter","Romero","Hicks","Dixon","Hunt","Palmer","Robertson","Black","Holmes","Stone","Meyer","Boyd","Mills","Warren","Fox","Rose","Rice","Moreno","Schmidt","Patel","Ferguson","Nichols","Herrera","Medina","Ryan","Fernandez","Weaver","Daniels","Stephens","Gardner","Payne","Kelley","Dunn","Pierce","Arnold","Tran","Spencer","Peters","Hawkins","Grant","Hansen","Castro","Hoffman","Hart","Elliott","Cunningham","Knight","Bradley","Carroll","Hudson","Duncan","Armstrong","Berry","Andrews","Johnston","Ray","Lane","Riley","Carpenter","Perkins","Aguilar","Silva","Richards","Willis","Matthews","Chapman","Lawrence","Garza","Vargas","Watkins","Wheeler","Larson","Carlson","Harper","George","Greene","Burke","Guzman","Morrison","Munoz","Jacobs","Obrien","Lawson","Franklin","Lynch","Bishop","Carr","Salazar","Austin","Mendez","Gilbert","Jensen","Williamson","Montgomery","Harvey","Oliver","Howell","Dean","Hanson","Weber","Garrett","Sims","Burton","Fuller","Soto","Mccoy","Welch","Chen","Schultz","Walters","Reid","Fields","Walsh","Little","Fowler","Bowman","Davidson","May","Day","Schneider","Newman","Brewer","Lucas","Holland","Wong","Banks","Santos","Curtis","Pearson","Delgado","Valdez","Pena","Rios","Douglas","Sandoval","Barrett","Hopkins","Keller","Guerrero","Stanley","Bates","Alvarado","Beck","Ortega","Wade","Estrada","Contreras","Barnett","Caldwell","Santiago","Lambert","Powers","Chambers","Nunez","Craig","Leonard","Lowe","Rhodes","Byrd","Gregory","Shelton","Frazier","Becker","Maldonado","Fleming","Vega","Sutton","Cohen","Jennings","Parks","Mcdaniel","Watts","Barker","Norris","Vaughn","Vazquez","Holt","Schwartz","Steele","Benson","Neal","Dominguez","Horton","Terry","Wolfe","Hale","Lyons","Graves","Haynes","Miles","Park","Warner","Padilla","Bush","Thornton","Mccarthy","Mann","Zimmerman","Erickson","Fletcher","Mckinney","Page","Dawson","Joseph","Marquez","Reeves","Klein","Espinoza","Baldwin","Moran","Love","Robbins","Higgins","Ball","Cortez","Le","Griffith","Bowen","Sharp","Cummings","Ramsey","Hardy","Swanson","Barber","Acosta","Luna","Chandler","Daniel","Blair","Cross","Simon","Dennis","Oconnor","Quinn","Gross","Navarro","Moss","Fitzgerald","Doyle","Mclaughlin","Rojas","Rodgers","Stevenson","Singh","Yang","Figueroa","Harmon","Newton","Paul","Manning","Garner","Mcgee","Reese","Francis","Burgess","Adkins","Goodman","Curry","Brady","Christensen","Potter","Walton","Goodwin","Mullins","Molina","Webster","Fischer","Campos","Avila","Sherman","Todd","Chang","Blake","Malone","Wolf","Hodges","Juarez","Gill","Farmer","Hines","Gallagher","Duran","Hubbard","Cannon","Miranda","Wang","Saunders","Tate","Mack","Hammond","Carrillo","Townsend","Wise","Ingram","Barton","Mejia","Ayala","Schroeder","Hampton","Rowe","Parsons","Frank","Waters","Strickland","Osborne","Maxwell","Chan","Deleon","Norman","Harrington","Casey","Patton","Logan","Bowers","Mueller","Glover","Floyd","Hartman","Buchanan","Cobb","French","Kramer","Mccormick","Clarke","Tyler","Gibbs","Moody","Conner","Sparks","Mcguire","Leon","Bauer","Norton","Pope","Flynn","Hogan","Robles","Salinas","Yates","Lindsey","Lloyd","Marsh","Mcbride","Owen","Solis","Pham","Lang","Pratt","Lara","Brock","Ballard","Trujillo","Shaffer","Drake","Roman","Aguirre","Morton","Stokes","Lamb","Pacheco","Patrick","Cochran","Shepherd","Cain","Burnett","Hess","Li","Cervantes","Olsen","Briggs","Ochoa","Cabrera","Velasquez","Montoya","Roth","Meyers","Cardenas","Fuentes","Weiss","Wilkins","Hoover","Nicholson","Underwood","Short","Carson","Morrow","Colon","Holloway","Summers","Bryan","Petersen","Mckenzie","Serrano","Wilcox","Carey","Clayton","Poole","Calderon","Gallegos","Greer","Rivas","Guerra","Decker","Collier","Wall","Whitaker","Bass","Flowers","Davenport","Conley","Houston","Huff","Copeland","Hood","Monroe","Massey","Roberson","Combs","Franco","Larsen","Pittman","Randall","Skinner","Wilkinson","Kirby","Cameron","Bridges","Anthony","Richard","Kirk","Bruce","Singleton","Mathis","Bradford","Boone","Abbott","Charles","Allison","Sweeney","Atkinson","Horn","Jefferson","Rosales","York","Christian","Phelps","Farrell","Castaneda","Nash","Dickerson","Bond","Wyatt","Foley","Chase","Gates","Vincent","Mathews","Hodge","Garrison","Trevino","Villarreal","Heath","Dalton","Valencia","Callahan","Hensley","Atkins","Huffman","Roy","Boyer","Shields","Lin","Hancock","Grimes","Glenn","Cline","Delacruz","Camacho","Dillon","Parrish","Oneill","Melton","Booth","Kane","Berg","Harrell","Pitts","Savage","Wiggins","Brennan","Salas","Marks","Russo","Sawyer","Baxter","Golden","Hutchinson","Liu","Walter","Mcdowell","Wiley","Rich","Humphrey","Johns","Koch","Suarez","Hobbs","Beard","Gilmore","Ibarra","Keith","Macias","Khan","Andrade","Ware","Stephenson","Henson","Wilkerson","Dyer","Mcclure","Blackwell","Mercado","Tanner","Eaton","Clay","Barron","Beasley","Oneal","Small","Preston","Wu","Zamora","Macdonald","Vance","Snow","Mcclain","Stafford","Orozco","Barry","English","Shannon","Kline","Jacobson","Woodard","Huang","Kemp","Mosley","Prince","Merritt","Hurst","Villanueva","Roach","Nolan","Lam","Yoder","Mccullough","Lester","Santana","Valenzuela","Winters","Barrera","Orr","Leach","Berger","Mckee","Strong","Conway","Stein","Whitehead","Bullock","Escobar","Knox","Meadows","Solomon","Velez","Odonnell","Kerr","Stout","Blankenship","Browning","Kent","Lozano","Bartlett","Pruitt","Buck","Barr","Gaines","Durham","Gentry","Mcintyre","Sloan","Rocha","Melendez","Herman","Sexton","Moon","Hendricks","Rangel","Stark","Lowery","Hardin","Hull","Sellers","Ellison","Calhoun","Gillespie","Mora","Knapp","Mccall","Morse","Dorsey","Weeks","Nielsen","Livingston","Leblanc","Mclean","Bradshaw","Glass","Middleton","Buckley","Schaefer","Frost","Howe","House","Mcintosh","Ho","Pennington","Reilly","Hebert","Mcfarland","Hickman","Noble","Spears","Conrad","Arias","Galvan","Velazquez","Huynh","Frederick","Randolph","Cantu","Fitzpatrick","Mahoney","Peck","Villa","Michael","Donovan","Mcconnell","Walls","Boyle","Mayer","Zuniga","Giles","Pineda","Pace","Hurley","Mays","Mcmillan","Crosby","Ayers","Case","Bentley","Shepard","Everett","Pugh","David","Mcmahon","Dunlap","Bender","Hahn","Harding","Acevedo","Raymond","Blackburn","Duffy","Landry","Dougherty","Bautista","Shah","Potts","Arroyo","Valentine","Meza","Gould","Vaughan","Fry","Rush","Avery","Herring","Dodson","Clements","Sampson","Tapia","Bean","Lynn","Crane","Farley","Cisneros","Benton","Ashley","Mckay","Finley","Best","Blevins","Friedman","Moses","Sosa","Blanchard","Huber","Frye","Krueger","Bernard","Rosario","Rubio","Mullen","Benjamin","Haley","Chung","Moyer","Choi","Horne","Yu","Woodward","Ali","Nixon","Hayden","Rivers","Estes","Mccarty","Richmond","Stuart","Maynard","Brandt","Oconnell","Hanna","Sanford","Sheppard","Church","Burch","Levy","Rasmussen","Coffey","Ponce","Faulkner","Donaldson","Schmitt","Novak","Costa","Montes","Booker","Cordova","Waller","Arellano","Maddox","Mata","Bonilla","Stanton","Compton","Kaufman","Dudley","Mcpherson","Beltran","Dickson","Mccann","Villegas","Proctor","Hester","Cantrell","Daugherty","Cherry","Bray","Davila","Rowland","Madden","Levine","Spence","Good","Irwin","Werner","Krause","Petty","Whitney","Baird","Hooper","Pollard","Zavala","Jarvis","Holden","Hendrix","Haas","Mcgrath","Bird","Lucero","Terrell","Riggs","Joyce","Rollins","Mercer","Galloway","Duke","Odom","Andersen","Downs","Hatfield","Benitez","Archer","Huerta","Travis","Mcneil","Hinton","Zhang","Hays","Mayo","Fritz","Branch","Mooney","Ewing","Ritter","Esparza","Frey","Braun","Gay","Riddle","Haney","Kaiser","Holder","Chaney","Mcknight","Gamble","Vang","Cooley","Carney","Cowan","Forbes","Ferrell","Davies","Barajas","Shea","Osborn","Bright","Cuevas","Bolton","Murillo","Lutz","Duarte","Kidd","Key","Cooke"];
                $creation = new \stdClass();
                $creation->points = $this->getDoctrine()
                    ->getRepository(point_schemas::class)
                    ->findAll();
                $creation->traits = $this->getDoctrine()
                    ->getRepository(trait_entity::class)
                    ->findAll();
                $creation->attributes = new \stdClass();
                $creation->attributes->physical = $this->findTrait($creation->traits,1, 1);
                $creation->attributes->social = $this->findTrait($creation->traits,1, 2);
                $creation->attributes->mental = $this->findTrait($creation->traits,1, 3);
                $creation->abilities = new \stdClass();
                $creation->abilities->talents = $this->findTrait($creation->traits,2, 4);
                $creation->abilities->skills = $this->findTrait($creation->traits,2, 5);
                $creation->abilities->knowledges = $this->findTrait($creation->traits,2, 6);
                $creation->clans = $this->getDoctrine()
                    ->getRepository(clan::class)
                    ->findAll();
                $creation->backgrounds = $this->findTrait($creation->traits,4, 0);
                $creation->virtues = $this->findTrait($creation->traits,5, 0);

                $character = new \stdClass();
                $character->clan = rand(0,(count($creation->clans)-1));
                $character->clanId = $creation->clans[$character->clan]->getId();
                $character->clan = $creation->clans[$character->clan]->getName();
                $ct->setClan($character->clan);
                $ct->setClanId($character->clanId);
                $creation->clanDisciplines = $this->getDoctrine()
                    ->getRepository(clan_disciplines::class)
                    ->findBy(["clan" => $character->clanId]);
                $ct->setGeneration(13);
                $character->generation = 13;
                $character->attributes = new \stdClass();
                $character->abilities = new \stdClass();
                $character->advantages = new \stdClass();
                $character->attributes->physical = new \stdClass();
                $character->attributes->physical->total = 0;
                $character->attributes->physical->target = 0;
                for ($i=0; $i<count($creation->attributes->physical); $i++) {
                    $trait = $creation->attributes->physical[$i]->getTrait();
                    $character->attributes->physical->{$trait} = new \stdClass();
                    $character->attributes->physical->{$trait}->id = $creation->attributes->physical[$i]->getId();
                    $character->attributes->physical->{$trait}->value = 1;
                    $character->attributes->physical->{$trait}->name = $creation->attributes->physical[$i]->getTrait();
                }
                $character->attributes->social = new \stdClass();
                $character->attributes->social->total = 0;
                $character->attributes->social->target = 0;
                for ($i=0; $i<count($creation->attributes->social); $i++) {
                    $trait = $creation->attributes->social[$i]->getTrait();
                    $character->attributes->social->{$trait} = new \stdClass();
                    $character->attributes->social->{$trait}->id = $creation->attributes->social[$i]->getId();
                    $character->attributes->social->{$trait}->value = 1;
                    $character->attributes->social->{$trait}->name = $trait;
                }
                $character->attributes->mental = new \stdClass();
                $character->attributes->mental->total = 0;
                $character->attributes->mental->target = 0;
                for ($i=0; $i<count($creation->attributes->mental); $i++) {
                    $trait = $creation->attributes->mental[$i]->getTrait();
                    $character->attributes->mental->{$trait} = new \stdClass();
                    $character->attributes->mental->{$trait}->id = $creation->attributes->mental[$i]->getId();
                    $character->attributes->mental->{$trait}->value = 1;
                    $character->attributes->mental->{$trait}->name = $trait;
                }
                $ct->setAttributes($character->attributes);
                $character->abilities->talents = new \stdClass();
                $character->abilities->talents->total = 0;
                $character->abilities->talents->target = 0;
                for ($i=0; $i<count($creation->abilities->talents); $i++) {
                    $trait = $creation->abilities->talents[$i]->getTrait();
                    $character->abilities->talents->{$trait} = new \stdClass();
                    $character->abilities->talents->{$trait}->id = $creation->abilities->talents[$i]->getId();
                    $character->abilities->talents->{$trait}->value = 1;
                    $character->abilities->talents->{$trait}->name = $trait;
                }
                $character->abilities->skills = new \stdClass();
                $character->abilities->skills->total = 0;
                $character->abilities->skills->target = 0;
                for ($i=0; $i<count($creation->abilities->skills); $i++) {
                    $trait = $creation->abilities->skills[$i]->getTrait();
                    $character->abilities->skills->{$trait} = new \stdClass();
                    $character->abilities->skills->{$trait}->id = $creation->abilities->skills[$i]->getId();
                    $character->abilities->skills->{$trait}->value = 1;
                    $character->abilities->skills->{$trait}->name = $trait;
                }
                $character->abilities->knowledges = new \stdClass();
                $character->abilities->knowledges->total = 0;
                $character->abilities->knowledges->target = 0;
                for ($i=0; $i<count($creation->abilities->knowledges); $i++) {
                    $trait = $creation->abilities->knowledges[$i]->getTrait();
                    $character->abilities->knowledges->{$trait} = new \stdClass();
                    $character->abilities->knowledges->{$trait}->id = $creation->abilities->knowledges[$i]->getId();
                    $character->abilities->knowledges->{$trait}->value = 1;
                    $character->abilities->knowledges->{$trait}->name = $trait;
                }
                $ct->setAbilities($character->abilities);
                $character->advantages->disciplines = new \stdClass();
                $character->advantages->disciplines->total = 0;
                $character->advantages->disciplines->target = 0;
                for ($i=0; $i<count($creation->clanDisciplines); $i++) {
                    $lookup = $this->findTrait($creation->traits, 0, 0,$creation->clanDisciplines[$i]->getTrait());
                    $trait = $lookup->getTrait();
                    $character->advantages->disciplines->{$trait} = new \stdClass();
                    $character->advantages->disciplines->{$trait}->id = $lookup->getId();
                    $character->advantages->disciplines->{$trait}->value = 0;
                    $character->advantages->disciplines->{$trait}->name = $trait;
                }
                $character->advantages->virtues = new \stdClass();
                $character->advantages->virtues->total = 0;
                $character->advantages->virtues->target = 0;
                for ($i=0; $i<count($creation->virtues); $i++) {
                    $trait = $creation->virtues[$i]->getTrait();
                    $character->advantages->virtues->{$trait} = new \stdClass();
                    $character->advantages->virtues->{$trait}->id = $creation->virtues[$i]->getId();
                    $character->advantages->virtues->{$trait}->value = 1;
                    $character->advantages->virtues->{$trait}->name = $trait;
                }
                $character->advantages->backgrounds = new \stdClass();
                $character->advantages->backgrounds->total = 0;
                $character->advantages->backgrounds->target = 0;
                $ct->setAdvantages($character->advantages);
                $ct->setWillpower(0);
                $ct->setPath(0);
                $s = rand(0,1);
                $f = null;
                $l = null;
                switch ($s) {
                    case 0:
                        $r = rand(0,(count($names->m)-1));
                        $f = $names->m[$r];
                        break;
                    case 1:
                        $r = rand(0,(count($names->f)-1));
                        $f = $names->f[$r];
                        break;
                }
                $r = rand(0,(count($names->l)-1));
                $l = $names->l[$r];
                $ct->setName("$f $l");
                $ct->setNature(0);
                $ct->setDemeanor(0);
                $ct->setSire(0);
                break;
        }
        return $ct;
    }

    protected function findTrait($traits, int $category, int $subCategory, int $id = 0)
    {
        $a = [];
        $c = count($traits);
        for ($i=0; $i<$c; $i++) {
            if ($category !== 0) {
                if ($traits[$i]->getCategory() === $category && $traits[$i]->getSubCategory() === $subCategory) {
                    $a[] = $traits[$i];
                }
            }
            if ($id != 0) {
                if ($traits[$i]->getId() === $id) {
                    return $traits[$i];
                }
            }
        }
        return $a;
    }
    /**
     * @Route("/edit/creation-points")
     */
    public function editCreationPointsAction(Request $request)
    {
        $content = $request->getContent();
        if (!empty($content)) {
            $em = $this->getDoctrine()->getManager();

            $ps = new point_schemas();
            $ps->setType($request->get("point_type"));
            $ps->setSubType($request->get("point_sub_type"));
            $ps->setAttributePrimary($request->get("attribute_primary"));
            $ps->setAttributeSecondary($request->get("attribute_secondary"));
            $ps->setAttributeTertiary($request->get("attribute_tertiary"));
            $ps->setAbilityPrimary($request->get("ability_primary"));
            $ps->setAbilitySecondary($request->get("ability_secondary"));
            $ps->setAbilityTertiary($request->get("ability_tertiary"));
            $ps->setAdvantagesSpecial($request->get("advantages_special"));
            $ps->setAdvantagesBackgrounds($request->get("advantages_backgrounds"));
            $ps->setAdvantagesVirtues($request->get("advantages_virtues"));
            $ps->setFreebies($request->get("advantages_freebies"));
            $ps->setName($request->get("point_name"));

            $em->persist($ps);
            $em->flush();
        }

        $data = new \stdClass();
        $data->url = $request->getRequestUri();
        $data->host = $request->getSchemeAndHttpHost();

        $data->point_schemas = $this->getDoctrine()
            ->getRepository(point_schemas::class)
            ->findAll();

        if (empty($data->point_schemas)) {
            $data->point_schemas = [];
        }
        $data->point_schemas_count = count($data->point_schemas);
        $types = $this->getDoctrine()
            ->getRepository(types::class)
            ->findAll();
        $data->types = [];
        $types_count = count($types);
        $data->types = $this->toAnon($types);
        for ($i=0; $i<$types_count; $i++) {
            $temp = new \stdClass();
            $temp->id = $types[$i]->getId();
            $temp->name = $types[$i]->getName();
            $temp->type = $types[$i]->getType();
            $temp->subType = $types[$i]->getSubType();
            $data->types[] = $temp;
        }
        $data->typesData = json_encode($data->types);
        for ($i=0; $i<$types_count; $i++) {
            if ($data->types[$i]->getSubType() !== 0) {
                unset($data->types[$i]);
            }
        }
        $data->types = array_values($data->types);
        dump($data);

        return $this->render('default/editCreationPoints.html.twig',["data" => $data]);
    }

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

    protected function freebies($ct, $creation)
    {
        $freebies = new \stdClass();
        $freebies->total = 0;
        $freebies->target = $creation->selected->getFreebies();
        $freebies->used_on = [];
        while ($freebies->total < $freebies->target) {
            $left = $freebies->target - $freebies->total;
            $o = new \stdClass();
            if ($left >= 7 && $ct->clan->name !== "caitiff") {
                $n = rand(0,10);
                switch ($n) {
                    case 0:
                        $o->type = "attribute.physical";
                        $o->cost = 5;
                        $o->group = $ct->attributes->physical;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 1:
                        $o->type = "attribute.social";
                        $o->cost = 5;
                        $o->group = $ct->attributes->social;
                        $o->items = $this->getKeyLists($o->group);
                        if ($ct->clan->name === "nosferatu") {
                            $o->items = ["charisma", "manipulation"];
                        }
                        break;
                    case 2:
                        $o->type = "attribute.mental";
                        $o->cost = 5;
                        $o->group = $ct->attributes->mental;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 3:
                        $o->type = "ability.talents";
                        $o->cost = 2;
                        $o->group = $ct->abilities->talents;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 4:
                        $o->type = "ability.skills";
                        $o->cost = 2;
                        $o->group = $ct->abilities->skills;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 5:
                        $o->type = "ability.knowledges";
                        $o->cost = 2;
                        $o->group = $ct->abilities->knowledges;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 6:
                        $o->type = "advantages.disciplines";
                        $o->cost = 7;
                        $o->group = $ct->advantages->disciplines;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 7:
                        $o->type = "advantages.backgrounds";
                        $o->cost = 1;
                        $o->group = $ct->advantages->backgrounds;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 8:
                        $o->type = "advantages.virtues";
                        $o->cost = 1;
                        $o->group = $ct->advantages->virtues;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 9:
                        $o->type = "path";
                        $o->cost = 2;
                        $o->item = $ct->path;
                        break;
                    case 10:
                        $o->type = "willpower";
                        $o->cost = 1;
                        $o->item = $ct->willpower;
                        break;
                }
            } elseif ($left >= 5 && ($left < 7 || $ct->clan->name === "caitiff")) {
                $n = rand(0,9);
                switch ($n) {
                    case 0:
                        $o->type = "attribute.physical";
                        $o->cost = 5;
                        $o->group = $ct->attributes->physical;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 1:
                        $o->type = "attribute.social";
                        $o->cost = 5;
                        $o->group = $ct->attributes->social;
                        $o->items = $this->getKeyLists($o->group);
                        if ($ct->clan->name === "nosferatu") {
                            $o->items = ["charisma", "manipulation"];
                        }
                        break;
                    case 2:
                        $o->type = "attribute.mental";
                        $o->cost = 5;
                        $o->group = $ct->attributes->mental;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 3:
                        $o->type = "ability.talents";
                        $o->cost = 2;
                        $o->group = $ct->abilities->talents;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 4:
                        $o->type = "ability.skills";
                        $o->cost = 2;
                        $o->group = $ct->abilities->skills;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 5:
                        $o->type = "ability.knowledges";
                        $o->cost = 2;
                        $o->group = $ct->abilities->knowledges;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 6:
                        $o->type = "advantages.backgrounds";
                        $o->cost = 1;
                        $o->group = $ct->advantages->backgrounds;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 7:
                        $o->type = "advantages.virtues";
                        $o->cost = 1;
                        $o->group = $ct->advantages->virtues;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 8:
                        $o->type = "path";
                        $o->cost = 2;
                        $o->item = $ct->path;
                        break;
                    case 9:
                        $o->type = "willpower";
                        $o->cost = 1;
                        $o->item = $ct->willpower;
                        break;
                }
            } elseif ($left >= 2 && $left < 5) {
                $n = rand(0,6);
                switch ($n) {
                    case 0:
                        $o->type = "ability.talents";
                        $o->cost = 2;
                        $o->group = $ct->abilities->talents;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 1:
                        $o->type = "ability.skills";
                        $o->cost = 2;
                        $o->group = $ct->abilities->skills;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 2:
                        $o->type = "ability.knowledges";
                        $o->cost = 2;
                        $o->group = $ct->abilities->knowledges;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 3:
                        $o->type = "advantages.backgrounds";
                        $o->cost = 1;
                        $o->group = $ct->advantages->backgrounds;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 4:
                        $o->type = "advantages.virtues";
                        $o->cost = 1;
                        $o->group = $ct->advantages->virtues;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 5:
                        $o->type = "path";
                        $o->cost = 2;
                        $o->item = $ct->path;
                        break;
                    case 6:
                        $o->type = "willpower";
                        $o->cost = 1;
                        $o->item = $ct->willpower;
                        break;
                }
            } elseif ($left >= 1 && $left < 2) {
                $n = rand(0,2);
                switch ($n) {
                    case 0:
                        $o->type = "advantages.backgrounds";
                        $o->cost = 1;
                        $o->group = $ct->advantages->backgrounds;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 1:
                        $o->type = "advantages.virtues";
                        $o->cost = 1;
                        $o->group = $ct->advantages->virtues;
                        $o->items = $this->getKeyLists($o->group);
                        break;
                    case 2:
                        $o->type = "willpower";
                        $o->cost = 1;
                        $o->item = $ct->willpower;
                        break;
                }
            }
            if ($left >= $o->cost) {
                if (!empty($o->item)) {
                    $o->item++;
                    $freebies->total = $freebies->total + $o->cost;
                    $freebies->used_on[] = $o->type;
                } elseif (!empty($o->group)) {
                    $set = false;
                    while ($set === false) {
                        $c = count($o->items)-1;
                        $r = rand(0, $c);
                        if ($o->group[$r]->value < 5) {
                            $o->group[$r]->value++;
                            $freebies->total = $freebies->total + $o->cost;
                            $freebies->used_on[] = $o->type;
                            break;
                        }
                    }
                }
            }
        }
        $ct->freebies = $freebies;
    }

    /**
     * @Route("/freebies/{id}")
     */
    public function freebiesAction(Request $request, $id)
    {
        $data = $this->getCharacterById($request, $id);
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
        $creation = $this->setCreation($data->character->clan->id);

        $this->freebies($data->character, $creation);
        $data->character->freebies = $data->character->freebies->target - $data->character->freebies->total;
        if (!empty($data->character->backgrounds->generation)) {
            $data->character->generation = 13 - $data->character->backgrounds->generation->value;
        }
        $em = $this->getDoctrine()->getManager();
        $cp = $em->getRepository(character_profile::class)
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
                $this->persistCharacterTrait($id, $trait->id, $trait->value);
            }
        }
        foreach ($data->character->abilities as $ability) {
            foreach ($ability as $trait) {
                $this->persistCharacterTrait($id, $trait->id, $trait->value);
            }
        }
        foreach ($data->character->advantages as $advantage) {
            foreach ($advantage as $trait) {
                $this->persistCharacterTrait($id, $trait->id, $trait->value);
            }
        }

        return $this->redirect("/app_dev.php/edit");

    }

    protected function persistCharacterTrait($cp, $trait, $value)
    {
        $em = $this->getDoctrine()->getManager();
        $ct = $em
            ->getRepository(character_traits::class)
            ->findBy(["characterProfile" => intval($cp), "trait" => $trait]);
        $ct[0]->setValue($value);

        $em->flush();
    }


}
