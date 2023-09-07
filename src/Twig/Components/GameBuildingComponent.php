<?php

namespace App\Twig\Components;

use App\Entity\Building;
use App\Entity\Game;
use App\Entity\GameBuilding;
use App\Repository\PlayerRepository;
use App\Service\GameService;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent]
final class GameBuildingComponent extends AbstractFieldComponent
{
    public GameBuilding $field;

    public function __construct(
        private readonly GameService $gameService,
        private readonly PlayerRepository $playerRepository,
    )
    {
        parent::__construct($this->playerRepository);
    }

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

    public function wholeStreetOwned(): bool
    {
        return $this->gameService->wholeStreetOwned($this->getGame(), $this->getField());
    }
}
