<?php

namespace App\Controller;

use App\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/game/start/{code}/index', name: 'app_game_start')]
    public function start(Game $game): Response
    {
        return $this->render('game/start.html.twig', [
            'controller_name' => 'GameController',
        ]);
    }
}
