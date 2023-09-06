<?php

namespace App\Twig\Components;

use App\Entity\ActionField;
use App\Entity\Game;
use App\Entity\GameActionField;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent]
final class GameActionFieldComponent extends AbstractFieldComponent
{
    public GameActionField $field;

    #[PreMount]
    public function preMount(array $data): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('field');
        $resolver->setAllowedTypes('field', GameActionField::class);
        $resolver->setDefaults([
            'rotation' => self::ROTATION_0,
        ]);

        return $resolver->resolve($data);
    }

    public function getField(): ActionField
    {
        return $this->field->getField();
    }

    public function getGame(): Game
    {
        return $this->field->getGame();
    }
}
