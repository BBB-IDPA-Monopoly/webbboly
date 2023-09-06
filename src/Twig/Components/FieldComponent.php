<?php

namespace App\Twig\Components;

use App\Entity\GameActionField;
use App\Entity\GameBuilding;
use App\Entity\GameField;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class FieldComponent
{
    public GameField $field;

    public function isBuilding(): bool
    {
        return $this->field instanceof GameBuilding;
    }

    public function isActionField(): bool
    {
        return $this->field instanceof GameActionField;
    }
}
