<?php
declare(strict_types=1);

namespace App\Handlers\Interfaces;

use Throwable;

interface ErrorRendererInterface
{

    /**
     * Renders a Throwable
     *
     * @param Throwable $exception
     * @return string|null
     */
    public function render(Throwable $exception): ?string;
}
