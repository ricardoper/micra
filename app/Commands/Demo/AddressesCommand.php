<?php
declare(strict_types=1);

namespace App\Commands\Demo;

use App\Kernel\Abstracts\CommandAbstract;
use App\Models\Demo\AddressesModel;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddressesCommand extends CommandAbstract
{

    /**
     * Command name
     *
     * @var string
     */
    protected $cmdName = 'Addresses';


    /**
     * Configure/explain command
     */
    protected function configure()
    {
        $this
            ->setName($this->cmdName)
            ->setDescription('Addresses Example Command (Local Env Only)')
            ->setDefinition(
                new InputDefinition(
                    [
                        new InputOption('--pdo', '-p', InputOption::VALUE_NONE, 'Get data with native PDO'),
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


        if ($sleep = $input->getOption('pdo')) {
            $output->writeln('<fg=yellow>With native PDO...</fg=yellow>');
            $data = $this->pdo();
        } else {
            $data = $this->list();
        }
        $output->writeln('');


        $table = new Table($output);

        $table
            ->setHeaders([
                '#ID',
                'Address',
                'District',
                'City',
                'Postal Code',
                'Phone',
            ])
            ->setRows($data);

        $table->render();

        $output->writeln("\n");

        return 0;
    }

    /**
     * List Action
     *
     * @return array
     */
    protected function list(): array
    {
        return (new AddressesModel())->getLast();
    }

    /**
     * List With PDO Action
     *
     * @return array
     */
    protected function pdo(): array
    {
        return (new AddressesModel())->getLastWithPdo();
    }
}
