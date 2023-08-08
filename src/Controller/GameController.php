<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Player;
use App\Form\NicknameType;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

final class GameController extends AbstractController
{
    public function __construct(
        private readonly GameRepository $gameRepository,
        private readonly PlayerRepository $playerRepository,
    ) {}

    /**
     * @throws Exception
     */
    #[Route('/game/create', name: 'app_game_create')]
    public function create(): Response
    {
        $code = random_int(100000, 999999);

        $game = new Game();
        $game->setCode($code);

        $this->gameRepository->save($game, true);

        return $this->redirectToRoute('app_game_nickname', compact('code'));
    }

    #[Route('/game/nickname/{code}', name: 'app_game_nickname')]
    public function nickname(Request $request, int $code): Response
    {
        //Get the game
        $game = $this->gameRepository->findOneBy(compact('code'));

        if (!$game) {
            $this->addFlash('danger', 'The game does not exist.');
            return $this->redirectToRoute('app_index');
        }

        //Check if the game is full
        if ($game->getPlayers()->count() >= 4) {
            $this->addFlash('danger', 'The game is full.');
            return $this->redirectToRoute('app_index');
        }

        //Check if the game is started
        //Check if the game is finished
        $form = $this->createForm(NicknameType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $nickname = $form->get('nickname')->getData();

            if ($game->getPlayers()->exists(static fn (int $key, Player $player) => $player->getNickname() === $nickname)) {
                $this->addFlash('error', 'The nickname is already taken.');
                return $this->redirectToRoute('app_game_nickname', compact('code'));
            }

            $player = new Player();
            $player->setNickname($nickname);

            $game->addPlayer($player);

            $this->playerRepository->save($player, true);
            $this->gameRepository->save($game, true);

            $request->getSession()->set('player_id', $player->getId());

            return $this->redirectToRoute('app_game_lobby', compact('code'));
        }

        return $this->render(
            'game/nickname.html.twig',
            ['nicknameForm' => $form,]
        );
    }

    #[Route('/game/lobby/{code}', name: 'app_game_lobby')]
    public function lobby(Request $request, HubInterface $hub, int $code): Response
    {
        //Get the game
        $game = $this->gameRepository->findOneBy(compact('code'));

        if (!$game) {
            $this->addFlash('danger', 'The game does not exist.');
            return $this->redirectToRoute('app_index');
        }

        //Get the user
        $playerId = $request->getSession()->get('player_id');

        if (!$playerId) {
            $this->addFlash('danger', 'You are not a player.');
            return $this->redirectToRoute('app_index');
        }

        $player = $this->playerRepository->find($playerId);

        if (!$player || $player->getGame() !== $game) {
            $this->addFlash('danger', 'You are not in the game.');
            return $this->redirectToRoute('app_index');
        }

        //new Update with the stream template
        $update = new Update(
            sprintf('https://example.com/game/%d', $game->getId()),
            $this->renderView('game/stream/_player-join.stream.html.twig', compact('player')),
        );

        $hub->publish($update);

        return $this->render('game/lobby.html.twig', compact('game', 'player'));
    }
}
