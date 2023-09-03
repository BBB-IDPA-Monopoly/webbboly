<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Player;
use App\Form\NicknameType;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use App\Service\GameService;
use App\Service\LobbyStreamService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class LobbyController extends AbstractController
{
    public function __construct(
        private readonly GameService        $gameService,
        private readonly GameRepository     $gameRepository,
        private readonly PlayerRepository   $playerRepository,
        private readonly LobbyStreamService $lobbyStreamService,
    ) {}

    /**
     * @throws Exception
     */
    #[Route('/game/create', name: 'app_lobby_create')]
    public function create(): Response
    {
        $game = $this->gameService->createGame();

        return $this->redirectToRoute('app_lobby_nickname', [
            'code' => $game->getCode(),
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws Exception
     */
    #[Route('/game/remove/{code}', name: 'app_lobby_delete')]
    public function delete(Game $game): Response
    {
        foreach ($game->getPlayers() as $player) {
            $this->playerRepository->remove($player, true);
        }

        $this->gameRepository->remove($game, true);

        $this->lobbyStreamService->sendGameDelete($game);

        return $this->redirectToRoute('app_index');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws Exception
     */
    #[Route('/game/nickname/{code}', name: 'app_lobby_nickname')]
    public function nickname(Game $game, Request $request): Response
    {
        if ($game->getPlayers()->count() >= 4) {
            $this->addFlash('danger', 'The game is full.');
            return $this->redirectToRoute('app_index');
        }

        $form = $this->createForm(NicknameType::class, options: [
            'taken_nicknames' => $game->getPlayers()->map(
                static fn (Player $player) => $player->getNickname()
            )->toArray(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $nickname = $form->get('nickname')->getData();

            if ($game->getPlayers()->exists(
                static fn (int $key, Player $player) => strtolower($player->getNickname()) === strtolower($nickname)
            )) {
                $this->addFlash('danger', 'The nickname is already taken.');
                return $this->redirectToRoute('app_lobby_nickname', ['code' => $game->getCode()]);
            }

            $player = $this->gameService->joinGame($game, $nickname);
            $this->lobbyStreamService->sendPlayerJoin($game, $player);

            $request->getSession()->set('player_id', $player->getId());

            return $this->redirectToRoute('app_lobby_overview', ['code' => $game->getCode()]);
        }

        return $this->render(
            'lobby/nickname.html.twig',
            ['nicknameForm' => $form,]
        );
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/game/lobby/{code}', name: 'app_lobby_overview')]
    public function lobby(Game $game, Request $request): Response
    {
        $playerId = $request->getSession()->get('player_id');

        if (!$playerId) {
            $this->addFlash('danger', 'You are not a player.');
            return $this->redirectToRoute('app_index');
        }

        $currentPlayer = $this->playerRepository->find($playerId);

        if (!$currentPlayer || $currentPlayer->getGame() !== $game) {
            $this->addFlash('danger', 'You are not in the game.');
            return $this->redirectToRoute('app_index');
        }

        if ($game->isGameFull() && !$game->isGameReady()) {
            $this->lobbyStreamService->sendUpdateStartLink($game);
        }

        return $this->render('lobby/lobby.html.twig', compact('game', 'currentPlayer'));
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/game/ready/{code}', name: 'app_lobby_ready')]
    public function ready(Game $game, Request $request): Response
    {
        $playerId = $request->getSession()->get('player_id');

        if (!$playerId) {
            $this->addFlash('danger', 'You are not a player.');
            return $this->redirectToRoute('app_index');
        }

        $currentPlayer = $this->playerRepository->find($playerId);

        if (!$currentPlayer || $currentPlayer->getGame() !== $game) {
            $this->addFlash('danger', 'You are not in the game.');
            return $this->redirectToRoute('app_index');
        }

        $currentPlayer->setReady(!$currentPlayer->isReady());

        $this->playerRepository->save($currentPlayer, true);

        $this->lobbyStreamService->sendPlayerReady($game, $currentPlayer);

        if ($game->isGameReady()) {
            $this->lobbyStreamService->sendUpdateStartLink($game);
        }

        return $this->redirectToRoute('app_lobby_overview', ['code' => $game->getCode()]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/game/leave/{code}', name: 'app_lobby_leave')]
    public function leave(Game $game, Request $request): Response
    {
        $playerId = $request->getSession()->get('player_id');

        if (!$playerId) {
            $this->addFlash('danger', 'You are not a player.');
            return $this->redirectToRoute('app_index');
        }

        $currentPlayer = $this->playerRepository->find($playerId);

        if (!$currentPlayer || $currentPlayer->getGame() !== $game) {
            $this->addFlash('danger', 'You are not in the game.');
            return $this->redirectToRoute('app_index');
        }

        $game->removePlayer($currentPlayer);
        $this->lobbyStreamService->sendPlayerLeave($game, $currentPlayer);
        $this->playerRepository->remove($currentPlayer, true);

        return $this->redirectToRoute('app_index');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/game/kick/{code}/{number}', name: 'app_lobby_kick')]
    public function kick(Game $game, Player $player): Response
    {
        $game->removePlayer($player);
        $this->lobbyStreamService->sendPlayerKick($game, $player);
        $this->playerRepository->remove($player, true);

        return $this->redirectToRoute('app_lobby_overview', ['code' => $game->getCode()]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/game/start/{code}', name: 'app_lobby_start')]
    public function start(Game $game): Response
    {
        $this->lobbyStreamService->sendGameStart($game);

        return $this->redirectToRoute('app_game_start', ['code' => $game->getCode()]);
    }
}
