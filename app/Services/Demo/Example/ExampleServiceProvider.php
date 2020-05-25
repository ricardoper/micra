<?php
declare(strict_types=1);

namespace App\Services\Demo\Example;

use App\Kernel\Interfaces\ServiceProviderInterface;
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
     * @return mixed
     */
    public function register(Container $container)
    {
        return function (Container $container) {
            unset($container);

            return new Example();
        };
    }
}
