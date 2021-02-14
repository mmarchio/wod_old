<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CombatArenaController extends Controller
{
    /**
    * @Route("/arena", name="arena")
    * @IsGranted("ROLE_ADMIN")
    */
    public function arenaAction(Request $request): Response
    {

    }
} 
