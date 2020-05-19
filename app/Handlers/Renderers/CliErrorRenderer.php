<?php
declare(strict_types=1);

namespace App\Handlers\Renderers;

use App\Handlers\Interfaces\ErrorRendererInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Throwable;

class CliErrorRenderer implements ErrorRendererInterface
{

    /**
     * Renders a Throwable
     *
     * @param Throwable $exception
     * @param bool $output
     * @return string|null
     */
    public function render(Throwable $exception, bool $output = false): ?string
    {
        return (new CliDumper())->dump((new VarCloner())->cloneVar($exception), $output);
    }
}
