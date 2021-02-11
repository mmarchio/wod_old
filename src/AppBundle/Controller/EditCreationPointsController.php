<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class EditCreationPointsController extends Controller 
{
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

        return $this->render('default/editCreationPoints.html.twig',["data" => $data]);
    }
}