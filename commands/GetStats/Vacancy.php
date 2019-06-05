<?php declare(strict_types=1);

namespace app\commands\GetStats;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class Vacancy
{
    /** @var Client */
    private $guzzleHttpClient;

    public function __construct()
    {
        $this->guzzleHttpClient = new Client(['http_errors' => false]);
    }

    public function getUrlsListFromPage(string $pageUrl, string $vacancyUrlsSelector): ?array
    {
        if(!($html = $this->httpRequest($pageUrl))) {
            return null;
        }

        $vacancyUrls = [];
        (new Crawler($html))
            ->filter($vacancyUrlsSelector)
            ->each(function(Crawler $node, $i) use(&$vacancyUrls, $pageUrl) {
                $vacancyUrl = $node->attr('href');
                if (substr($vacancyUrl, 0, 1) == '/') {
                    $vacancyUrl = parse_url($pageUrl, PHP_URL_HOST) . $vacancyUrl;
                }

                if(!in_array($vacancyUrl, $vacancyUrls)) {
                    $vacancyUrls[] = $vacancyUrl;
                }
            });

        return $vacancyUrls;
    }

    public function getTextByUrl(string $url, string $vacancyTextSelector): ?string
    {
        if(!($html = $this->httpRequest($url))) {
            return null;
        }

        $crawler = (new Crawler($html))->filter($vacancyTextSelector);
        if($crawler->count() && ($text = $crawler->text())) {
            return $text;
        } else {
            return null;
        }
    }

    private function httpRequest(string $url): ?string
    {
        $response = $this->guzzleHttpClient->request('GET', $url);
        if($response->getStatusCode() == 404) {
            return null;
        }

        $html = $response->getBody()->getContents();
        $html = str_replace('<!DOCTYPE html>', '', $html);

        return $html;
    }
}