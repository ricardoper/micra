<?php
declare(strict_types=1);

namespace App\Handlers;

use App\Handlers\Helpers\Severity;
use App\Handlers\Renderers\CliErrorRenderer;
use App\Handlers\Renderers\PlainTextErrorRenderer;
use ErrorException;
use Throwable;

class ErrorHandler
{

    /**
     * Default Renderers
     *
     * @var string[]
     */
    protected $renderers = [
        'cli' => CliErrorRenderer::class,
        'plain' => PlainTextErrorRenderer::class,
    ];


    /**
     * Better Handler for PHP Exceptions
     *
     * @param int $type
     * @param string $message
     * @param string $file
     * @param int $line
     * @throws ErrorException
     */
    public function handleError(int $type, string $message, string $file, int $line): void
    {
        throw new ErrorException(ucfirst($message), 0, $type, $file, $line);
    }

    /**
     * Better Handler for PHP Exceptions
     *
     * @param Throwable $exception
     */
    public function handleException(Throwable $exception): void
    {
        $this->render($exception);
    }

    /**
     * Render PHP Exceptions
     *
     * @param Throwable $exception
     */
    public function render(Throwable $exception): void
    {
        (new $this->renderers['cli'])->render($exception);

        $this->log($exception);
    }


    /**
     * Log PHP Exceptions
     *
     * @param Throwable $exception
     */
    protected function log(Throwable $exception): void
    {
        $level = (new Severity())->getSeverity($exception->getCode());

        $message = (new $this->renderers['plain'])->render($exception);

        container('logger')->log(
            $level,
            $message
        );
    }
}
