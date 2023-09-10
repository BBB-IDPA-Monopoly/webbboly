<?php

namespace App\Service;

use App\Entity\ActionField;
use App\Entity\Building;
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
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class GameService
{
    const STARTING_MONEY = 1500;
    const STARTING_POSITION = 0;
    const GO_MONEY = 200;
    const INCOME_TAX = 200;
    const LUXURY_TAX = 100;
    const RAILROAD_PRICE = 200;
    const UTILITY_PRICE = 150;
    const REPAIR_HOUSE = 25;
    const REPAIR_HOTEL = 100;
    const PRISON_TURN = 3;
    const PRISON_BAIL = 50;

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
        private ActionFieldRent $actionFieldRent,
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

        $turnOrder = new ArrayCollection();
        foreach ($players as $player) {
            $player->setMoney(self::STARTING_MONEY);
            $player->setPosition(self::STARTING_POSITION);

            $turnOrder[$player->getNumber()] = $player->getId();

            $this->playerRepository->save($player, true);
        }

        $game->setTurnOrder($turnOrder);
        $game->setCurrentTurnPlayer($players->first());

        $this->gameRepository->save($game, true);
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
    public function turn(Game $game, Player $currentPlayer, int $position, bool $pasch = false): void
    {
        if ($currentPlayer->getPrisonTurns() !== null && $pasch === false) {
            $currentPlayer->setPrisonTurns($currentPlayer->getPrisonTurns() - 1);

            if ($currentPlayer->getPrisonTurns() === 0) {
                $currentPlayer->subtractMoney(self::PRISON_BAIL);
                $currentPlayer->setPrisonTurns(null);
            }

            $this->playerRepository->save($currentPlayer, true);
            $this->gameStreamService->sendUpdatePlayer($game, $currentPlayer, true);
            $this->gameStreamService->sendTurnRolled($game, $currentPlayer);
            return;
        } elseif ($currentPlayer->getPrisonTurns() !== null && $pasch === true) {
            $currentPlayer->setPrisonTurns(null);
        }

        $fields = $game->getFieldsWithPositions();
        $currentPosition = $currentPlayer->getPosition();

        if ($position > count($fields)) {
            $position = $position - count($fields);
            $currentPlayer->addMoney(200);
        }

        $currentField = $fields[$currentPosition];
        $newPosition = $fields[$position];

        $currentPlayer->setFieldsAdvanced($position - $currentPlayer->getPosition());
        $currentPlayer->setPosition($position);

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
            } else {
                throw new Exception('This should not happen.');
            }
        }

        if ($currentPosition !== $currentPlayer->getPosition()) {
            $newPosition = $fields[$currentPlayer->getPosition()];
        }

        $currentPrice = $currentField instanceof GameActionField
            ? $this->getPriceByFunction($currentField->getField()->getFunction())
            : 0;

        $newPrice = $newPosition instanceof GameActionField
            ? $this->getPriceByFunction($newPosition->getField()->getFunction())
            : 0;

        $this->playerRepository->save($currentPlayer, true);
        $this->gameStreamService->sendUpdatePlayer($game, $currentPlayer, true);
        $this->gameStreamService->sendUpdateField($game, $currentField, $currentPrice);
        $this->gameStreamService->sendUpdateField($game, $newPosition, $newPrice);
        $this->gameStreamService->sendTurnRolled($game, $currentPlayer);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function turnEnd(Game $game, Player $player): void
    {
        $turnOrder = $game->getTurnOrder();

        while ($turnOrder->current() !== $player->getId()) {
            $turnOrder->next();
        }

        $nextPlayerId = $turnOrder->next();

        if ($nextPlayerId === false) {
            $nextPlayerId = $turnOrder->first();
        }

        $nextPlayer = $this->playerRepository->find($nextPlayerId);

        if ($nextPlayer === null) {
            throw new Exception('This should not happen.');
        }

        $game->setCurrentTurnPlayer($nextPlayer);

        $this->gameRepository->save($game, true);
        $this->gameStreamService->sendUpdatePlayer($game, $player);
        $this->gameStreamService->sendUpdatePlayer($game, $nextPlayer, true);
        $this->gameStreamService->sendEndTurn($game, $player, $nextPlayer);

        if ($nextPlayer->getPrisonTurns() !== null) {
            $this->gameStreamService->sendPrisonBailOptions($game, $nextPlayer);
        }
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

        $this->gameStreamService->sendUpdatePlayer($game, $player, true);

        $gameStreetBuildings = $this->gameBuildingRepository->findByGameAndStreet(
            $game,
            $gameBuilding->getBuilding()->getStreet()
        );

        foreach ($gameStreetBuildings as $gameStreetBuilding) {
            $this->gameStreamService->sendUpdateField($game, $gameStreetBuilding);
        }
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function buyActionField(Game $game, Player $player, GameActionField $gameActionField, int $price): void
    {
        if ($player->getMoney() < $price) {
            return;
        }

        $gameActionField->setOwner($player);
        $player->subtractMoney($price);

        $this->gameActionFieldRepository->save($gameActionField, true);
        $this->playerRepository->save($player, true);

        $this->gameStreamService->sendUpdatePlayer($game, $player, true);

        switch ($gameActionField->getActionField()->getFunction()) {
            case GameFunctions::FUNCTION_RAILROAD:
                $railroads = $this->actionFieldRent->getRailroads($game);
                $ownedRailroads = $this->actionFieldRent->getOwnedRailroads($player, $railroads);

                foreach ($ownedRailroads as $railroad) {
                    $this->gameStreamService->sendUpdateField($game, $railroad, self::RAILROAD_PRICE);
                }
                break;
            case GameFunctions::FUNCTION_UTILITY:
                $utilities = $this->actionFieldRent->getUtilities($game);
                $ownedUtilities = $this->actionFieldRent->getOwnedUtilities($player, $utilities);

                foreach ($ownedUtilities as $utility) {
                    $this->gameStreamService->sendUpdateField($game, $utility, self::UTILITY_PRICE);
                }
                break;
            default:
                $this->gameStreamService->sendUpdateField($game, $gameActionField);
                break;
        }
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function mortgageBuilding(Game $game, Player $player, GameBuilding $gameBuilding): void
    {
        if ($gameBuilding->getOwner() !== $player) {
            return;
        }

        $streetBuildings = $this->gameBuildingRepository->findByGameAndStreet(
            $game,
            $gameBuilding->getBuilding()->getStreet()
        );

        foreach ($streetBuildings as $streetBuilding) {
            if ($streetBuilding->getHouses() > 0) {
                return;
            }
        }

        if ($gameBuilding->isMortgaged()) {
            $gameBuilding->setMortgaged(false);
            $player->subtractMoney((int)round($gameBuilding->getBuilding()->getMortgage() * 1.1));
        } else {
            $gameBuilding->setMortgaged(true);
            $player->addMoney($gameBuilding->getBuilding()->getMortgage());
        }

        $this->gameBuildingRepository->save($gameBuilding, true);
        $this->playerRepository->save($player, true);

        $this->gameStreamService->sendUpdatePlayer($game, $player, true);
        $this->gameStreamService->sendUpdateField($game, $gameBuilding);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function mortgageActionField(Game $game, Player $player, GameActionField $gameActionField): void
    {
        if ($gameActionField->getOwner() !== $player) {
            return;
        }

        if ($gameActionField->isMortgaged()) {
            $gameActionField->setMortgaged(false);
            $player->subtractMoney((int)round($gameActionField->getActionField()->getMortgage() * 1.1));
        } else {
            $gameActionField->setMortgaged(true);
            $player->addMoney($gameActionField->getActionField()->getMortgage());
        }

        $this->gameActionFieldRepository->save($gameActionField, true);
        $this->playerRepository->save($player, true);

        $this->gameStreamService->sendUpdatePlayer($game, $player, true);

        switch ($gameActionField->getActionField()->getFunction()) {
            case GameFunctions::FUNCTION_RAILROAD:
                $this->gameStreamService->sendUpdateField($game, $gameActionField, self::RAILROAD_PRICE);
                break;
            case GameFunctions::FUNCTION_UTILITY:
                $this->gameStreamService->sendUpdateField($game, $gameActionField, self::UTILITY_PRICE);
                break;
            default:
                $this->gameStreamService->sendUpdateField($game, $gameActionField);
                break;
        }
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function buyHouse(Game $game, Player $player, GameBuilding $gameBuilding): void
    {
        if (
            $gameBuilding->getOwner() !== $player ||
            !$this->wholeStreetOwned($game, $gameBuilding->getBuilding())
        ) {
            return;
        }

        $streetBuildings = $this->gameBuildingRepository->findByGameAndStreet(
            $game,
            $gameBuilding->getBuilding()->getStreet()
        );

        $minHouses = 5;
        $maxHouses = 0;
        foreach ($streetBuildings as $streetBuilding) {
            if ($streetBuilding->getHouses() > $maxHouses) {
                $maxHouses = $streetBuilding->getHouses();
            }

            if ($streetBuilding->getHouses() < $minHouses) {
                $minHouses = $streetBuilding->getHouses();
            }
        }

        if ($gameBuilding->getHouses() != $minHouses) {
            return;
        }

        $gameBuilding->addHouse();
        $player->subtractMoney($gameBuilding->getBuilding()->getStreet()->getHouseCost());

        $this->gameBuildingRepository->save($gameBuilding, true);
        $this->playerRepository->save($player, true);

        $this->gameStreamService->sendUpdatePlayer($game, $player, true);
        $this->gameStreamService->sendUpdateField($game, $gameBuilding);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sellHouse(Game $game, Player $player, GameBuilding $gameBuilding): void
    {
        if (
            $gameBuilding->getOwner() !== $player ||
            !$this->wholeStreetOwned($game, $gameBuilding->getBuilding())
        ) {
            return;
        }

        $streetBuildings = $this->gameBuildingRepository->findByGameAndStreet(
            $game,
            $gameBuilding->getBuilding()->getStreet()
        );

        $minHouses = 5;
        $maxHouses = 0;
        foreach ($streetBuildings as $streetBuilding) {
            if ($streetBuilding->getHouses() > $maxHouses) {
                $maxHouses = $streetBuilding->getHouses();
            } elseif ($streetBuilding->getHouses() < $minHouses) {
                $minHouses = $streetBuilding->getHouses();
            }
        }

        if ($gameBuilding->getHouses() != $maxHouses) {
            return;
        }

        $gameBuilding->removeHouse();
        $player->addMoney($gameBuilding->getBuilding()->getStreet()->getHouseCost() / 2);

        $this->gameBuildingRepository->save($gameBuilding, true);
        $this->playerRepository->save($player, true);

        $this->gameStreamService->sendUpdatePlayer($game, $player, true);
        $this->gameStreamService->sendUpdateField($game, $gameBuilding);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function callCardFunction(Game $game, Player $player, GameCard $gameCard): void
    {
        $function = $gameCard->getCard()->getFunction();
        $parts = explode('.', $function);
        $method = $parts[0];

        if ($function == 'moveToNextRailroad') {
            $currentPosition = $player->getPosition();
            $railroads = $this->gameActionFieldRepository->findByGameAndFunction(
                $player->getGame()->getId(),
                GameFunctions::FUNCTION_RAILROAD
            );

            usort($railroads, static function (GameActionField $a, GameActionField $b) {
                return $a->getField()->getPosition() <=> $b->getField()->getPosition();
            });

            foreach ($railroads as $railroad) {
                if ($railroad->getField()->getPosition() > $currentPosition) {
                    $this->turn($player->getGame(), $player, $railroad->getField()->getPosition());
                    return;
                }
            }

            $this->turn($player->getGame(), $player, 5);
        } elseif ($function == 'moveTo') {
            $this->turn($game, $player, (int)$parts[1]);
        } else {
            if (method_exists($this->gameFunctions, $method)) {
                if (count($parts) > 1) {
                    $this->gameFunctions->$method($player, (int)$parts[1]);
                } else {
                    $this->gameFunctions->$method($player);
                }
            } else {
                throw new Exception('This should not happen.');
            }

            $this->gameCardRepository->save($gameCard, true);
            $this->playerRepository->save($player, true);

            $this->gameStreamService->sendUpdatePlayer($game, $player, true);
        }
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws Exception
     */
    public function prisonBail(Game $game, Player $player, string $option): void
    {
        if ($option === 'pay') {
            $player->subtractMoney(self::PRISON_BAIL);
        } elseif ($option === 'card') {
            $freeFromJailCard = $player->getGameCards()->first();

            if ($freeFromJailCard === null) {
                throw new Exception('This should not happen.');
            }

            $freeFromJailCard->setOwner(null);
            $player->removeCard($freeFromJailCard);

            $this->gameCardRepository->save($freeFromJailCard, true);
        } else {
            throw new Exception('This should not happen.');
        }

        $player->setPrisonTurns(null);

        $this->playerRepository->save($player, true);
        $this->gameStreamService->sendUpdatePlayer($game, $player, true);
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

    private function getPriceByFunction(string|null $function): int
    {
        if ($function === null) {
            return 0;
        }

        return match ($function) {
            GameFunctions::FUNCTION_RAILROAD => self::RAILROAD_PRICE,
            GameFunctions::FUNCTION_UTILITY => self::UTILITY_PRICE,
            default => 0,
        };
    }
}
