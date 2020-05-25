# PHP Micra Framework for CLI Apps

**Micra** is a CLI framework for your apps with a minimum overhead based on [Symfony Components](https://symfony.com/components).

- PHP >= 7.2
- Customizable with an easy configuration:
  + Models
  + Commands
  + Handlers
  + Configurations
  + Service Providers
  + Error Handler
  + Shutdown Handler
- [PSR-3 Logger](https://www.php-fig.org/psr/psr-3/)
- Global Helpers
- Better & More Verbose Error Handler
- Environment variables with [PHP dotenv](https://github.com/vlucas/phpdotenv)
- [Pimple](https://pimple.symfony.com/) Dependency Injection Container
- [Medoo](https://medoo.in/) Database Framework (MySQL, PostgreSQL, SQLite, MS SQL Server, ...)

## Table of Contents
- [How to Install](#how-to-install)
- [Most Relevant Folders](#most-relevant-folders)
- [Global Helpers](#global-helpers)
  - [Development Only](#global-helpers-for-development-only)
- [Configurations](#configurations)
  - [Dot Notation](#configurations-dot-notation)
- [Commands](#commands)
  - [Helpers](#commands-helpers)
  - [Inbuilt](#inbuilt-commands)
- [Run Commands](#run-commands)
- [Models](#models)
  - [Models Helpers](#models-helpers)
- [Services Providers](#services-providers)
- [Handlers](#handlers)
- [Database Support](#database-support)
- [Exceptions](#exceptions)
- [Logging](#logging)
- [Debugging](#debugging)
- [Demo](#demo)
- [Benchmarks](#benchmarks)

---

## How to Install

Run this command from the directory in which you want to install your new **Micra Framework**.

```bash
composer create-project ricardoper/micra [my-app-name]
```

**NOTE**:<br>
- Replace `[my-app-name]`with the desired directory name for your new application.
- Ensure `storage/` is web writeable.

## Most Relevant Folders

- /app : *Application* code (**App** Namespace - [PSR-4](https://www.php-fig.org/psr/psr-4/))
  + ./Commands : The start point of your tasks. Add your *Controllers* here.
  + ./Handlers : Handles specified behaviors of the application. Add your *Handlers* here.
  + ./Models : Manages the data, logic and rules of the application. Add your *Models* here.
  + ./Services : Define bindings and inject dependencies. Add your *Service Providers* here.
- /configs : Add/modify your *Configurations* here

## Global Helpers

- `env(string $variable, string $default)` - Returns *environment* variables (using DotEnv)
- `app()` - Returns *App* instance
- `container(string $name)` - Returns *Container* registered data
- `configs(string $variable, string $default)` - Returns *Configuration* data
- `base_path(string $path)` - Returns *base path* location
- `app_path(string $path)` - Returns *app path* location
- `configs_path(string $path)` - Returns *configs path* location
- `storage_path(string $path)` - Returns *storage path* location

### Global Helpers for Development Only
- `dump($var1, $var2, ...)` - Dump vars
- `dd($var1, $var2, ...)` - Dump & die vars

## Configurations

You can add as many configurations files as you want (`/configs`). These files *will be automatically preload and merged* in the container based on selected environment.

If you have an environment called "sandbox" and you want to overwrite some configuration only for this environment, you need to create a subfolder "sandbox" in `/configs`. Something like that `/configs/sandbox`. Then create the file that includes the configuration that you need to replace and the respective keys and values inside it.

`/configs/logger.php`
```php
return [

    'name' => 'app',

    'maxFiles' => 7,

];
```

`/configs/local/logger.php`
```php
return [

    'name' => 'app-local',

];
```

Results of `name` for the environment:
- prod : 'app'
- sandbox : 'app'
- **local : 'app-local'**
- testing : 'app'

**NOTE**: You can see the example in this framework for the *local* environment.

### Configurations Dot Notation

You can use **dot notation** to get values from configurations.

`/configs/example.php`
```php
return [

    'types' => [
        'mysql' => [
            'host' => 'localhost',
            'port' => '3306',
        ],
        'postgre' => [
            'host' => 'localhost',
            'port' => '3306',
        ],
    ],

];
```

If you want the `host` value for MySQL type:
```php
$this->getConfigs('example.types.mysql.host')  => 'localhost'

configs('example.types.mysql.host') => 'localhost'

container('configs')->get('example.types.mysql.host') => 'localhost'
```

## Commands

*The start point of your tasks.*

You can add as many *Commands* as you want in a cleaning way (`/app/Commands`).

After add your *Command*, you can enable or disable it in `config/[env/]commands.php` configuration file.

**NOTE**: To have helpers you must extend the *Commands* with **ControllerAbstract** located in `\App\Kernel\Abstracts`.

```php
use App\Kernel\Abstracts\CommandAbstract;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloCommand extends CommandAbstract
{

    /**
     * Command name
     *
     * @var string
     */
    protected $cmdName = 'Hello';


    /**
     * Configure/explain command
     */
    protected function configure()
    {
        $this
            ->setName($this->cmdName)
            ->setDescription('Hello World!');

    }

    /**
     * Execute command process
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hello World!');

        return 0;
    }
}
```

Enable it in `config/[env/]commands.php`:
```php
use App\Commands\Demo\HelloCommand;

return [

    'hello' => HelloCommand::class,

];
```

### Commands Helpers

- `getApp()` - Returns *App* object
- `getContainer(string $name)` - Returns the App *Container*
- `getConfigs(string $name)` - Returns App *Configs*
- `getService(string $service)` - Returns *Service Provider* from container by name

### Inbuilt commands

- *Deps* : Is an in-built command to see if your system respect all the dependencies to run this framework.
- *Hello* : Is a command only for *local* environment (that can be deleted) only for demo purposes.
- *Example* : Is a command only for *local* environment (that can be deleted) only for demo purposes.
- *Addresses* : Is a command only for *local* environment (that can be deleted) only for demo purposes.

## Run Commands

To run Commands use `micra` PHP executable.<br>
```./micra [command-name]```

If you need help:<br>
```./micra -h```

If you need Command help:<br>
```./micra [command-name] -h```

## Models

*Manages the data, logic and rules of the application.*

You can add as many *Models* as you want in a cleaning way (`/app/Models`).

After add your *Models*, you use it for, for example, in a *Controller*.

**NOTE**: To have helpers you must extend the *Model* with **ModelAbstract** located in `\App\Kernel\Abstracts`.

```php
use App\Kernel\Abstracts\ModelAbstract;
use PDO;

class AddressesModel extends ModelAbstract
{

    /**
     * Get Last Addresses with Pdo
     *
     * @param int $limit
     * @return array
     */
    public function getLastWithPdo(int $limit = 25): array
    {
        /** @var $db PDO */
        $db = $this->getDb()->pdo;

        $sql = 'SELECT `address`.`address_id`,`address`.`address`,`address`.`address2`,`address`.`district`,`city`.`city`,`address`.`postal_code`,`address`.`phone` FROM `address` ';
        $sql .= 'LEFT JOIN `city` ON `address`.`city_id` = `city`.`city_id` ';
        $sql .= 'ORDER BY `address_id` DESC LIMIT 10';

        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
```

### Models Helpers

- `getApp()` - Returns *App* object
- `getContainer(string $name)` - Returns the App *Container*
- `getConfigs(string $name)` - Returns App *Configs*
- `getService(string $service)` - Returns *Service Provider* from container by name
- `getDb()` - Returns *Database* object

## Services Providers

*Define bindings and inject dependencies.*

You can add as many *Services Providers* as you want in a cleaning way (`/app/Services`).

After add your *Services Provider*, you can enable or disable it in `configs/services.php` configuration file.

**NOTE**: *Service Providers* must respect the **ServiceProviderInterface** located in `\App\Kernel\Interfaces`.

```php
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
        return function (Container $c) {
            unset($c);

            return new Example();
        };
    }
}
```

Enable it in `configs/services.php`:
```php
use App\Services\Example\ExampleServiceProvider;

return [

    'example' => ExampleServiceProvider::class,

];
```

## Handlers

*Handles specified behaviors of the application.*

You can override the following Handlers in a cleaning way (`/app/Handlers`):
- *ErrorHandler* (default located in `/app/Handlers/ErrorHandler`)
- *ShutdownHandler* (default located in `/app/Handlers/ShutdownHandler`)

After add your *Handler*, you can enable or disable it in `configs/app.php` configuration file.

```php
use App\Handlers\ErrorHandler;
use App\Handlers\ShutdownHandler;

return [

    // Handlers //
    'errorHandler' => ErrorHandler::class,

    'shutdownHandler' => ShutdownHandler::class,
```

## Database Support

*Medoo* is implemented out of box as a *Service Provider*. The use **is optional** and is not enabled by default.

To enable database support with *Medoo* you need to add this library/vendor with Composer:
```bash
composer require catfan/medoo
```

Once installed you need to enable the *Service Provider* in `configs/services.php`:
```php
use App\Services\Database\DatabaseServiceProvider;

return [

    'database' => DatabaseServiceProvider::class,

];
```

Now you are ready to use it...

If you need more details, documentation, api reference, please visit Medoo webpage:
[https://medoo.in/](https://medoo.in/)

**NOTES**:
- Don't forget to load PDO extensions for your database. For example, if you need MySQL, you need to install `pdo_mysql` PHP extensions.
- You can use another library as a *Service Provider* (Native drivers for MySQLi, PostgreSQL, MongoDB, Redis, ...).

## Exceptions

You have some *Exceptions* out the box, located in `\App\Kernel\Exceptions`, than you can use it:
```text
CommandException  - For Commands Exceptions
ConfigsException  - For Configurations Exceptions
ModelException    - For Models Exceptions
ServiceException  - For Service Providers Exceptions
```

## Logging

Logging is always enabled and you can see all the output in `/storage/logs/app-[date].log`.

**NOTE**: Logs will with more than 7 days will be deleted automatically by the Logger.

## Benchmarks for Hello Command

Some numbers...

**Machine:**<br/>
Intel® Core™ i5-8400 CPU @ 2.80GHz × 6<br>
16Gb RAM<br>
SSD<br>

**Versions:**<br/>
Ubuntu 20.04 LTS<br/>
Docker v19.03.8<br>
PHP v7.4.3<br/>
PHP v7.2.24<br/>
Zend OPcache enabled<br/>

|  | PHP v7.2 | PHP v7.4 |
| --- | :----: | :---: |
| Linux Time - real | 0m0.057s| 0m0.054s |
| Linux Time - user | 0m0.026s| 0m0.024s |
| Linux Time - sys| sys 0m0.031s| sys 0m0.030s |
| PHP - Time | 0.016968s | 0.015434s |
| PHP - Max Memory | 2414 KB | 2294 KB |
| PHP - Allocated Memory | 4096 KB | 4096 KB |
<br/>

___

### Enjoy the simplicity :oP
