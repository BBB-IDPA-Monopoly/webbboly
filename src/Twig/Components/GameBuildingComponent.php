<?php

namespace App\Twig\Components;

use App\Entity\Building;
use App\Entity\Game;
use App\Entity\GameBuilding;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent]
final class GameBuildingComponent extends AbstractFieldComponent
{
    public GameBuilding $field;

    #[PreMount]
    public function preMount(array $data): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('field');
        $resolver->setAllowedTypes('field', GameBuilding::class);

        return $resolver->resolve($data);
    }

    public function getField(): Building
    {
        return $this->field->getField();
    }

    public function getGame(): Game
    {
        return $this->field->getGame();
    }
}
