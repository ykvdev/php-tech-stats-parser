<?php declare(strict_types=1);

namespace app\commands\Common;

use Symfony\Component\Console\Output\OutputInterface;

class Output
{
    /** @var OutputInterface */
    private $output;

    /** @var string */
    private $outputLogPath;

    /**
     * @param OutputInterface $output
     * @param bool|string $outputLogPath
     */
    public function __construct(OutputInterface $output, $outputLogPath = false)
    {
        $this->output = $output;
        $this->outputLogPath = $outputLogPath;
        $this->removeOldLogIfNeed();
    }

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @param string $msg
     */
    public function info(string $msg): void
    {
        $msg = date('Y-m-d H:i:s ') . $msg;
        $this->output->writeln($msg);
        $this->toLog($msg);
    }

    /**
     * @param string $msg
     */
    public function error(string $msg): void
    {
        $msg = date('Y-m-d H:i:s') . ' ERROR: ' . $msg;
        $this->output->writeln("<error>{$msg}</error>");
        $this->toLog($msg);
    }

    public function eol(): void
    {
        $this->output->write(PHP_EOL);
    }

    /**
     * @param string $msg
     */
    public function toLog(string $msg): void
    {
        if($this->outputLogPath) {
            file_put_contents($this->outputLogPath, $msg . PHP_EOL, FILE_APPEND);
        }
    }

    private function removeOldLogIfNeed(): void
    {
        if($this->outputLogPath && file_exists($this->outputLogPath)) {
            unlink($this->outputLogPath);
        }
    }
}