<?php

namespace App\Twig\Components;

use App\Entity\ActionField;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class ActionFieldCardComponent
{
    public ActionField $actionField;
}
