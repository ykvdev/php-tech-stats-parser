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

    protected function configure() {
        $this->setName('get-stats-hh')
            ->setDescription('Get tech stats from hh.ru');
    }

    protected function initialize(InputInterface $input, OutputInterface $output) {
        $this->input = $input;
        $this->output = $output;
    }
    
    protected function execute() {
        $pageNumber = 0;
        $stats = [];
        $ignoredWords = [];
        while(true) {
            $vacancyUrls = $this->getVacancyUrls($pageNumber);
            if(!$vacancyUrls) {
                break;
            }

            $pageNumber++;
        }
    }

    private function getVacancyUrls($pageNumber) {
        $this->output->writeln("Get stats for page {$pageNumber}");
        $url = sprintf(self::PAGES_URL, $pageNumber);
        $html = (new Client())->request('GET', $url)->getBody();

        $urls = [];
        (new Crawler($html))->filter(self::VACANCY_URLS_SELECTOR)->each(function(Crawler $node, $i) {
            $links[] = $node->attr('href');
        });

        return $urls;
    }

    private function getVacancyStats($url) {
        $html = (new Client())->request('GET', $url)->getBody();

        $crawler = new Crawler($html);
        $title = $crawler->filter(self::VACANCY_TITLE_SELECTOR)->text();
        $text = $crawler->filter(self::VACANCY_TEXT_SELECTOR)->text();
        if(!$title || !$text) {
            throw new \Exception("Get vacancy data failed ({$url})");
        }

//        $vacancyTechs = [];
//        foreach($config as $category => $techs) {
//            foreach($techs as $tech => $patterns) {
//                foreach($patterns as $pattern) {
//                    if(!in_array($tech, $vacancyTechs) && preg_match('/' . $pattern . '/Ui', $text)) {
//                        $stats[$category][$tech] = isset($stats[$category][$tech])
//                            ? $stats[$category][$tech] + 1 : 1;
//                        $vacancyTechs[] = $tech;
//                        $text = preg_replace('/' . $pattern . '/Ui', '', $text);
//                        break;
//                    }
//                }
//            }
//        }
    }
}