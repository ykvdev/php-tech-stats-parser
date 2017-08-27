<?php

namespace app\commands\GetStatsHh;

use Symfony\Component\Console\Output\OutputInterface;

class Output
{
    /** @var OutputInterface */
    private $output;

    /** @var string */
    private $outputLogPath;

    public function __construct(OutputInterface $output, $outputLogPath) {
        $this->output = $output;
        $this->outputLogPath = $outputLogPath;
        $this->removeOldLogIfNeed();
    }

    /**
     * @param string $msg
     */
    public function info($msg) {
        $msg = date('Y-m-d H:i:s') . ' [INFO] ' . $msg;
        $this->output->writeln($msg);
        $this->toLog($msg);
    }

    /**
     * @param string $msg
     */
    public function error($msg) {
        $msg = date('Y-m-d H:i:s') . ' [ERROR] ' . $msg;
        $this->output->writeln("<error>{$msg}</error>");
        $this->toLog($msg);
    }

    public function eol() {
        $this->output->write(PHP_EOL);
    }

    private function removeOldLogIfNeed() {
        if(file_exists($this->outputLogPath)) {
            unlink($this->outputLogPath);
        }
    }

    /**
     * @param string $msg
     */
    private function toLog($msg) {
        file_put_contents($this->outputLogPath, $msg . PHP_EOL, FILE_APPEND);
    }
}