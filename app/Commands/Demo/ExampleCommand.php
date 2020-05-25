<?php
declare(strict_types=1);

namespace App\Commands\Demo;

use App\Kernel\Abstracts\CommandAbstract;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExampleCommand extends CommandAbstract
{

    /**
     * Command name
     *
     * @var string
     */
    protected $cmdName = 'Example';


    /**
     * Configure/explain command
     */
    protected function configure()
    {
        $this
            ->setName($this->cmdName)
            ->setDescription('Example Command (Local Env Only)')
            ->setDefinition(
                new InputDefinition(
                    [
                        new InputArgument('arg1', InputArgument::OPTIONAL, 'Some input argument (arg1)'),
                        new InputArgument('arg2', InputArgument::OPTIONAL, 'Some input argument (arg2)'),
                        new InputOption('--opt1', '-o', InputOption::VALUE_NONE, 'Test option (opt1)'),
                        new InputOption('--opt2', '-p', InputOption::VALUE_NONE, 'Test option (opt2)'),
                        new InputOption('--dev', null, InputOption::VALUE_NONE, 'For dev proposes only'),
                        new InputOption('--sleep', null, InputOption::VALUE_OPTIONAL, 'For dev proposes only'),
                    ]
                )
            );

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
        $output->writeln('');


        if ($input->getOption('dev')) {
            $output->writeln('<fg=yellow>DEV mode detected</fg=yellow>');
        } else {
            $output->writeln('<fg=blue>Try using --dev</fg=blue>');
        }
        $output->writeln('');


        if ($sleep = $input->getOption('sleep')) {
            $output->writeln('<fg=yellow>Sleeping ' . $sleep . 's...</fg=yellow>');
            sleep((int)$sleep);

            $output->writeln('<fg=green>Job Finished.</fg=green>');
        } else {
            $output->writeln('<fg=blue>Try using --sleep=[seconds]</fg=blue>');
        }
        $output->writeln('');


        if ($arg1 = $input->getArgument('arg1')) {
            dump('Given Arg1: ' . $arg1);
        } else {
            $output->writeln('<fg=blue>Try adding argument</fg=blue>');
        }

        if ($arg2 = $input->getArgument('arg2')) {
            dump('Given Arg2: ' . $arg2);
        } else {
            $output->writeln('<fg=blue>Try adding another argument</fg=blue>');
        }
        $output->writeln('');


        if ($input->getOption('opt1')) {
            dump('Option1 Selected');
        } else {
            $output->writeln('<fg=blue>Try using --opt1 or -o</fg=blue>');
        }

        if ($input->getOption('opt2')) {
            dump('Option2 Selected');
        } else {
            $output->writeln('<fg=blue>Try using --opt2 or -p</fg=blue>');
        }


        $output->writeln("\n\n <fg=yellow>--== Global Helpers ==--</fg=yellow>\n");

        $output->write(' * Base Path: ');
        dump(base_path());

        $output->write(' * App Path: ');
        dump(app_path());

        $output->write(' * Configs Path: ');
        dump(configs_path());

        $output->write(' * Storage Path: ');
        dump(storage_path());


        $output->write("\n * Container Example: ");
        dump(container('configs')->get('app.name'));
        dump($this->getContainer('configs')->get('app.name'));

        $output->write(" * Service Provider Example: ");
        dump(container('example')->capitalize('name'));
        dump($this->getService('example')->capitalize('name'));

        $output->write(' * Configs Example: ');
        dump(configs('app.timezone'));
        dump($this->getConfigs('app.timezone'));

        $output->write(' * App Env Example 1: ');
        dump(app()->getEnv());
        dump($this->getApp()->getEnv());

        $output->write(' * App Env Example 2: ');
        dump(configs('app.env'));

        $output->write(' * App Env Example 3: ');
        dump(env('APP_ENV'));


        $output->writeln("\n\n <fg=yellow>--== Array Helpers ==--</fg=yellow>\n");

        $output->writeln(' * Start Array: ');
        $sarr = [
            'app' => [
                'aKey1' => 'aVal1',
                'aKey2' => 'aVal2',
                'aKey3' => 'aVal3',
            ],
            'config' => [
                'cKey1' => 'cVal1',
                'cKey2' => 'cVal2',
                'cKey3' => 'cVal3',
            ],
        ];
        dump($sarr);

        $output->writeln("\n * Array Add ('app.newKey', 'newVal'): ");
        dump(array_add($sarr, 'app.newKey', 'newVal'));


        $output->write("\n * Array Get ('config.cKey2'): ");
        dump(array_get($sarr, 'config.cKey2'));

        $output->writeln("\n * See all array helpers in arrayHelpers.php\n");


        // ---- -- Exceptions -- ---- //

        // throw new CommandException('Command Exception Example');
        // throw new ConfigsException('Configs Exception Example');
        // throw new ModelException('Model Exception Example');
        // throw new ServiceException('Service Exception Example');

        // throw new \Exception('Exception Example');
        // throw new \RuntimeException('Exception Example');
        // throw new \InvalidArgumentException('Exception Example');


        return 0;
    }
}
