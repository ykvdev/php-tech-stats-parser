<?php declare(strict_types=1);

namespace app\commands;

use app\commands\Common\Output;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use CpChart\Data;
use CpChart\Image;

class GenCharts extends Command
{
    /** @var array */
    private $config;

    /** @var Output */
    private $output;

    /** @var Input */
    private $input;

    /** @var int */
    private $year;

    /** @var int */
    private $month;

    public function __construct(array $config)
    {
        $this->config = $config;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('gen-charts')
            ->setDescription('Generate charts by statistics file');

        $this->addOption('year', 'y', InputOption::VALUE_OPTIONAL,
            'Specific year for charts generation', date('Y'));

        $this->addOption('month', 'm', InputOption::VALUE_OPTIONAL,
            'Specific month for charts generation', date('n'));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->output = new Output($output);
        $this->input = $input;

        $this->year = $this->input->getOption('year');
        $this->month = $this->input->getOption('month');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $statsFilePath = strtr($this->config['paths']['stats_json'], ['{year}' => $this->year]);
            if (!file_exists($statsFilePath)) {
                throw new \RuntimeException("Stats file for {$this->year} year not found");
            }
            $stats = json_decode(file_get_contents($statsFilePath), true);

            $this->output->info("Begin generate charts by {$this->year} year and {$this->month} month");
            foreach ($stats as $sourceAlias => $sourceStats) {
                if (!isset($sourceStats[$this->month])) {
                    $this->output->error("Stats for {$sourceAlias} and {$this->month} month does not exists");
                    continue;
                }

                $this->genCatsCharts($sourceAlias, $sourceStats);
                $this->genCommonChart($sourceAlias, $sourceStats);
            }
        } catch (\Throwable $e) {
            $this->output->error(
                PHP_EOL . '(' . get_class($e) . ') ' . $e->getMessage()
                . PHP_EOL . $e->getTraceAsString()
            );
        }
    }

    private function genCatsCharts(string $sourceAlias, array $sourceStats): void
    {
        $chartNumber = 1;
        $this->removeOldChartsIfNeed($sourceAlias);
        foreach ($sourceStats[$this->month] as $category => $techsStats) {
            $this->output->info("Generate chart for {$sourceAlias} - {$category}");
            $this->generateBarChart($sourceAlias, $chartNumber++, $category, $techsStats);
        }
    }

    private function genCommonChart(string $sourceAlias, array $sourceStats): void
    {
        $this->output->info("Generate common chart for {$sourceAlias}");
        $commonStats = array_merge(...array_values($sourceStats[$this->month]));
        $hitsSum = array_sum($commonStats);
        $averageHit = $hitsSum / count($commonStats);
        foreach ($commonStats as $techName => $techHits) {
            if ($techHits < $averageHit) {
                unset($commonStats[$techName]);
            }
        }
        arsort($commonStats);
        $this->generateBarChart($sourceAlias, 0, 'Общее', $commonStats);
    }

    /**
     * @param string $sourceAlias
     *
     * @throws \RuntimeException
     */
    private function removeOldChartsIfNeed(string $sourceAlias): void
    {
        $files = glob($this->getChartPath($sourceAlias, '*', '*'));
        foreach($files as $file) {
            if(!unlink($file)) {
                throw new \RuntimeException("Remove old chart failed: {$file}");
            }
        }
    }

    private function generateBarChart(string $sourceAlias, int $chartNumber, string $category, array $techsStats,
    int $width = 600, ?int $height = null): void
    {
        $height = $height ?? (count($techsStats) * 40) + 50;
        $height = $height < 100 ? 100 : $height;

        $data = new Data();
        $data->addPoints(array_values($techsStats), 'hits');
        $data->setPalette('hits', ['R' => 224, 'G' => 100, 'B' => 46]);
        $data->setAxisName(0,"Кол-во упоминаний");
        $data->addPoints(array_keys($techsStats),"techs");
        $data->setAbscissa("techs");
        $data->setAxisDisplay(0,AXIS_FORMAT_METRIC,1);

        $image = new Image($width, $height, $data);
//    $image->drawGradientArea(0,0,$width,$height,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
//    $image->drawGradientArea(0,0,$width,$height,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));
        $image->setFontProperties(array("FontName"=>__DIR__ . '/GenCharts/Candara.ttf',"FontSize"=>10));
        $image->drawText(20,30,$category,array("FontSize"=>13,"Align"=>TEXT_ALIGN_BOTTOMLEFT));
        $image->setGraphArea(150,50,$width - 20,$height - 20);
        $image->drawScale(["CycleBackground"=>TRUE,"DrawSubTicks"=>false,"GridR"=>0,"GridG"=>0,"GridB"=>0,"GridAlpha"=>10,"Pos"=>SCALE_POS_TOPBOTTOM,"Mode"=>SCALE_MODE_START0]);
        $image->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
        $image->drawBarChart();

        $chartPath = $this->getChartPath($sourceAlias, $chartNumber,
            \Behat\Transliterator\Transliterator::transliterate($category));
        $chartDirPath = pathinfo($chartPath, PATHINFO_DIRNAME);
        if (!is_dir($chartDirPath)) {
            mkdir($chartDirPath, 0777, true);
        }

        $image->autoOutput($chartPath);
    }

    private function getChartPath(string $sourceAlias, $chartNumber, string $category): string
    {
        $chartPath = strtr($this->config['paths']['chart'], [
            '{source}' => $sourceAlias,
            '{year}' => $this->year,
            '{month}' => $this->month,
            '{number}' => $chartNumber,
            '{category}' => $category,
        ]);

        return $chartPath;
    }
}