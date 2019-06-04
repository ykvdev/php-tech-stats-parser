<?php

namespace app\tests\Common;

use app\commands\Common\Output;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OutputTest extends TestCase
{
    /** @var string */
    private $logPath;

    /** @var Output|MockObject $outputMock */
    private $outputMock;

    protected function setUp(): void
    {
        $this->logPath = sys_get_temp_dir() . '/' . uniqid() . '_output.log';

        $this->outputMock = $this->getMockBuilder(Output::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['info', 'error', 'eol', 'toLog', 'removeOldLogIfNeed'])
            ->getMock();

        $outputLogPathProp = new \ReflectionProperty(Output::class, 'outputLogPath');
        $outputLogPathProp->setAccessible(true);
        $outputLogPathProp->setValue($this->outputMock, $this->logPath);

        $outputProp = new \ReflectionProperty(Output::class, 'output');
        $outputProp->setAccessible(true);
        $outputProp->setValue($this->outputMock, new class {
            public function writeln(string $msg): void
            {
                echo $msg;
            }

            public function write(string $msg): void
            {
                echo $msg;
            }
        });
    }

    public function testInfo(): void
    {
        $msg = 'Some message';
        $this->expectOutputRegex("/{$msg}/");
        $this->outputMock->info($msg);
    }

    public function testError(): void
    {
        $msg = 'Some message';
        $this->expectOutputRegex("/{$msg}/");
        $this->outputMock->error($msg);
    }

    public function testEol(): void
    {
        $this->expectOutputString(PHP_EOL);
        $this->outputMock->eol();
    }

    public function testToLog(): void
    {
        $this->outputMock->toLog('Some message');

        $this->assertFileExists($this->logPath);
        $this->assertStringEqualsFile($this->logPath, 'Some message' . PHP_EOL);
    }

    public function testRemoveOldLogIfNeed(): void
    {
        file_put_contents($this->logPath, 'Some message');

        $removeOldLogIfNeedMethod = new \ReflectionMethod(Output::class, 'removeOldLogIfNeed');
        $removeOldLogIfNeedMethod->setAccessible(true);
        $removeOldLogIfNeedMethod->invoke($this->outputMock);

        $this->assertFileNotExists($this->logPath);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->logPath)) {
            unlink($this->logPath);
        }
    }
}
