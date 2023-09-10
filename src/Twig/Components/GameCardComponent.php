<?php

namespace App\Twig\Components;

use App\Entity\Card;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class GameCardComponent
{
    public Card $card;
}
