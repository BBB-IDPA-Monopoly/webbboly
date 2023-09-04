<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\PlayerRepository;
use App\Service\GameService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class GameController extends AbstractController
{
    public function __construct(
        private readonly GameService $gameService,
        private readonly PlayerRepository $playerRepository,
    ) {}

    /**
     * @throws Exception
     */
    #[Route('/game/start/{code}/index', name: 'app_game_start')]
    public function start(Game $game, Request $request): Response
    {
        $playerId = $request->getSession()->get('player_id');

        if (!$playerId) {
            $this->addFlash('danger', 'You are not a player.');
            return $this->redirectToRoute('app_index');
        }

        $this->gameService->startGame($game);
        $currentPlayer = $this->playerRepository->find($playerId);

        return $this->render('game/start.html.twig', compact('game', 'currentPlayer'));
    }
}
