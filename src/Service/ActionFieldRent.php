<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Player;
use App\Repository\GameActionFieldRepository;

final readonly class ActionFieldRent
{
    public function __construct(
        private GameActionFieldRepository $gameActionFieldRepository,
    ) {}

    public function getRailroadRent(Player $player): int
    {
        $game = $player->getGame();
        $railroads = $this->getRailroads($game);
        $ownedRailroads = $this->getOwnedRailroads($player, $railroads);

        return 25 * 2 ** (count($ownedRailroads) - 1);
    }

    public function getUtilityRent(Player $player): int
    {

        $game = $player->getGame();
        $utilities = $this->getUtilities($game);
        $ownedUtilities = $this->getOwnedUtilities($player, $utilities);

        if ($player->getFieldsAdvanced() === 0) {
            if (count($ownedUtilities) === 1) {
                return 4;
            }

            return 10;
        }

        if (count($ownedUtilities) === 1) {
            return 4 * $player->getFieldsAdvanced();
        }

        return 10 * $player->getFieldsAdvanced();
    }

    public function getOwnedRailroads(Player $player, array $railroads): array
    {
        $ownedRailroads = [];

        foreach ($railroads as $railroad) {
            if ($railroad->getOwner() === $player) {
                $ownedRailroads[] = $railroad;
            }
        }

        return $ownedRailroads;
    }

    public function getOwnedUtilities(Player $player, array $utilities): array
    {
        $ownedUtilities = [];

        foreach ($utilities as $utility) {
            if ($utility->getOwner() === $player) {
                $ownedUtilities[] = $utility;
            }
        }

        return $ownedUtilities;
    }

    public function getRailroads(Game $game): array
    {
        return $this->gameActionFieldRepository->findByGameAndFunction($game->getId(), GameFunctions::FUNCTION_RAILROAD);
    }

    public function getUtilities(Game $game): array
    {
        return $this->gameActionFieldRepository->findByGameAndFunction($game->getId(), GameFunctions::FUNCTION_UTILITY);
    }
}
