<?php

namespace app\commands;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
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

    protected function initialize(InputInterface $input, OutputInterface $output) {
        $this->input = $input;
        $this->output = $output;
        $this->config = require APP_ROOT_PATH . '/config.php';
    }
    
    protected function execute() {
        $pageNumber = 0;
        while(true) {
            $vacancyUrls = $this->getVacancyUrls($pageNumber);
            if(!$vacancyUrls) {
                $this->output->writeln('The end');
                break;
            }

            foreach ($vacancyUrls as $url) {
                $this->parseVacancy($url);
            }
            $this->saveStats();
            $this->saveIgnoredWords();

            $pageNumber++;
        }
    }

    private function getVacancyUrls($pageNumber) {
        $this->output->writeln("Get stats for page {$pageNumber}");
        $url = sprintf(self::PAGES_URL, $pageNumber);
        $html = (new Client())->request('GET', $url)->getBody();

        $urls = [];
        (new Crawler($html))->filter(self::VACANCY_URLS_SELECTOR)->each(function(Crawler $node, $i) {
            $urls[] = $node->attr('href');
        });

        return $urls;
    }

    private function parseVacancy($url) {
        $html = (new Client())->request('GET', $url)->getBody();
        sleep(mt_rand(0, 5));

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

        foreach(explode(' ', $text) as $word) {
            if(!in_array($word, $this->ignoredWords) && preg_match('/[a-z]{2,}/i', $word)) {
                $this->ignoredWords[] = trim($word);
            }
        }
    }

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

    private function saveIgnoredWords() {
        if(file_put_contents(
            APP_ROOT_PATH . '/results/last-ignored-words.txt',
            implode(PHP_EOL, $this->ignoredWords)
        ) === false) {
            throw new \Exception('Save ignored words failed');
        }
    }
}