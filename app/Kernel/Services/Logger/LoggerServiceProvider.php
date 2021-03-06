<?php
declare(strict_types=1);

namespace App\Kernel\Services\Logger;

use App\Kernel\Interfaces\ServiceProviderInterface;
use Pimple\Container;

class LoggerServiceProvider implements ServiceProviderInterface
{

    /**
     * Service register name
     */
    public function name(): string
    {
        return 'logger';
    }

    /**
     * Register new service on dependency container
     *
     * @param Container $container
     * @return mixed
     */
    public function register(Container $container)
    {
        return function (Container $container) {
            return new Logger($container);
        };
    }
}
