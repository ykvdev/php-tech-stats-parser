<?php

namespace app\commands;

use app\commands\GetStatsHh\IgnoredWords;
use app\commands\GetStatsHh\Output;
use app\commands\GetStatsHh\Vacancy;
use app\commands\GetStatsHh\Stats;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetStatsHh extends Command
{
    /** @var array */
    private $config;

    /** @var Output */
    private $output;

    /** @var Vacancy */
    private $vacancy;

    /** @var Stats */
    private $stats;

    /** @var IgnoredWords */
    private $ignoredWords;

    protected function configure() {
        $this->setName('get-stats-hh')
            ->setDescription('Get tech stats from hh.ru');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output) {
        $this->config = require __DIR__ . '/GetStatsHh/config.php';
        $this->output = new Output($output, $this->config['paths']['output_log']);
        $this->vacancy = new Vacancy;
        $this->stats = new Stats($this->config['patterns'], $this->config['paths']['stats_json']);
        $this->ignoredWords = new IgnoredWords($this->config['paths']['last_ignored_words']);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            $this->output->info('Begin parse vacancy urls');
            $urls = $this->vacancy->getUrlsList();
            $countVacancies = count($urls);
            $this->output->info("Received vacancies number: {$countVacancies}");

            $this->output->info('Begin parse vacancy stats');
            $progress = new ProgressBar($this->output->getOutput(), $countVacancies);
            $progress->start();
            foreach ($urls as $url) {
                $text = $this->vacancy->getTextByUrl($url);
                $this->stats->parseFromVacancyText($text);
                $this->ignoredWords->parseFromVacancyText($text);

                $progress->advance();
            }
            $progress->finish();
            $this->output->eol();

            $this->stats->sort();
            $this->stats->save();
            $this->ignoredWords->save();
            $this->output->info('Save stats and ignored words finished');
        } catch (\Exception $e) {
            $this->output->error('(' . get_class($e) . ') ' . $e->getMessage());
        }
    }
}