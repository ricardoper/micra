<?php
declare(strict_types=1);

namespace App\Kernel;

use App\Kernel\Exceptions\KernelException;
use App\Kernel\Services\Configs\Configs;
use Pimple\Container;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\ErrorHandler\ErrorHandler;
use Throwable;

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
    protected $env = 'production';

    /**
     * Container
     *
     * @var Container
     */
    protected $container = null;


    /**
     * Ignore Exceptions List
     *
     * @var string[]
     */
    protected $ignoreExceptions = [
        CommandNotFoundException::class,
    ];

    /**
     * Ignore Exceptions Files
     *
     * @var string[]
     */
    protected $ignoreExceptionsFiles = [
        'Input/ArgvInput.php',
    ];


    /**
     * Error Levels
     *
     * @var array
     */
    private $errorLevels = [
        E_DEPRECATED => LogLevel::INFO,
        E_USER_DEPRECATED => LogLevel::INFO,
        E_NOTICE => LogLevel::WARNING,
        E_USER_NOTICE => LogLevel::WARNING,
        E_STRICT => LogLevel::WARNING,
        E_WARNING => LogLevel::WARNING,
        E_USER_WARNING => LogLevel::WARNING,
        E_COMPILE_WARNING => LogLevel::WARNING,
        E_CORE_WARNING => LogLevel::WARNING,
        E_USER_ERROR => LogLevel::CRITICAL,
        E_RECOVERABLE_ERROR => LogLevel::CRITICAL,
        E_COMPILE_ERROR => LogLevel::CRITICAL,
        E_PARSE => LogLevel::CRITICAL,
        E_ERROR => LogLevel::CRITICAL,
        E_CORE_ERROR => LogLevel::CRITICAL,
    ];


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
     * Render Errors in Console
     *
     * @param Throwable $e
     * @param OutputInterface $output
     */
    public function renderThrowable(Throwable $e, OutputInterface $output): void
    {
        $ignoreException = (in_array(get_class($e), $this->ignoreExceptions) === false);

        $ignoreExcepFile = (in_array($this->getFileLevels($e->getFile()), $this->ignoreExceptionsFiles) === false);

        if ($ignoreException === true && $ignoreExcepFile === true) {
            $level = $this->getErrorLevel($e);

            $this->container['logger']->log($level, $e->getMessage(), $e ? ['exception' => $e] : []);
        }

        parent::renderThrowable($e, $output);
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
        $handler = ErrorHandler::register();
        $handler->setDefaultLogger($this->container['logger']);
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


    /**
     * Get FileLevels
     *
     * @param string $file
     * @param int $levels
     * @return string
     */
    private function getFileLevels(string $file, int $levels = 2): string
    {
        $file = explode('/', $file);

        $file = array_slice($file, -$levels);

        return implode('/', $file);
    }

    /**
     * Get Error Level
     *
     * @param Throwable $e
     * @return string
     */
    private function getErrorLevel(Throwable $e): string
    {
        try {
            $levelEx = explode(':', $e->getMessage());

            $level = constant('E_' . strtoupper($levelEx[0]));

            return $this->errorLevels[$level] ?? LogLevel::DEBUG;
        } catch (Throwable $ex) {
            return LogLevel::DEBUG;
        }
    }
}
