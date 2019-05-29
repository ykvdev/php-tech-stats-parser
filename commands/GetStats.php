<?php

namespace app\commands;

use app\commands\GetStats\IgnoredWords;
use app\commands\Common\Output;
use app\commands\GetStats\Vacancy;
use app\commands\GetStats\Stats;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetStats extends Command
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
        $this->setName('get-stats')
            ->setDescription('Get tech stats');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output) {
        $this->config = require APP_ROOT_PATH . '/configs/common.php';
        $this->output = new Output($output, $this->config['paths']['get_stats_output_log']);
        $this->vacancy = new Vacancy;
        $this->stats = new Stats($this->config['tech_patterns'], $this->config['paths']['stats_json']);
        $this->ignoredWords = new IgnoredWords($this->config['paths']['last_ignored_words']);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            foreach ($this->config['sources'] as $sourceAlias => $sourceConfig) {
                $this->output->info('Begin parsing source ' . $sourceAlias);
                $this->output->info('Begin parsing vacancy urls');
                $urls = $this->vacancy->getUrlsList($sourceConfig['pages_url'], $sourceConfig['vacancy_urls_selector']);
                $countVacancies = count($urls);
                $this->output->info("Received vacancies number: {$countVacancies}");

                $this->output->info('Begin parsing vacancy stats');
                $progress = new ProgressBar($this->output->getOutput(), $countVacancies);
                $progress->start();
                foreach ($urls as $url) {
                    $text = $this->vacancy->getTextByUrl(
                        $url,
                        $sourceConfig['vacancy_title_selector'],
                        $sourceConfig['vacancy_text_selector']
                    );
                    $this->stats->parseFromVacancyText($text, $sourceAlias);
                    $this->ignoredWords->parseFromVacancyText($text);

                    $progress->advance();
                }
                $this->stats->sort($sourceAlias);
                $progress->finish();
                $this->output->eol();
            }

            $this->stats->save();
            $this->ignoredWords->save();
            $this->output->info('Save stats and ignored words finished');
        } catch (\Exception $e) {
            $this->output->error(
                PHP_EOL . '(' . get_class($e) . ') '
                . $e->getMessage()
                . PHP_EOL . $e->getTraceAsString()
            );
        }
    }
}