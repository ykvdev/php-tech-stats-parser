<?php

namespace app\commands;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class GetStatsHh extends Command
{
    const PAGES_URL = 'http://hh.ru/search/vacancy?items_on_page=100&enable_snippets=true&text=PHP&no_magic=true&clusters=true&search_period=30&currency_code=USD&page=%d';

    const VACANCY_URLS_SELECTOR = 'div.search-result-item__head a';
    const VACANCY_TITLE_SELECTOR = 'h1.b-vacancy-title';
    const VACANCY_TEXT_SELECTOR = 'div.b-vacancy-desc-wrapper';

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var array */
    private $config;

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
        $this->input = $input;
        $this->output = $output;
        $this->config = require APP_ROOT_PATH . '/config.php';
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            $this->outputInfo('Begin parse vacancy urls');
            $urls = $this->getVacancyUrls();
            $countVacancies = count($urls);
            $this->outputInfo("Received vacancies number: {$countVacancies}");

            $this->outputInfo('Begin parse vacancy stats');
            $progress = new ProgressBar($this->output, $countVacancies);
            $progress->start();
            foreach ($urls as $url) {
                $this->parseVacancy($url);
                $progress->advance();
            }
            $progress->finish();
            $this->output->write(PHP_EOL);

            $this->saveStats();
            $this->saveIgnoredWords();
            $this->outputInfo('Save stats and ignored words finished');
        } catch (\Exception $e) {
            $this->outputError('(' . get_class($e) . ') ' . $e->getMessage());
        }
    }

    /**
     * @return array
     */
    private function getVacancyUrls() {
        $urls = [];

        $pageNumber = 0;
        while(true) {
            $url = sprintf(self::PAGES_URL, $pageNumber);
            $response = (new Client(['http_errors' => false]))->request('GET', $url);
            if($response->getStatusCode() == 404) {
                break;
            }

            $html = $response->getBody()->getContents();
            (new Crawler($html))
            ->filter(self::VACANCY_URLS_SELECTOR)
            ->each(function(Crawler $node, $i) use(&$urls) {
                $urls[] = $node->attr('href');
            });

            $pageNumber++;
        }

        return $urls;
    }

    /**
     * @param string $url
     * @throws \Exception
     */
    private function parseVacancy($url) {
        $html = (new Client())->request('GET', $url)->getBody()->getContents();
        $crawler = new Crawler($html);
        $title = $crawler->filter(self::VACANCY_TITLE_SELECTOR)->text();
        $text = $crawler->filter(self::VACANCY_TEXT_SELECTOR)->text();
        if(!$title || !$text) {
            throw new \Exception("Get vacancy data failed ({$url})");
        }
        $text .= $title;

        $vacancyTechs = [];
        foreach($this->config as $category => $techs) {
            foreach($techs as $tech => $pattern) {
                if(!in_array($tech, $vacancyTechs) && preg_match('/' . $pattern . '/Ui', $text)) {
                    $this->stats[$category][$tech] = isset($this->stats[$category][$tech])
                        ? $this->stats[$category][$tech] + 1 : 1;
                    $vacancyTechs[] = $tech;
                    $text = preg_replace('/' . $pattern . '/Ui', '', $text);
                }
            }
        }

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
    private function saveStats() {
        foreach($this->stats as &$techs) {
            if(is_array($techs)) {
                arsort($techs);
            }
        }

        if(file_put_contents(
            APP_ROOT_PATH . '/results/stats.json',
            json_encode($this->stats)
        ) === false) {
            throw new \Exception('Save stats failed');
        }
    }

    /**
     * @throws \Exception
     */
    private function saveIgnoredWords() {
        asort($this->ignoredWords);

        if(file_put_contents(
            APP_ROOT_PATH . '/results/last-ignored-words.txt',
            implode(PHP_EOL, $this->ignoredWords)
        ) === false) {
            throw new \Exception('Save ignored words failed');
        }
    }

    /**
     * @param string $msg
     */
    private function outputInfo($msg) {
        $this->output->writeln(date('Y-m-d H:i:s') . ' [INFO] ' . $msg);
    }

    /**
     * @param string $msg
     */
    private function outputError($msg) {
        $this->output->writeln('<error>' . date('Y-m-d H:i:s') . ' [ERROR] ' . $msg . '</error>');
    }
}