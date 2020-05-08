<?php
declare(strict_types=1);

namespace App\Kernel\Services\Logger\Handlers;

use DateTime;
use DateTimeZone;
use Exception;

/**
 * Stores logs to files that are rotated every day and a limited number of files are kept.
 *
 * usage example:
 *
 *   $log = new Logger('application');
 *   $rotating = new RotatingFileHandler("file.log", 7);
 *   $log->pushHandler($rotating);
 */
class RotatingFileHandler implements HandlerInterface
{

    /**
     * Max rotation files
     *
     * @var int
     */
    protected $maxFiles = 0;

    /**
     * Log filename
     *
     * @var string
     */
    protected $filename;

    /**
     * File date format
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d';

    /**
     * Filename format
     *
     * @var string
     */
    protected $filenameFormat = '{filename}-{date}';


    /**
     * RotatingFileHandler constructor
     *
     * @param $filename
     * @param int $maxFiles
     */
    public function __construct($filename, $maxFiles = 0)
    {
        $this->filename = $filename;

        $this->maxFiles = (int)$maxFiles;
    }

    /**
     * Handle record
     *
     * @param array $record
     * @return bool
     * @throws Exception
     */
    public function handle(array $record): bool
    {
        $this->rotate();

        return $this->write($record);
    }


    /**
     * Write file
     *
     * @param array $record
     * @return bool
     * @throws Exception
     */
    protected function write(array $record): bool
    {
        $data = '[' . $record['recorded'] . ']  ' . $record['channel'] . '.' . $record['level'] . '  ';

        $data .= $record['message'] . "\n";

        $data .= trim(implode('', $record['context']), "\n") . "\n\n";

        return file_put_contents($this->getTimedFilename(), $data, FILE_APPEND) !== false;
    }

    /**
     * Rotates the files
     *
     * @throws Exception
     */
    protected function rotate(): void
    {
        $timedFilename = $this->getTimedFilename();

        // Check if rotation is needed
        if (file_exists($timedFilename)) {
            return;
        }

        // Check unlimited files flag
        if ($this->maxFiles === 0) {
            return;
        }

        // Touch log file
        if (is_writable(dirname($timedFilename))) {
            touch($timedFilename);
        }

        // Check if exists files to remove
        $logFiles = glob($this->getGlobPattern());
        if ($this->maxFiles >= count($logFiles)) {
            return;
        }

        // Sorting the files by name to remove the older ones
        usort(
            $logFiles,
            function ($a, $b) {
                return strcmp($b, $a);
            }
        );

        // Remove older files
        foreach (array_slice($logFiles, $this->maxFiles) as $file) {
            if (is_writable($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Get timed filename
     *
     * @return string
     * @throws Exception
     */
    protected function getTimedFilename(): string
    {
        $time = new DateTime('now');


        $fileInfo = pathinfo($this->filename);

        $timedFilename = str_replace(
            ['{filename}', '{date}'],
            [$fileInfo['filename'], $time->format($this->dateFormat)],
            $fileInfo['dirname'] . '/' . $this->filenameFormat
        );

        if (!empty($fileInfo['extension'])) {
            $timedFilename .= '.' . $fileInfo['extension'];
        }

        return $timedFilename;
    }

    /**
     * Get Glob function pattern
     *
     * @return string
     */
    protected function getGlobPattern(): string
    {
        $fileInfo = pathinfo($this->filename);

        $glob = str_replace(
            ['{filename}', '{date}'],
            [$fileInfo['filename'], '*'],
            $fileInfo['dirname'] . '/' . $this->filenameFormat
        );

        if (!empty($fileInfo['extension'])) {
            $glob .= '.' . $fileInfo['extension'];
        }

        return $glob;
    }
}
