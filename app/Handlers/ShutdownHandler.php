<?php
declare(strict_types=1);

namespace App\Handlers;

use ErrorException;

class ShutdownHandler
{

    /**
     * Better Handler for PHP Exceptions
     */
    public function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error !== null) {
            $message = ucfirst($error['message']);
            $type = $error['type'];
            $file = $error['file'] ?? null;
            $line = $error['line'] ?? null;

            $exception = new ErrorException($message, 0, $type, $file, $line);

            (new ErrorHandler())->render($exception);
        }
    }
}
