<?php
declare(strict_types=1);

namespace App\Services\Example;

use App\Kernel\ServiceProviderInterface;
use Closure;
use Pimple\Container;

class ExampleServiceProvider implements ServiceProviderInterface
{

    /**
     * Service register name
     */
    public function name(): string
    {
        return 'example';
    }

    /**
     * Register new service on dependency container
     *
     * @param Container $container
     * @return Closure
     */
    public function register(Container $container): Closure
    {
        return function (Container $container) {
            unset($container);

            return new Example();
        };
    }
}
