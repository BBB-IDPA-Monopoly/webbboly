<?php

namespace App\Controller;

use App\Entity\Game;
use App\Service\GameService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class GameController extends AbstractController
{
    public function __construct(
        private readonly GameService $gameService,
    ) {}

    /**
     * @throws Exception
     */
    #[Route('/game/start/{code}/index', name: 'app_game_start')]
    public function start(Game $game): Response
    {
        $this->gameService->startGame($game);

        return $this->render('game/start.html.twig', [
            'controller_name' => 'GameController',
        ]);
    }
}
