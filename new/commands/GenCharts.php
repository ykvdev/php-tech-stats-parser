<?php

namespace app\commands;

use app\commands\Common\Output;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenCharts extends Command
{
    /** @var array */
    private $config;

    /** @var Output */
    private $output;

    /** @var Input */
    private $input;

    protected function configure() {
        $this->config = require APP_ROOT_PATH . '/config.php';

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

            $this->output->info("Begin generate charts by {$year} year and {$month} month");
            $stats = json_decode(strtr($this->config['paths']['stats_json'], ['{year}' => $year]), true);
            $chartNumber = 1;
            foreach ($stats[$month] as $category => $techsStats) {
                $this->output->info("Generate bar chart for category \"{$category}\"");
                $this->generateBarChart($chartNumber++, $category, $techsStats);
            }
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
        $lastMonth = key(end($stats));

        return [$lastYear, $lastMonth];
    }

    private function generateBarChart($number, $category, $techsStats) {
        arsort($techsStats);

        /* Create and populate the pData object */
        $MyData = new pData();
        $MyData->setAxisName(0,"Кол-во упоминаний");
        $MyData->addPoints(array_keys($techsStats),"abscissa");
        $MyData->setAbscissa("abscissa");
        $MyData->setAxisDisplay(0,AXIS_FORMAT_METRIC,1);

        $MyData->addPoints(array_values($techsStats));

        /* Create the pChart object */
        $myPicture = new pImage(600,500,$MyData);
//    $myPicture->drawGradientArea(0,0,600,500,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
//    $myPicture->drawGradientArea(0,0,600,500,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));
        $myPicture->setFontProperties(array("FontName"=>__DIR__ . '/GenCharts/Candara.ttf',"FontSize"=>10));

        $myPicture->drawText(20,30,$category,array("FontSize"=>13,"Align"=>TEXT_ALIGN_BOTTOMLEFT));

        /* Draw the chart scale */
        $myPicture->setGraphArea(150,50,580,480);
        $myPicture->drawScale(array("CycleBackground"=>TRUE,"DrawSubTicks"=>false,"GridR"=>0,"GridG"=>0,"GridB"=>0,"GridAlpha"=>10,"Pos"=>SCALE_POS_TOPBOTTOM,"Mode"=>SCALE_MODE_START0));

        /* Turn on shadow computing */
        $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

        /* Draw the chart */
        $myPicture->drawBarChart();

        /* Render the picture (choose the best way) */
        $myPicture->autoOutput(strtr($this->config['paths']['chart'], [
            '{number}' => $number,
            '{category}' => \Behat\Transliterator\Transliterator::transliterate($category)
        ]));
    }
}