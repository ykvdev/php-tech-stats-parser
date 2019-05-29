<?php

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

    protected function configure() {
        $this->config = require APP_ROOT_PATH . '/configs/common.php';

        $this->setName('gen-charts')
            ->setDescription('Generate charts by statistics file');

        $this->addOption('year', 'y', InputOption::VALUE_OPTIONAL,
            'Specific year for charts generation');

        $this->addOption('month', 'm', InputOption::VALUE_OPTIONAL,
            'Specific month for charts generation');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output) {
        $this->output = new Output($output);
        $this->input = $input;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            list($year, $month) = $this->getYearAndMonth();
            if(!$year) {
                throw new \Exception('No stats files found');
            }

            $this->removeOldChartsIfNeed();

            $stats = json_decode(file_get_contents(strtr($this->config['paths']['stats_json'], ['{year}' => $year])), true);

            $this->output->info("Begin generate charts by {$year} year and {$month} month");
            $chartNumber = 1;
            foreach ($stats[$month] as $category => $techsStats) {
                $this->output->info("Generate chart for category \"{$category}\"");
                $this->generateBarChart($chartNumber++, $category, $techsStats);
            }

            $this->output->info('Generate common chart');
            $commonStats = array_merge(...array_values($stats[$month]));
            $hitsSum = array_sum($commonStats);
            $averageHit = $hitsSum / count($commonStats);
            foreach ($commonStats as $techName => $techHits) {
                if($techHits < $averageHit) {
                    unset($commonStats[$techName]);
                }
            }
            arsort($commonStats);
            $this->generateBarChart($chartNumber, 'Общее', $commonStats);
        } catch (\Exception $e) {
            $this->output->error('(' . get_class($e) . ') ' . $e->getMessage());
        }
    }

    /**
     * @return array
     */
    private function getYearAndMonth() {
        $year = $this->input->getOption('year');
        $month = $this->input->getOption('month');
        if(!$year || !$month) {
            list($lastStatsYear, $lastStatsMonth) = $this->getLastStatsYearAndMonth();
            $year = $year ?: $lastStatsYear;
            $month = $month ?: $lastStatsMonth;
        }

        return [$year, $month];
    }

    /**
     * @return array|null
     */
    private function getLastStatsYearAndMonth() {
        $files = glob(strtr($this->config['paths']['stats_json'], ['{year}' => '*']));
        if(!$files) {
            return null;
        }

        asort($files);
        $lastStatsFilePath = end($files);
        $lastYear = str_replace(explode('{year}', $this->config['paths']['stats_json']),
            '', $lastStatsFilePath);

        $stats = json_decode(file_get_contents($lastStatsFilePath), true);
        end($stats);
        $lastMonth = key($stats);

        return [$lastYear, $lastMonth];
    }

    /**
     * @throws \Exception
     */
    private function removeOldChartsIfNeed() {
        foreach(glob(strtr($this->config['paths']['chart'], ['{number}' => '*', '{category}' => '*'])) as $file) {
            if(!unlink($file)) {
                throw new \Exception("Remove old chart failed: {$file}");
            }
        }
    }

    private function generateBarChart($number, $category, $techsStats, $width = 600, $height = null) {
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
        $image->autoOutput(strtr($this->config['paths']['chart'], [
            '{number}' => $number,
            '{category}' => \Behat\Transliterator\Transliterator::transliterate($category)
        ]));
    }
}