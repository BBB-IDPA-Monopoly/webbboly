<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Player;
use App\Form\NicknameType;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use App\Service\GameStreamService;
use Exception;
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
        private readonly GameRepository $gameRepository,
        private readonly PlayerRepository $playerRepository,
        private readonly GameStreamService $gameStreamService,
    ) {}

    /**
     * @throws Exception
     */
    #[Route('/game/create', name: 'app_game_create')]
    public function create(): Response
    {
        while (true) {
            $code = random_int(100000, 999999);

            if (!$this->gameRepository->findOneBy(compact('code'))) {
                break;
            }
        }

        $game = new Game();
        $game->setCode($code);

        $this->gameRepository->save($game, true);

        return $this->redirectToRoute('app_game_nickname', compact('code'));
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/game/nickname/{code}', name: 'app_game_nickname')]
    public function nickname(Game $game, Request $request): Response
    {
        if ($game->getPlayers()->count() >= 4) {
            $this->addFlash('danger', 'The game is full.');
            return $this->redirectToRoute('app_index');
        }

        $form = $this->createForm(NicknameType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $nickname = $form->get('nickname')->getData();

            if ($game->getPlayers()->exists(static fn (int $key, Player $player) => $player->getNickname() === $nickname)) {
                $this->addFlash('error', 'The nickname is already taken.');
                return $this->redirectToRoute('app_game_nickname', ['code' => $game->getCode()]);
            }

            $player = new Player();
            $player->setNickname($nickname);

            $game->addPlayer($player);

            $this->playerRepository->save($player, true);
            $this->gameStreamService->sendPlayerJoin($game->getCode(), $player);

            $request->getSession()->set('player_id', $player->getId());

            return $this->redirectToRoute('app_game_lobby', ['code' => $game->getCode()]);
        }

        return $this->render(
            'game/nickname.html.twig',
            ['nicknameForm' => $form,]
        );
    }

    #[Route('/game/lobby/{code}', name: 'app_game_lobby')]
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

        return $this->render('game/lobby.html.twig', compact('game', 'currentPlayer'));
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/game/ready/{code}', name: 'app_game_ready')]
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

        $this->gameStreamService->sendPlayerReady($game->getCode(), $currentPlayer);

        return $this->redirectToRoute('app_game_lobby', ['code' => $game->getCode()]);
    }

    //remove player from game

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/game/kick/{code}/{number}', name: 'app_game_kick')]
    public function kick(Game $game, Player $player): Response
    {
        $this->playerRepository->remove($player, true);
        $this->gameStreamService->sendPlayerLeave($game->getCode(), $player);

        return $this->redirectToRoute('app_game_lobby', ['code' => $game->getCode()]);
    }
}
