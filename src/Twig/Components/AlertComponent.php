<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class AlertComponent
{
    const ALERT_TYPE_SUCCESS = 'success';
    const ALERT_TYPE_INFO = 'info';
    const ALERT_TYPE_WARNING = 'warning';
    const ALERT_TYPE_DANGER = 'danger';

    public string $type = 'success';
    public string $message;
    public bool $dismissible = true;

    public function getIconClass(): string
    {
        return match ($this->type) {
            'danger', 'warning' => 'fa fa-circle-exclamation',
            'info' => 'fa fa-circle-info',
            default => 'fa fa-circle-check',
        };
    }
}
