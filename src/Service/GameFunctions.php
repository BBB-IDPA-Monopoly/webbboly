<?php

namespace App\Service;

use App\Entity\GameActionField;
use App\Entity\Player;
use App\Repository\ActionFieldRepository;
use App\Repository\GameActionFieldRepository;
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
    }

    public function luxuryTax(Player $player): void
    {
        $player->subtractMoney(GameService::LUXURY_TAX);
        $player->getGame()->addFunds(GameService::LUXURY_TAX);
    }

    public function jail(Player $player): void
    {

    }

    public function freeParking(Player $player): void
    {
        $player->addMoney($player->getGame()->getFunds());
    }

    public function goToJail(Player $player): void
    {

    }

    public function chance(Player $player): void
    {

    }

    public function communityChest(Player $player): void
    {

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

            $this->playerRepository->save($player);
            $this->playerRepository->save($railroad->getOwner());

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

            $this->playerRepository->save($player);
            $this->playerRepository->save($utility->getOwner());

            $this->gameStreamService->sendUpdatePlayer($utility->getOwner()->getGame(), $utility->getOwner());
        }
    }
}
