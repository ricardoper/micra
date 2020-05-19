<?php
declare(strict_types=1);

namespace App\Handlers\Renderers;

use App\Handlers\Helpers\Severity;
use App\Handlers\Interfaces\ErrorRendererInterface;
use Throwable;

class PlainTextErrorRenderer implements ErrorRendererInterface
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
        $text = sprintf("Type: %s\n", get_class($exception));

        $code = $exception->getCode();
        if ($code !== null) {
            $text .= sprintf("Code: %s\n", $code);
        }

        $message = $exception->getMessage();
        if ($message !== null) {
            $text .= sprintf("Message: %s\n", ucfirst($message));
        }

        $file = $exception->getFile();
        if ($file !== null) {
            $text .= sprintf("File: %s\n", $file);
        }

        $line = $exception->getLine();
        if ($line !== null) {
            $text .= sprintf("Line: %s\n", $line);
        }

        try {
            $severity = $exception->getSeverity();
            if ($severity !== null) {
                $severity = (new Severity())->getSeverity($severity);

                $text .= sprintf("Severity: %s\n", strtoupper($severity));
            }
        } catch (Throwable $e) {
        }

        $trace = $exception->getTraceAsString();
        if ($trace !== null) {
            $text .= sprintf("\nTrace:\n%s", $trace);
        }

        return $text;
    }
}
