<?php
declare(strict_types=1);

namespace App\Kernel\Services\Logger;

use App\Kernel\ServiceProviderInterface;
use Closure;
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
     * @return Closure
     */
    public function register(Container $container): Closure
    {
        return function ($container) {
            return new Logger($container['configs']->get('app.name'));
        };
    }
}
