<?php

namespace app\commands\get_stats_hh;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait OutputTrait
 * @package app\commands\common
 *
 * @property array $config
 */
trait OutputTrait
{
    /** @var OutputInterface */
    private $output;

    private function removeOldOutputLogIfNeed() {
        if(file_exists($this->config['paths']['get_stats_output_log'])) {
            unlink($this->config['paths']['get_stats_output_log']);
        }
    }

    /**
     * @param string $msg
     */
    private function outputInfo($msg) {
        $msg = date('Y-m-d H:i:s') . ' [INFO] ' . $msg;
        $this->output->writeln($msg);
        $this->outputToLog($msg);
    }

    /**
     * @param string $msg
     */
    private function outputError($msg) {
        $msg = date('Y-m-d H:i:s') . ' [ERROR] ' . $msg;
        $this->output->writeln("<error>{$msg}</error>");
        $this->outputToLog($msg);
    }

    private function outputEol() {
        $this->output->write(PHP_EOL);
    }

    /**
     * @param string $msg
     */
    private function outputToLog($msg) {
        file_put_contents($this->config['paths']['get_stats_output_log'], $msg . PHP_EOL, FILE_APPEND);
    }
}