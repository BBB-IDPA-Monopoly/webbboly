<?php

namespace App\Service;

use App\Entity\Building;
use App\Entity\Game;
use App\Entity\GameActionField;
use App\Entity\GameBuilding;
use App\Entity\GameCard;
use App\Entity\Player;
use App\Entity\Street;
use App\Repository\ActionFieldRepository;
use App\Repository\BuildingRepository;
use App\Repository\CardRepository;
use App\Repository\GameActionFieldRepository;
use App\Repository\GameBuildingRepository;
use App\Repository\GameCardRepository;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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
        private GameStreamService $gameStreamService,
        private GameFunctions $gameFunctions,
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

        $this->gameStreamService->sendGameStart($game);
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

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws Exception
     */
    public function turn(Game $game, Player $currentPlayer, int $position): void
    {
        $fields = $game->getFieldsWithPositions();

        if ($position > count($fields)) {
            $position = $position - count($fields);
            $currentPlayer->addMoney(200);
        }

        $currentField = $fields[$currentPlayer->getPosition()];
        $newPosition = $fields[$position];

        if ($newPosition instanceof GameBuilding) {
            if ($newPosition->getOwner() === null) {
                $this->gameStreamService->sendShowBuildingCard($newPosition->getBuilding(), $currentPlayer);
            } else {
                $this->payRent($currentPlayer, $newPosition);
            }
        } elseif ($newPosition instanceof GameActionField) {
            $function = $newPosition->getActionField()->getFunction();

            if (method_exists($this->gameFunctions, $function)) {
                $this->gameFunctions->$function($currentPlayer);
            }
        }

        $currentPlayer->setPosition($position);

        $this->playerRepository->save($currentPlayer, true);
        $this->gameStreamService->sendUpdateField($game, $currentField);
        $this->gameStreamService->sendUpdateField($game, $newPosition);
        $this->gameStreamService->sendUpdatePlayer($game, $currentPlayer);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function buyBuilding(Game $game, Player $player, GameBuilding $gameBuilding): void
    {
        if ($player->getMoney() < $gameBuilding->getBuilding()->getPrice()) {
            return;
        }

        $gameBuilding->setOwner($player);
        $player->subtractMoney($gameBuilding->getBuilding()->getPrice());

        $this->gameBuildingRepository->save($gameBuilding, true);
        $this->playerRepository->save($player, true);

        $this->gameStreamService->sendUpdateField($game, $gameBuilding);
        $this->gameStreamService->sendUpdatePlayer($game, $player);
    }

    public function wholeStreetOwned(Game $game, Building $building): bool
    {
        $street = $building->getStreet();

        $buildings = $this->gameBuildingRepository->findByGameAndStreet($game, $street);

        $owners = array_map(static fn(GameBuilding $building) => $building->getOwner(), $buildings);

        $sameOwner = false;
        foreach ($owners as $owner) {
            if ($owner === null) {
                return false;
            }

            if ($sameOwner === false) {
                $sameOwner = $owner;
            }

            if ($sameOwner !== $owner) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws Exception
     */
    private function payRent(Player $currentPlayer, GameBuilding $newPosition): void
    {
        if ($newPosition->getOwner() === $currentPlayer) {
            return;
        } elseif ($newPosition->getOwner() === null) {
            throw new Exception('This should not happen.');
        }

        $wholeStreetOwned = $this->wholeStreetOwned($newPosition->getGame(), $newPosition->getBuilding());

        $owner = $newPosition->getOwner();
        $rent = $newPosition->getRent($wholeStreetOwned);

        $currentPlayer->subtractMoney($rent);
        $owner->addMoney($rent);

        $this->playerRepository->save($currentPlayer, true);
        $this->playerRepository->save($owner, true);

        $this->gameStreamService->sendUpdatePlayer($newPosition->getGame(), $owner);
    }
}
