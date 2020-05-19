<?php
declare(strict_types=1);

namespace App\Kernel;

use App\Kernel\Exceptions\KernelException;
use App\Kernel\Interfaces\ServiceProviderInterface;
use App\Kernel\Services\Configs\Configs;
use Pimple\Container;
use Symfony\Component\Console\Application;

class App extends Application
{

    /**
     * Instance
     *
     * @var self
     */
    protected static $instance;

    /**
     * Environment name
     *
     * @var string
     */
    protected $env = 'prod';

    /**
     * Container
     *
     * @var Container
     */
    protected $container = null;


    /**
     * Application constructor
     *
     * @throws KernelException
     */
    public function __construct()
    {
        static::$instance = $this;

        $this->setContainer();


        $this->registerBootstrapServices();

        $this->setAppEnv();

        $this->setExceptionBehaviour();


        $this->registerServiceProviders();

        $this->registerCommands();


        $configs = $this->container['configs'];
        parent::__construct($configs->get('app.name'), $configs->get('app.version'));
    }

    /**
     * Get App Instance
     *
     * @return self
     */
    public static function getInstance(): self
    {
        return static::$instance;
    }

    /**
     * Get Container
     *
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Get Configs
     *
     * @return Configs
     */
    public function getConfigs(): Configs
    {
        return $this->container['configs'];
    }

    /**
     * Set environment
     *
     * @param string $env
     */
    public function setEnv(string $env): void
    {
        $this->env = $env;

        $this->container['configs']->set('app.env', $env);
    }

    /**
     * Get environment
     *
     * @return string
     */
    public function getEnv(): string
    {
        return $this->env;
    }


    /**
     * Set Container
     */
    protected function setContainer()
    {
        $this->container = new Container();
    }

    /**
     * Register Bootstrap Service Providers
     *
     * @throws KernelException
     */
    protected function registerBootstrapServices(): void
    {
        $services = require base_path('bootstrap/services.php');

        if (is_array($services) && !empty($services)) {
            foreach ($services as $service) {
                if (!class_exists($service)) {
                    throw new KernelException('Bootstrap Service Provider "' . $service . '" Not Found');
                }

                /**
                 * @var $instance ServiceProviderInterface
                 */
                $instance = new $service();

                $this->container[$instance->name()] = $instance->register($this->container);
            }
        }
    }

    /**
     * Set App Environment
     */
    public function setAppEnv(): void
    {
        $this->env = $this->container['configs']->get('app.env', $this->env);
    }

    /**
     * Set Exceptions Behaviour
     */
    protected function setExceptionBehaviour()
    {
        $this->setCatchExceptions(false);

        $configs = $this->container['configs']->get('handlers');


        $errorHandler = new $configs['errorHandler']();

        set_error_handler([$errorHandler, 'handleError']);

        set_exception_handler([$errorHandler, 'handleException']);


        $shutdownHandler = new $configs['shutdownHandler']();

        register_shutdown_function([$shutdownHandler, 'handleShutdown']);
    }

    /**
     * Register Service Providers
     *
     * @throws KernelException
     */
    protected function registerServiceProviders(): void
    {
        $services = $this->container['configs']->get('services');

        if (is_array($services) && !empty($services)) {
            foreach ($services as $service) {
                if (!class_exists($service)) {
                    throw new KernelException('Service Provider "' . $service . '" Not Found');
                }

                /**
                 * @var $instance ServiceProviderInterface
                 */
                $instance = new $service();

                $this->container[$instance->name()] = $instance->register($this->container);
            }
        }
    }

    /**
     * Register Console Commands (included Bootstrap)
     *
     * @throws KernelException
     */
    protected function registerCommands(): void
    {
        $commands = require base_path('bootstrap/commands.php');

        $commands = array_merge($commands, $this->container['configs']->get('commands'));

        if (is_array($commands) && !empty($commands)) {
            foreach ($commands as $command) {
                if (!class_exists($command)) {
                    throw new KernelException('Command "' . $command . '"" Not Found');
                }

                $this->add(new $command);
            }
        }
    }
}
