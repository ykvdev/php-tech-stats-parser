<?php

namespace app\commands\GetStats;

use app\commands\Common\Output;
use GuzzleHttp\Client;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\DomCrawler\Crawler;

class Vacancy
{
    /** @var Output */
    private $output;

    public function __construct(Output $output)
    {
        $this->output = $output;
    }

    public function getUrlsList(string $pagesUrl, int $pageNumber, string $vacancyUrlsSelector) {
        $urls = [];

        $this->output->info('Begin parsing vacancy urls');
        $progress = new ProgressBar($this->output->getOutput());
        $progress->start();
        while(true) {
            $url = strtr($pagesUrl, ['{pageNumber}' => $pageNumber]);
            $response = (new Client(['http_errors' => false]))->request('GET', $url);
            if($response->getStatusCode() == 404) {
                break;
            }

            $html = $response->getBody()->getContents();
            $lastReceivedUrlsNumber = 0;
            (new Crawler($html))
                ->filter($vacancyUrlsSelector)
                ->each(function(Crawler $node, $i) use(&$urls, $pagesUrl, $progress, &$lastReceivedUrlsNumber) {
                    $url = $this->normalizeUrlIfNeed($node->attr('href'), $pagesUrl);
                    if(!in_array($url, $urls)) {
                        $urls[] = $url;
                        $lastReceivedUrlsNumber++;
                        $progress->advance();
                    }
                });
            if(!$lastReceivedUrlsNumber) {
                break;
            }

            $pageNumber++;
        }
        $progress->finish();
        $this->output->eol();
        $this->output->info('Received vacancies number: ' . count($urls));

        return $urls;
    }

    private function normalizeUrlIfNeed(string $url, string $pagesUrl): string
    {
        if (substr($url, 0, 1) == '/') {
            $url = parse_url($pagesUrl, PHP_URL_HOST) . $url;
        }

        return $url;
    }

    public function getTextByUrl($url, string $vacancyTextSelector) {
        $this->output->toLog('Parsing vacancy ' . $url);
        $html = (new Client(['http_errors' => false]))->request('GET', $url)->getBody()->getContents();
        $html = $this->normalizeHtml($html);
        $crawler = (new Crawler($html))->filter($vacancyTextSelector);
        if(!$crawler->count() || !($text = $crawler->text())) {
            $this->output->eol();
            $this->output->error("Get vacancy data failed ({$url})");

            return null;
        } else {
            return $text;
        }
    }

    private function normalizeHtml(string $html): string
    {
        return str_replace('<!DOCTYPE html>', '', $html);
    }
}