<?php

namespace App\Twig\Components;

use App\Entity\Building;
use App\Entity\Field;
use LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent]
final class FieldComponent
{
    const ROTATION_0 = 0;
    const ROTATION_90 = 90;
    const ROTATION_180 = 180;
    const ROTATION_270 = 270;

    public Field $field;

    public int $rotation;

    public function isBuilding(): bool
    {
        return $this->field instanceof Building;
    }

    public function getClasses(): string
    {
        return match ($this->rotation) {
            self::ROTATION_0 => 'cell',
            self::ROTATION_90 => 'cell cell-rotated-90',
            self::ROTATION_180 => 'cell cell-rotated-180',
            self::ROTATION_270=> 'cell cell-rotated-270',
            default => throw new LogicException('Invalid rotation'),
        };
    }

    #[PreMount]
    public function preMount(array $data): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('field');
        $resolver->setAllowedTypes('field', Field::class);
        $resolver->setDefaults([
            'rotation' => self::ROTATION_0,
        ]);

        return $resolver->resolve($data);
    }
}
