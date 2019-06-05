<?php declare(strict_types=1);

namespace app\tests;

use app\commands\Common\Output;
use app\commands\GenCharts;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GenChartsTest extends TestCase
{
    /** @var string */
    private $chartPath;

    /** @var GenCharts|MockObject */
    private $genChartsMock;

    protected function setUp(): void
    {
        $this->chartPath = sys_get_temp_dir() . '/' . uniqid() . '_{source}_{year}_{month}_{number}_{category}.png';

        $this->genChartsMock = $this->getMockBuilder(GenCharts::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['removeOldChartsIfNeed', 'generateBarChart', 'getChartPath'])
            ->getMock();

        $configParam = new \ReflectionProperty(GenCharts::class, 'config');
        $configParam->setAccessible(true);
        $configParam->setValue($this->genChartsMock, ['paths' => ['chart' => $this->chartPath]]);

        $outputParam = new \ReflectionProperty(GenCharts::class, 'output');
        $outputParam->setAccessible(true);
        $outputParam->setValue($this->genChartsMock, $this->createMock(Output::class));

        $yearParam = new \ReflectionProperty(GenCharts::class, 'year');
        $yearParam->setAccessible(true);
        $yearParam->setValue($this->genChartsMock, date('Y'));

        $monthParam = new \ReflectionProperty(GenCharts::class, 'month');
        $monthParam->setAccessible(true);
        $monthParam->setValue($this->genChartsMock, date('n'));
    }

    public function testRemoveOldChartsIfNeed(): void
    {
        $testChartPath = strtr($this->chartPath, [
            '{source}' => 'some_source',
            '{year}' => date('Y'),
            '{month}' => date('n'),
            '{number}' => 1,
            '{category}' => 'some_category'
        ]);

        imagepng(imagecreatetruecolor(1, 1), $testChartPath);
        $this->assertFileExists($testChartPath);

        $removeOldChartsIfNeedMethod = new \ReflectionMethod(GenCharts::class, 'removeOldChartsIfNeed');
        $removeOldChartsIfNeedMethod->setAccessible(true);
        $removeOldChartsIfNeedMethod->invokeArgs($this->genChartsMock, ['some_source']);
        $this->assertFileNotExists($testChartPath);
    }

    public function testGenerateBarChart(): void
    {
        $generateBarChartMethod = new \ReflectionMethod(GenCharts::class, 'generateBarChart');
        $generateBarChartMethod->setAccessible(true);
        $generateBarChartMethod->invokeArgs($this->genChartsMock, ['some_source', 1, 'some_category', ['some_tech' => 1]]);

        $testChartPath = strtr($this->chartPath, [
            '{source}' => 'some_source',
            '{year}' => date('Y'),
            '{month}' => date('n'),
            '{number}' => 1,
            '{category}' => \Behat\Transliterator\Transliterator::transliterate('some_category')
        ]);
        $this->assertFileExists($testChartPath);

        unlink($testChartPath);
    }

    public function testGetChartPath(): void
    {
        $testChartPath = strtr($this->chartPath, [
            '{source}' => 'some_source',
            '{year}' => date('Y'),
            '{month}' => date('n'),
            '{number}' => 1,
            '{category}' => 'some_category'
        ]);

        $getChartPathMethod = new \ReflectionMethod(GenCharts::class, 'getChartPath');
        $getChartPathMethod->setAccessible(true);
        $generatedChartPath = $getChartPathMethod->invokeArgs($this->genChartsMock, ['some_source', 1, 'some_category']);

        $this->assertEquals($testChartPath, $generatedChartPath);
    }
}