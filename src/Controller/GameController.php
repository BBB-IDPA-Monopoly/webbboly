<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\Game;
use App\Entity\GameBuilding;
use App\Entity\Player;
use App\Repository\PlayerRepository;
use App\Service\GameService;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class GameController extends AbstractController
{
    public function __construct(
        private readonly GameService $gameService,
        private readonly PlayerRepository $playerRepository,
    ) {}

    /**
     * @throws Exception
     */
    #[Route('/game/start/{code}', name: 'app_game_start')]
    public function start(Game $game, Request $request): Response
    {
        $playerId = $request->getSession()->get('player_id');

        if (!$playerId) {
            $this->addFlash('danger', 'You are not a player.');
            return $this->redirectToRoute('app_index');
        }

        $this->gameService->startGame($game);

        return $this->redirectToRoute('app_game_play', [
            'code' => $game->getCode(),
        ], Response::HTTP_SEE_OTHER);
    }

    #[Route('/game/play/{code}', name: 'app_game_play')]
    public function play(Game $game, Request $request): Response
    {
        $playerId = $request->getSession()->get('player_id');

        if (!$playerId) {
            $this->addFlash('danger', 'You are not a player.');
            return $this->redirectToRoute('app_index');
        }

        $currentPlayer = $this->playerRepository->find($playerId);

        return $this->render('game/start.html.twig', compact('game', 'currentPlayer'));
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/game/{code}/turn/{id}/{position}', name: 'app_game_move')]
    public function turn(
        #[MapEntity(mapping: ['code' => 'code'])] Game $game,
        #[MapEntity(id: 'id')] Player $player,
        int $position
    ): Response
    {
        $this->gameService->turn($game, $player, $position);

        return $this->json([
            'success' => true,
            'position' => $player->getPosition(),
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/game/{code}/player/{playerId}/buy/{buildingId}', name: 'app_game_buy')]
    public function buy(
        #[MapEntity(mapping: ['code' => 'code'])] Game $game,
        #[MapEntity(id: 'playerId')] Player $player,
        #[MapEntity(id: 'buildingId')] GameBuilding $gameBuilding,
    ): Response
    {
        $this->gameService->buyBuilding($game, $player, $gameBuilding);

        return $this->json([
            'success' => true,
        ]);
    }
}
