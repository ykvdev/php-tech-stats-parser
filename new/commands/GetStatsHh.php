<?php

namespace app\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use app\commands\GetStatsHh\Output;
use app\commands\GetStatsHh\Vacancy;

class GetStatsHh extends Command
{
    /** @var array */
    private $config;

    /** @var Output */
    private $output;

    /** @var Vacancy */
    private $vacancy;

    /** @var array */
    private $stats = [];

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
                $this->parseVacancyStats($text);
                $this->parseVacancyIgnoredWords($text);

                $progress->advance();
            }
            $progress->finish();
            $this->output->eol();

            $this->sortStats();
            $this->saveStats();
            $this->saveIgnoredWords();
            $this->output->info('Save stats and ignored words finished');
        } catch (\Exception $e) {
            $this->output->error('(' . get_class($e) . ') ' . $e->getMessage());
        }
    }

    /**
     * @param string $text
     */
    private function parseVacancyStats(&$text) {
        $vacancyTechs = [];
        foreach($this->config['patterns'] as $category => $techs) {
            foreach($techs as $tech => $pattern) {
                if(!in_array($tech, $vacancyTechs) && preg_match('/' . $pattern . '/Ui', $text)) {
                    $this->stats[$category][$tech] = isset($this->stats[$category][$tech])
                        ? $this->stats[$category][$tech] + 1 : 1;
                    $vacancyTechs[] = $tech;
                    $text = preg_replace('/' . $pattern . '/Ui', '', $text);
                }
            }
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

    private function sortStats() {
        $sortedStats = [];
        foreach (array_keys($this->config['patterns']) as $category) if (isset($this->stats[$category])) {
            $sortedStats[$category] = $this->stats[$category];
            arsort($sortedStats[$category]);
        }

        $this->stats = $sortedStats;
    }

    /**
     * @throws \Exception
     */
    private function saveStats() {
        $statsFilePath = strtr($this->config['paths']['stats_json'], ['{year}' => date('Y')]);
        $statsFromFile = [];
        if(file_exists($statsFilePath)) {
            $statsFromFile = json_decode(file_get_contents($statsFilePath), true);
            $statsFromFile = !is_array($statsFromFile) ? [] : $statsFromFile;
        }

        $statsToFile = $statsFromFile;
        $statsToFile[date('n')] = $this->stats;
        ksort($statsToFile);

        if(file_put_contents($statsFilePath, json_encode($statsToFile)) === false) {
            throw new \Exception('Save stats failed');
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