<?php

namespace App\Service;

use App\Entity\ActionField;
use App\Entity\Building;
use App\Entity\Game;
use App\Entity\GameBuilding;
use App\Entity\GameCard;
use App\Entity\GameField;
use App\Entity\Player;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class GameStreamService
{
    public function __construct(
        private HubInterface $hub,
        private Environment $twig,
    ) {}

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function sendUpdateField(Game $game, GameField $field, int $price = 0): void
    {
        if ($field instanceof GameBuilding) {
            $this->send(
                sprintf('https://webbboly/game/%d', $game->getCode()),
                $this->twig->render('game/stream/_update-building-field.stream.html.twig',
                    compact('field')
                )
            );
        } else {
            $this->send(
                sprintf('https://webbboly/game/%d', $game->getCode()),
                $this->twig->render('game/stream/_update-action-field.stream.html.twig',
                    compact('field', 'price')
                )
            );
        }
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function sendUpdatePlayer(Game $game, Player $player, $isMyTurn = false): void
    {
        $isMe = false;
        $this->send(
            sprintf('https://webbboly/game/%d', $game->getCode()),
            $this->twig->render('game/stream/_update-player.stream.html.twig',
                compact('player', 'isMe', 'isMyTurn')
            )
        );

        $isMe = true;
        $this->send(
            sprintf('https://webbboly/game/%d/player/%d', $game->getCode(), $player->getNumber()),
            $this->twig->render('game/stream/_update-player.stream.html.twig',
                compact('player', 'isMe', 'isMyTurn')
            )
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendGameStart(Game $game): void
    {
        $this->send(
            sprintf('https://webbboly/game/%d', $game->getCode()),
            $this->twig->render('game/stream/_game-start.stream.html.twig',
                compact('game')
            )
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendShowBuildingCard(Building $building, Player $player): void
    {
        $this->send(
            sprintf('https://webbboly/game/%d/player/%d', $player->getGame()->getCode(), $player->getNumber()),
            $this->twig->render('game/stream/_show-building-card.stream.html.twig',
                compact('building', 'player')
            )
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendShowActionFieldCard(ActionField $actionField, Player $player, int $price): void
    {
        $this->send(
            sprintf('https://webbboly/game/%d/player/%d', $player->getGame()->getCode(), $player->getNumber()),
            $this->twig->render('game/stream/_show-action-field-card.stream.html.twig',
                compact('actionField', 'player', 'price')
            )
        );
    }

    public function sendEndTurn(Game $game, Player $player, Player $nextPlayer): void
    {
        $this->send(
            sprintf('https://webbboly/game/%d/player/%d/turn', $game->getCode(), $player->getNumber()),
            json_encode([
                'event' => 'end-turn',
                'position' => $player->getPosition(),
            ])
        );

        $this->send(
            sprintf('https://webbboly/game/%d/player/%d/turn', $game->getCode(), $nextPlayer->getNumber()),
            json_encode([
                'event' => 'turn',
                'position' => $nextPlayer->getPosition(),
            ])
        );
    }

    public function sendTurnRolled(Game $game, Player $player): void
    {
        $this->send(
            sprintf('https://webbboly/game/%d/player/%d/turn', $game->getCode(), $player->getNumber()),
            json_encode([
                'event' => 'turn-rolled',
                'position' => $player->getPosition(),
            ])
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendPrisonBailOptions(Game $game, Player $player): void
    {
        $this->send(
            sprintf('https://webbboly/game/%d/player/%d', $game->getCode(), $player->getNumber()),
            $this->twig->render('game/stream/_show-prison-bail-options.stream.html.twig',
                compact('player')
            )
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendBankrupt(Game $game, Player $player, Player|null $perpetrator): void
    {
        $this->send(
            sprintf('https://webbboly/game/%d/player/%d', $game->getCode(), $player->getNumber()),
            $this->twig->render('game/stream/_show-bankruptcy-options.stream.html.twig',
                compact('player', 'perpetrator')
            )
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendGameEnd(Game $game): void
    {
        $this->send(
            sprintf('https://webbboly/game/%d', $game->getCode()),
            $this->twig->render('game/stream/_game-end.stream.html.twig',
                compact('game')
            )
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendShowCard(GameCard $gameCard, Player $player): void
    {
        $this->send(
            sprintf('https://webbboly/game/%d/player/%d', $player->getGame()->getCode(), $player->getNumber()),
            $this->twig->render('game/stream/_show-card.stream.html.twig',
                compact('gameCard', 'player')
            )
        );
    }

    private function send(string $topic, string $data): void
    {
        $this->hub->publish(new Update($topic, $data));
    }
}
