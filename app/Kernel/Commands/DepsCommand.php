<?php
declare(strict_types=1);

namespace App\Kernel\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DepsCommand extends Command
{

    /**
     * Command name
     *
     * @var string
     */
    protected $cmdName = 'Deps';


    /**
     * Configure/explain command
     */
    protected function configure()
    {
        $this
            ->setName($this->cmdName)
            ->setDescription('App Dependencies Checks');
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
        $checks = $this->getChecks();


        $rows = [];

        $loop = false;

        foreach ($checks as $block => $check) {
            if ($loop === true) {
                $rows[] = new TableSeparator();
            }

            $loop = true;


            $rows[] = [new TableCell(strtoupper($block), ['colspan' => 3])];
            $rows[] = new TableSeparator();

            foreach ($check as $index => $item) {
                if (!empty($item['validate'])) {
                    $check[$index]['validate'] = '<fg=green>OK</fg=green>';
                } else {
                    $check[$index]['validate'] = '<fg=red>FAIL</fg=red>';
                }

                $rows[] = [
                    $check[$index]['component'],
                    $check[$index]['version'],
                    $check[$index]['validate'],
                ];
            }
        }


        // Create Table //
        $output->writeln("\n");

        $table = new Table($output);

        $table
            ->setHeaders([
                'Component',
                'System Version',
                'Check',
            ])
            ->setRows($rows);

        $table->render();

        $output->writeln("\n");

        return 0;
    }

    /**
     * Process Checks
     *
     * @return array
     */
    protected function getChecks(): array
    {
        return [
            'Framework' => [
                [
                    'component' => 'PHP-CLI ( >= 7.2.5 )',
                    'validate' => ($this->existShellCommand('php') && version_compare($this->getPhpVersion(), '7.2.5') >= 0),
                    'version' => $this->getPhpVersion(),
                ],
                [
                    'component' => 'Zend OPcache module',
                    'validate' => extension_loaded('Zend OPcache'),
                    'version' => phpversion('Zend OPcache'),
                ],
                [
                    'component' => 'Mbstring module',
                    'validate' => extension_loaded('mbstring'),
                    'version' => phpversion('mbstring'),
                ],
                [
                    'component' => 'Composer binary',
                    'validate' => $this->existShellCommand('composer'),
                    'version' => $this->getComposerVersion(),
                ],
            ],
        ];
    }


    /**
     * Get PHP Version
     *
     * @return string
     */
    private function getPhpVersion(): string
    {
        // Set the Columns environment variable to 1000 to avoid the text truncation when executing the command //
        putenv("COLUMNS=1000");

        preg_match('/PHP\s+(?<version>.*?)\s+\(cli\)\s+/sm', @shell_exec('php --version'), $phpVersion);

        if (empty($phpVersion['version'])) {
            $phpVersion['version'] = '';
        }

        return $phpVersion['version'];
    }

    /**
     * Get Composer Version
     *
     * @return string
     */
    private function getComposerVersion(): string
    {
        // Set the Columns environment variable to 1000 to avoid the text truncation when executing the command //
        putenv("COLUMNS=1000");

        preg_match('/Composer\s+version\s+(?<version>.*?)\s+/sm', @shell_exec('composer --version'), $composerVersion);

        if (empty($composerVersion['version'])) {
            $composerVersion['version'] = '';
        }

        return $composerVersion['version'];
    }

    /**
     * Get cURL Version
     *
     * @return string
     */
    private function getCurlVersion(): string
    {
        // Set the Columns environment variable to 1000 to avoid the text truncation when executing the command //
        putenv("COLUMNS=1000");

        preg_match('/curl\s+(?<version>.*?)\s+/sm', @shell_exec('curl --version'), $curlVersion);

        if (empty($curlVersion['version'])) {
            $curlVersion['version'] = '';
        }

        return $curlVersion['version'];
    }

    /**
     * Is PHP Function Enabled
     *
     * @param string $func
     * @return bool
     */
    private function isPhpFuncEnabled(string $func): bool
    {
        $disabled = ini_get('disable_functions');

        if ($disabled) {
            $disabled = explode(',', $disabled);

            $disabled = array_map('trim', $disabled);

            return !in_array($func, $disabled);
        }

        return true;
    }

    /**
     * Verify If Shell Command Exists In The System
     *
     * @param string $cmd
     * @return bool
     */
    private function existShellCommand(string $cmd): bool
    {
        // Set the Columns environment variable to 1000 to avoid the text truncation when executing the command //
        putenv("COLUMNS=1000");

        $returnVal = shell_exec('which ' . $cmd);

        return (empty($returnVal) ? false : true);
    }
}
