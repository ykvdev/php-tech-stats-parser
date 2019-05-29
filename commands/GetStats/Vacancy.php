<?php

namespace app\commands\GetStats;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class Vacancy
{
    public function getUrlsList(string $pagesUrl, string $vacancyUrlsSelector) {
        $urls = [];

        $pageNumber = 0;
        while(true) {
            $url = sprintf($pagesUrl, $pageNumber);
            $response = (new Client(['http_errors' => false]))->request('GET', $url);
            if($response->getStatusCode() == 404) {
                break;
            }

            $html = $response->getBody()->getContents();
            (new Crawler($html))
                ->filter($vacancyUrlsSelector)
                ->each(function(Crawler $node, $i) use(&$urls) {
                    $urls[] = $node->attr('href');
                });

            $pageNumber++;
        }

        return $urls;
    }

    public function getTextByUrl($url, string $vacancyTitleSelector, string $vacancyTextSelector) {
        $html = (new Client())->request('GET', $url)->getBody()->getContents();
        $crawler = new Crawler($html);
        $title = $crawler->filter($vacancyTitleSelector)->text();
        $text = $crawler->filter($vacancyTextSelector)->text();
        if(!$title || !$text) {
            throw new \Exception("Get vacancy data failed ({$url})");
        }
        $text .= $title;

        return $text;
    }
}