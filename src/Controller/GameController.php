<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\Game;
use App\Entity\GameActionField;
use App\Entity\GameBuilding;
use App\Entity\GameCard;
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
    #[Route('/game/{code}/turn/{id}/{position}/{pasch}', name: 'app_game_move')]
    public function turn(
        #[MapEntity(mapping: ['code' => 'code'])] Game $game,
        #[MapEntity(id: 'id')] Player $player,
        int $position,
        string $pasch = 'false',
    ): Response
    {
        $this->gameService->turn($game, $player, $position, $pasch === 'true');

        return $this->json([
            'success' => true,
            'position' => $player->getPosition(),
            'disable' => $player->getMoney() < 0,
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/game/{code}/turn/{id}/end', name: 'app_game_turn_end', priority: 1)]
    public function turnEnd(
        #[MapEntity(mapping: ['code' => 'code'])] Game $game,
        #[MapEntity(id: 'id')] Player $player
    ): Response
    {
        $this->gameService->turnEnd($game, $player);

        return $this->json([
            'success' => true,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/game/{code}/player/{playerId}/buy/{buildingId}', name: 'app_game_buy_building')]
    public function buyBuilding(
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

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/game/{code}/player/{playerId}/buy/{actionFieldId}/{price}', name: 'app_game_buy_action_field')]
    public function buyActionField(
        #[MapEntity(mapping: ['code' => 'code'])] Game $game,
        #[MapEntity(id: 'playerId')] Player $player,
        #[MapEntity(id: 'actionFieldId')] GameActionField $gameActionField,
        int $price,
    ): Response
    {
        $this->gameService->buyActionField($game, $player, $gameActionField, $price);

        return $this->json([
            'success' => true,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/game/{code}/player/{player}/mortgage/{building}/building', name: 'app_game_mortgage_building')]
    public function mortgageBuilding(
        #[MapEntity(mapping: ['code' => 'code'])] Game $game,
        #[MapEntity(id: 'playerId')] Player $player,
        #[MapEntity(id: 'buildingId')] GameBuilding $gameBuilding,
    ): Response
    {
        $this->gameService->mortgageBuilding($game, $player, $gameBuilding);

        return $this->json([
            'success' => true,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/game/{code}/player/{player}/mortgage/{actionField}/actionField', name: 'app_game_mortgage_action_field')]
    public function mortgageActionField(
        #[MapEntity(mapping: ['code' => 'code'])] Game $game,
        #[MapEntity(id: 'player')] Player $player,
        #[MapEntity(id: 'actionField')] GameActionField $gameActionField,
    ): Response
    {
        $this->gameService->mortgageActionField($game, $player, $gameActionField);

        return $this->json([
            'success' => true,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/game/{code}/player/{player}/{building}/house/buy', name: 'app_game_buy_house')]
    public function buyHouse(
        #[MapEntity(mapping: ['code' => 'code'])] Game $game,
        #[MapEntity(id: 'player')] Player $player,
        #[MapEntity(id: 'building')] GameBuilding $gameActionField,
    ): Response
    {
        $this->gameService->buyHouse($game, $player, $gameActionField);

        return $this->json([
            'success' => true,
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/game/{code}/player/{player}/{building}/house/sell', name: 'app_game_sell_house')]
    public function sellHouse(
        #[MapEntity(mapping: ['code' => 'code'])] Game $game,
        #[MapEntity(id: 'player')] Player $player,
        #[MapEntity(id: 'building')] GameBuilding $gameActionField,
    ): Response
    {
        $this->gameService->sellHouse($game, $player, $gameActionField);

        return $this->json([
            'success' => true,
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/game/{code}/player/{player}/{card}/function', name: 'app_game_call_card_function')]
    public function callCardFunction(
        #[MapEntity(mapping: ['code' => 'code'])] Game $game,
        #[MapEntity(id: 'player')] Player $player,
        #[MapEntity(id: 'card')] GameCard $gameCard,
    ): Response
    {
        $this->gameService->callCardFunction($game, $player, $gameCard);

        return $this->json([
            'success' => true,
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/game/{code}/player/{player}/{option}', name: 'app_game_prison_bail')]
    public function prisonBail(
        #[MapEntity(mapping: ['code' => 'code'])] Game $game,
        #[MapEntity(id: 'player')] Player $player,
        string $option,
    ): Response
    {
        $this->gameService->prisonBail($game, $player, $option);

        return $this->json([
            'success' => true,
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/game/{code}/player/{player}/bankrupt', name: 'app_game_bankrupt', priority: 2)]
    public function bankrupt(
        #[MapEntity(mapping: ['code' => 'code'])] Game $game,
        #[MapEntity(id: 'player')] Player $player,
    ): Response
    {
        $this->gameService->turnEnd($game, $player, true);

        return $this->json([
            'success' => true,
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/game/{code}/end', name: 'app_game_end')]
    public function end(
        #[MapEntity(mapping: ['code' => 'code'])] Game $game,
    ): Response
    {
        $winner = $game->winner();
        $players = $game->getPlayers();
        $this->gameService->end($game);

        return $this->render('game/end.html.twig', compact('game', 'winner', 'players'));
    }

}
