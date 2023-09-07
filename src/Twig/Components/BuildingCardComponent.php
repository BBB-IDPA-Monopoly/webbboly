<?php

namespace App\Twig\Components;

use App\Entity\Building;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class BuildingCardComponent
{
    public Building $building;
}
