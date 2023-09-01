<?php

namespace App\Twig\Components;

use App\Entity\Game;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class StartGameLinkComponent
{
    public Game $game;
}
