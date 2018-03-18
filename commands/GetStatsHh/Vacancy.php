<?php

namespace app\commands\GetStatsHh;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class Vacancy
{
    const PAGES_URL = 'http://hh.ru/search/vacancy?items_on_page=100&enable_snippets=true&text=PHP&no_magic=true&clusters=true&search_period=30&currency_code=USD&page=%d';

    const VACANCY_URLS_SELECTOR = 'div.vacancy-serp-item__info div.vacancy-serp-item__title a';
    const VACANCY_TITLE_SELECTOR = 'h1';
    const VACANCY_TEXT_SELECTOR = 'div.b-vacancy-desc-wrapper, div.l-paddings.b-vacancy-desc.g-user-content, .vacancy-description';

    /**
     * @return array
     */
    public function getUrlsList() {
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
     * @return string
     * @throws \Exception
     */
    public function getTextByUrl($url) {
        $html = (new Client())->request('GET', $url)->getBody()->getContents();
        $crawler = new Crawler($html);
        $title = $crawler->filter(self::VACANCY_TITLE_SELECTOR)->text();
        $text = $crawler->filter(self::VACANCY_TEXT_SELECTOR)->text();
        if(!$title || !$text) {
            throw new \Exception("Get vacancy data failed ({$url})");
        }
        $text .= $title;

        return $text;
    }
}