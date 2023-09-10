<?php

namespace App\Service;

use App\Entity\GameCard;
use App\Entity\Player;
use App\Enum\CardType;
use App\Repository\CardRepository;
use App\Repository\GameActionFieldRepository;
use App\Repository\GameCardRepository;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class GameFunctions
{
    const FUNCTION_START = 'start';
    const FUNCTION_INCOME_TAX = 'incomeTax';
    const FUNCTION_LUXURY_TAX = 'luxuryTax';
    const FUNCTION_JAIL = 'jail';
    const FUNCTION_FREE_PARKING = 'freeParking';
    const FUNCTION_GO_TO_JAIL = 'goToJail';
    const FUNCTION_CHANCE = 'chance';
    const FUNCTION_COMMUNITY_CHEST = 'communityChest';
    const FUNCTION_RAILROAD = 'railroad';
    const FUNCTION_UTILITY = 'utility';

    public function __construct(
        private PlayerRepository  $playerRepository,
        private GameRepository    $gameRepository,
        private CardRepository    $cardRepository,
        private GameCardRepository $gameCardRepository,
        private GameStreamService $gameStreamService,
        private ActionFieldRent   $actionFieldRent,
        private GameActionFieldRepository $gameActionFieldRepository,
    ) {}

    public function start(Player $player): void
    {
        $player->addMoney(GameService::GO_MONEY);
    }

    public function incomeTax(Player $player): void
    {
        $player->subtractMoney(GameService::INCOME_TAX);
        $player->getGame()->addFunds(GameService::INCOME_TAX);

        $this->playerRepository->save($player, true);
        $this->gameRepository->save($player->getGame(), true);
    }

    public function luxuryTax(Player $player): void
    {
        $player->subtractMoney(GameService::LUXURY_TAX);
        $player->getGame()->addFunds(GameService::LUXURY_TAX);

        $this->playerRepository->save($player, true);
        $this->gameRepository->save($player->getGame(), true);
    }

    public function jail(Player $player): void
    {

    }

    public function freeParking(Player $player): void
    {
        $player->addMoney($player->getGame()->getFunds() ?? 0);
        $player->getGame()->setFunds(0);

        $this->playerRepository->save($player, true);
        $this->gameRepository->save($player->getGame(), true);
    }

    public function goToJail(Player $player): void
    {

    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function chance(Player $player): void
    {
        $gameCards = $player->getGame()->getGameCards(CardType::CHANCE);
        $gameCard = $gameCards[array_rand($gameCards->toArray())];

        $this->gameStreamService->sendShowCard($gameCard, $player);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function communityChest(Player $player): void
    {
        $gameCards = $player->getGame()->getGameCards(CardType::COMMUNITY_CHEST);
        $gameCard = $gameCards[array_rand($gameCards->toArray())];

        $this->gameStreamService->sendShowCard($gameCard, $player);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function railroad(Player $player): void
    {
        $railroad = $this->gameActionFieldRepository->findByGameAndPosition(
            $player->getGame()->getId(),
            $player->getPosition()
        );

        if ($railroad === null) {
            throw new Exception('Railroad not found');
        }

        if ($railroad->getOwner() === null) {
            $this->gameStreamService->sendShowActionFieldCard($railroad->getActionField(), $player, GameService::RAILROAD_PRICE);
        } elseif ($railroad->getOwner() !== $player) {
            $rent = $this->actionFieldRent->getRailroadRent($railroad->getOwner());
            $player->subtractMoney($rent);
            $railroad->getOwner()->addMoney($rent);

            $this->playerRepository->save($player, true);
            $this->playerRepository->save($railroad->getOwner(), true);

            $this->gameStreamService->sendUpdatePlayer($railroad->getOwner()->getGame(), $railroad->getOwner());
        }
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function utility(Player $player): void
    {
        $utility = $this->gameActionFieldRepository->findByGameAndPosition(
            $player->getGame()->getId(),
            $player->getPosition()
        );

        if ($utility === null) {
            throw new Exception('Utility not found');
        }

        if ($utility->getOwner() === null) {
            $this->gameStreamService->sendShowActionFieldCard($utility->getActionField(), $player, GameService::UTILITY_PRICE);
        } elseif ($utility->getOwner() !== $player) {
            $rent = $this->actionFieldRent->getUtilityRent($utility->getOwner());
            $player->subtractMoney($rent);
            $utility->getOwner()->addMoney($rent);

            $this->playerRepository->save($player, true);
            $this->playerRepository->save($utility->getOwner(), true);

            $this->gameStreamService->sendUpdatePlayer($utility->getOwner()->getGame(), $utility->getOwner());
        }
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function add(Player $player, int $amount): void
    {
        $player->addMoney($amount);

        $this->playerRepository->save($player, true);
        $this->gameStreamService->sendUpdatePlayer($player->getGame(), $player);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function subtract(Player $player, int $amount): void
    {
        $player->subtractMoney($amount);

        $this->playerRepository->save($player, true);
        $this->gameStreamService->sendUpdatePlayer($player->getGame(), $player);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function payAll(Player $player, int $amount): void
    {
        foreach ($player->getGame()->getPlayers() as $gamePlayer) {
            if ($gamePlayer !== $player) {
                $gamePlayer->addMoney($amount);
                $player->subtractMoney($amount);

                $this->playerRepository->save($gamePlayer, true);
                $this->gameStreamService->sendUpdatePlayer($gamePlayer->getGame(), $gamePlayer);
            }
        }

        $this->playerRepository->save($player, true);
        $this->gameStreamService->sendUpdatePlayer($player->getGame(), $player);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function collect(Player $player, int $amount): void
    {
        foreach ($player->getGame()->getPlayers() as $gamePlayer) {
            if ($gamePlayer !== $player) {
                $gamePlayer->subtractMoney($amount);
                $player->addMoney($amount);

                $this->playerRepository->save($gamePlayer, true);
                $this->gameStreamService->sendUpdatePlayer($gamePlayer->getGame(), $gamePlayer);
            }
        }

        $this->playerRepository->save($player, true);
        $this->gameStreamService->sendUpdatePlayer($player->getGame(), $player);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function freeFromJail(Player $player): void
    {
        $freeFromJailCard = (new GameCard())
            ->setGame($player->getGame())
            ->setOwner($player)
            ->setCard($this->cardRepository->findOneBy(['function' => 'freeFromJail']));

        $player->addCard($freeFromJailCard);

        $this->playerRepository->save($player, true);
        $this->gameCardRepository->save($freeFromJailCard, true);
        $this->gameStreamService->sendUpdatePlayer($player->getGame(), $player);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function repair(Player $player): void
    {
        $houses = 0;
        $hotels = 0;

        foreach ($player->getGame()->getGameBuildings() as $gameBuilding) {
            if ($gameBuilding->getOwner() === $player) {
                if ($gameBuilding->getHouses() === 5) {
                    $hotels++;
                } else {
                    $houses += $gameBuilding->getHouses();
                }
            }
        }

        $amount = $houses * GameService::REPAIR_HOUSE + $hotels * GameService::REPAIR_HOTEL;

        $player->subtractMoney($amount);
        $player->getGame()->addFunds($amount);

        $this->playerRepository->save($player, true);
        $this->gameRepository->save($player->getGame(), true);
        $this->gameStreamService->sendUpdatePlayer($player->getGame(), $player);
    }
}
