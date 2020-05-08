<?php
declare(strict_types=1);

namespace App\Kernel\Services\Configs;

use App\Kernel\ServiceProviderInterface;
use Closure;
use Pimple\Container;

class ConfigsServiceProvider implements ServiceProviderInterface
{

    /**
     * Service register name
     */
    public function name(): string
    {
        return 'configs';
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
            return new Configs($container);
        };
    }
}
