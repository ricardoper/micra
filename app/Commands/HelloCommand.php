<?php
declare(strict_types=1);

namespace App\Commands;

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
