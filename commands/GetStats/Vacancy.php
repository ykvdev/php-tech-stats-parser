<?php declare(strict_types=1);

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

    public function getUrlsList(string $pagesUrl, int $pageNumber, string $vacancyUrlsSelector): array
    {
        $urls = [];

        $this->output->info('Begin parsing vacancy urls');
        $progress = new ProgressBar($this->output->getOutput());
        $progress->start();
        while(true) {
            $url = strtr($pagesUrl, ['{pageNumber}' => $pageNumber]);
            $html = $this->httpRequest($url);
            if(!$html) {
                break;
            }

            $lastReceivedUrlsNumber = 0;
            (new Crawler($html))
                ->filter($vacancyUrlsSelector)
                ->each(function(Crawler $node, $i) use(&$urls, $pagesUrl, $progress, &$lastReceivedUrlsNumber) {
                    $url = $node->attr('href');
                    if (substr($url, 0, 1) == '/') {
                        $url = parse_url($pagesUrl, PHP_URL_HOST) . $url;
                    }

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

    public function getTextByUrl(string $url, string $vacancyTextSelector): ?string
    {
        $this->output->toLog('Parsing vacancy ' . $url);
        $html = $this->httpRequest($url);
        $crawler = (new Crawler($html))->filter($vacancyTextSelector);
        if(!$crawler->count() || !($text = $crawler->text())) {
            $this->output->eol();
            $this->output->error("Get vacancy data failed ({$url})");

            return null;
        } else {
            return $text;
        }
    }

    private function httpRequest(string $url): ?string
    {
        $response = (new Client(['http_errors' => false]))->request('GET', $url);
        if($response->getStatusCode() == 404) {
            return null;
        }

        $html = $response->getBody()->getContents();
        $html = str_replace('<!DOCTYPE html>', '', $html);

        return $html;
    }
}