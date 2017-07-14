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

    const GET_REQUEST_MAX_ATTEMPTS = 5;
    const GET_REQUEST_MAX_SLEEP = 5;
    const GET_REQUEST_USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36';

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
            $this->outputInfo("Received {$countVacancies} vacancy urls");
            $this->output->write(PHP_EOL);

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
            $html = $this->getPage(sprintf(self::PAGES_URL, $pageNumber));
            $links = (new Crawler($html))->filter(self::VACANCY_URLS_SELECTOR);
            if($links->count() == 0) {
                break;
            }

            $links->each(function(Crawler $node, $i) {
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
        $crawler = new Crawler($this->getPage($url));
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
     * @param $url
     * @return string
     * @throws \Exception
     */
    private function getPage($url) {
        for($i = 1; $i <= self::GET_REQUEST_MAX_ATTEMPTS; $i++) {
            sleep(mt_rand(0, self::GET_REQUEST_MAX_SLEEP));

            try {
                $html = (new Client())->request('GET', $url, ['headers' => [
                    'User-Agent' => self::GET_REQUEST_USER_AGENT,
                ]])->getBody()->getContents();
                return $html;
            } catch (\Exception $e) {
                if($i == self::GET_REQUEST_MAX_ATTEMPTS) {
                    throw new $e;
                }
            }
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