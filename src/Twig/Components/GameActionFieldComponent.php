<?php

namespace App\Twig\Components;

use App\Entity\ActionField;
use App\Entity\Game;
use App\Entity\GameActionField;
use App\Repository\PlayerRepository;
use App\Service\ActionFieldRent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent]
final class GameActionFieldComponent extends AbstractFieldComponent
{
    public function __construct(
        PlayerRepository $playerRepository,
        private readonly ActionFieldRent $actionFieldRent,
    )
    {
        parent::__construct($playerRepository);
    }

    public GameActionField $field;
    public int|null $price = null;

    #[PreMount]
    public function preMount(array $data): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('field');
        $resolver->setAllowedTypes('field', GameActionField::class);
        $resolver->setDefined('price');
        $resolver->setAllowedTypes('price', ['int', 'null']);

        return $resolver->resolve($data);
    }

    public function getField(): ActionField
    {
        return $this->field->getField();
    }

    public function getRailroadRent(): int
    {
        return $this->actionFieldRent->getRailroadRent($this->field->getOwner());
    }

    public function getUtilityRent(): int
    {
        return $this->actionFieldRent->getUtilityRent($this->field->getOwner());
    }
}
