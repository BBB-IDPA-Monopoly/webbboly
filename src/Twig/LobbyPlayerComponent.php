<?php

namespace App\Twig;

use App\Entity\Player;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('LobbyPlayer')]
final class LobbyPlayerComponent
{
    public Player $player;
    public bool $isHost = false;
    public bool $isMe = false;
    public bool $forHost = false;
}
