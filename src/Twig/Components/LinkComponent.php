<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class LinkComponent
{
    public string $href;
    public string $text;
    public string $type = 'primary';
    public bool $disabled = false;
}
