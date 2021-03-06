<?php
declare(strict_types=1);

namespace App\Kernel\Abstracts;

use App\Kernel\App;
use Pimple\Container;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class CommandAbstract extends SymfonyCommand
{

    /**
     * App
     *
     * @var App
     */
    protected $app;

    /**
     * Configs
     *
     * @var array
     */
    protected $configs;

    /**
     * App
     *
     * @var Container
     */
    protected $container;


    /**
     * Controller constructor
     */
    public function __construct()
    {
        $this->app = app();

        $this->configs = $this->app->getConfigs();

        $this->container = $this->app->getContainer();

        parent::__construct();
    }


    /**
     * Get App
     *
     * @return App
     */
    protected function getApp(): App
    {
        return $this->app;
    }

    /**
     * Get Container
     *
     * @param string|null $name
     * @return mixed|null
     */
    protected function getContainer(string $name = null)
    {
        if ($name === null) {
            return $this->container;
        }

        return $this->container[$name] ?? null;
    }

    /**
     * Get Configs
     *
     * @param string|null $name
     * @param mixed $default
     * @return mixed|null
     */
    protected function getConfigs(string $name = null, $default = null)
    {
        $configs = $this->configs ?? null;

        if ($name === null) {
            return $configs;
        } else if ($configs === null) {
            return $default;
        }

        return $configs->get($name) ?? $default;
    }

    /**
     * Get Service From Container
     *
     * @param string $name
     * @return mixed|null
     */
    protected function getService(string $name)
    {
        return $this->container[$name] ?? null;
    }
}
