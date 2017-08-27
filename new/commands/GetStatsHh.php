<?php

namespace app\commands;

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

    /** @var array */
    private $ignoredWords = [];

    protected function configure() {
        $this->setName('get-stats-hh')
            ->setDescription('Get tech stats from hh.ru');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output) {
        $this->config = require __DIR__ . '/' . end(explode('\\', __CLASS__)) . '/config.php';
        $this->output = new Output($output, $this->config['paths']['output_log']);
        $this->vacancy = new Vacancy;
        $this->vacancy = new Stats($this->config['patterns'], $this->config['paths']['stats_json']);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            $this->output->info('Begin parse vacancy urls');
            $urls = $this->vacancy->getUrlsList();
            $countVacancies = count($urls);
            $this->output->info("Received vacancies number: {$countVacancies}");

            $this->output->info('Begin parse vacancy stats');
            $progress = new ProgressBar($this->output, $countVacancies);
            $progress->start();
            foreach ($urls as $url) {
                $text = $this->vacancy->getTextByUrl($url);
                $this->stats->parseByVacancyText($text);
                $this->parseVacancyIgnoredWords($text);

                $progress->advance();
            }
            $progress->finish();
            $this->output->eol();

            $this->stats->sort();
            $this->stats->save();
            $this->saveIgnoredWords();
            $this->output->info('Save stats and ignored words finished');
        } catch (\Exception $e) {
            $this->output->error('(' . get_class($e) . ') ' . $e->getMessage());
        }
    }

    /**
     * @param string $text
     */
    private function parseVacancyIgnoredWords(&$text) {
        $text = preg_replace('/[^\da-z\-\s\/\\\\\|]/i', '', $text);
        foreach(preg_split('/(\s|\/|\\\\|\|)/', $text) as $word) {
            $word = trim($word);
            if(!in_array($word, $this->ignoredWords) && preg_match('/[a-z]{2,}/i', $word)) {
                $this->ignoredWords[] = $word;
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function saveIgnoredWords() {
        asort($this->ignoredWords);

        if(file_put_contents(
            $this->config['paths']['last_ignored_words'],
            implode(PHP_EOL, $this->ignoredWords)
        ) === false) {
            throw new \Exception('Save ignored words failed');
        }
    }
}