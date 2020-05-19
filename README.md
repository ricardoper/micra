# PHP Micra Framework for CLI Apps

CLI framework for your apps with a minimum overhead based on [Symfony Components](https://symfony.com/components).

- PHP >= 7.2
- Customizable with an easy configuration:
  + Commands
  + Models
  + Service Providers
- [PSR-3 Logger](https://www.php-fig.org/psr/psr-3/)
- Global Helpers
- Environment variables with [PHP dotenv](https://github.com/vlucas/phpdotenv)
- [Pimple](https://pimple.symfony.com/) Dependency Injection Container

## How to install Micra Framework

Run this command from the directory in which you want to install your new **Micra Framework**.

```bash
composer create-project ricardoper/micra [my-app-name]
```

Replace `[my-app-name]`with the desired directory name for your new application. You'll want to:
- Ensure `storage/` is web writeable.

## Most relevant framework folders

- /app : *Application* code (PSR-4 **App** Namespace)
  + ./Commands : Add your *Commands* here
  + ./Models : Add your *Models* here
  + ./Services : Add your *Services* here
- /configs : Add/modify your *Configurations* here

## Helpers methods

- `env(string $variable, string $default)` - Returns *environment* variables (using DotEnv)
- `app()` - Returns *App* instance
- `container(string $name)` - Returns *Container* registered data
- `configs(string $variable, string $default)` - Returns *Configuration* data
- `base_path(string $path)` - Returns *base path* location
- `app_path(string $path)` - Returns *app path* location
- `configs_path(string $path)` - Returns *configs path* location
- `storage_path(string $path)` - Returns *storage path* location
- `dump($var1, $var2, ...)` - Dump vars
- `dd($var1, $var2, ...)` - Dump & die vars

## Commands

You can add as many *Commands* as you want in a cleaning way (`/app/Commands`).

After add your *Command*, you can enable or disable it in `config/[env/]commands.php` configuration file.

```php
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloCommand extends Command
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

## Inbuilt commands

- *Deps* : Is an in-built command to see if your system respect all the dependencies to run this framework.
- *Hello* : Is a command only for *local* environment (that can be deleted) only for demo purposes.
- *Example* : Is a command only for *local* environment (that can be deleted) only for demo purposes.

## Run Commands

To run Commands use `micra` PHP executable.<br>
```./micra [command-name]```

If you need help:<br>
```./micra -h```

If you need Command help:<br>
```./micra [command-name] -h```

## Services Providers

You can add as many *Services Providers* as you want in a cleaning way (`/app/Services`).

After add your *Services Provider*, you can enable or disable it in `config/[env/]services.php` configuration file.

**NOTE**: **Logger** is a *Service Provider*, it can be customized as any other *Service Provider* and you can replace it in `bootstrap/services.php` file.

```php
use App\Services\Example\ExampleServiceProvider;

return [

    ExampleServiceProvider::class,

];
```

*Service Providers* must respect the **ServiceProviderInterface** located in `/app/Kernel` folder.

Service Provider Example:
```php
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
```

## Models

You can add as many *Models* as you want in a cleaning way (`/app/Models`).

You can organize this models as you like.

```php
use App\Models\ModelAbstract;

class User extends ModelAbstract
{

    // Code Logic
}
```

## Configurations

You can add as many configurations files as you want (`/config`). These files *will be automatically preload* in the container based on selected environment.

If you have an environment called "sandbox" and you want to overwrite some configuration only for this environment, you need to create a subfolder "sandbox" in `/configs`. Something like that `/configs/sandbox`.

Then create the file that includes the configuration that you need to replace and the respective keys and values inside it.

You can see the example in this framework for the *local* environment.

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
