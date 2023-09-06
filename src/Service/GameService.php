<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\GameActionField;
use App\Entity\GameBuilding;
use App\Entity\GameCard;
use App\Entity\Player;
use App\Repository\ActionFieldRepository;
use App\Repository\BuildingRepository;
use App\Repository\CardRepository;
use App\Repository\GameActionFieldRepository;
use App\Repository\GameBuildingRepository;
use App\Repository\GameCardRepository;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use Exception;

final readonly class GameService
{
    const STARTING_MONEY = 1500;
    const STARTING_POSITION = 0;

    public function __construct(
        private GameRepository $gameRepository,
        private PlayerRepository $playerRepository,
        private GameBuildingRepository $gameBuildingRepository,
        private GameActionFieldRepository $gameActionFieldRepository,
        private GameCardRepository $gameCardRepository,
        private BuildingRepository $buildingRepository,
        private ActionFieldRepository $actionFieldRepository,
        private CardRepository $cardRepository,
    ) {}

    /**
     * @throws Exception
     */
    public function createGame(): Game
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

        return $game;
    }

    /**
     * @throws Exception
     */
    public function joinGame(Game $game, string $nickname): Player
    {
        $player = new Player();
        $player->setNickname($nickname);
        $player->setNumber($game->getPlayers()->count() + 1);
        $player->setGame($game);

        $this->playerRepository->save($player, true);

        return $player;
    }

    /**
     * @throws Exception
     */
    public function startGame(Game $game): void
    {
        $this->createBuildings($game);
        $this->createActionFields($game);
        $this->createCards($game);

        $players = $game->getPlayers();

        foreach ($players as $player) {
            $player->setMoney(self::STARTING_MONEY);
            $player->setPosition(self::STARTING_POSITION);

            $this->playerRepository->save($player, true);
        }
    }

    /**
     * @throws Exception
     */
    private function createBuildings(Game $game): void
    {
        $buildings = $this->buildingRepository->findAll();

        foreach ($buildings as $building) {
            $gameBuilding = $this->gameBuildingRepository->findOneBy(compact('game', 'building'));

            if (!$gameBuilding) {
                $gameBuilding = new GameBuilding();
                $gameBuilding->setGame($game);
                $gameBuilding->setBuilding($building);
            }

            $this->gameBuildingRepository->save($gameBuilding, true);
        }
    }

    /**
     * @throws Exception
     */
    private function createActionFields(Game $game): void
    {
        $actionFields = $this->actionFieldRepository->findAll();

        foreach ($actionFields as $actionField) {
            $gameActionField = $this->gameActionFieldRepository->findOneBy(compact('game', 'actionField'));

            if (!$gameActionField) {
                $gameActionField = new GameActionField();
                $gameActionField->setGame($game);
                $gameActionField->setActionField($actionField);
            }

            $this->gameActionFieldRepository->save($gameActionField, true);
        }
    }

    /**
     * @throws Exception
     */
    private function createCards(Game $game): void
    {
        $cards = $this->cardRepository->findAll();

        foreach ($cards as $card) {
            $gameCard = $this->gameCardRepository->findOneBy(compact('game', 'card'));

            if (!$gameCard) {
                $gameCard = new GameCard();
                $gameCard->setGame($game);
                $gameCard->setCard($card);
            }

            $this->gameCardRepository->save($gameCard, true);
        }
    }
}
