<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class EditController extends Controller 
{
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
}