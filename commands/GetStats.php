<?php declare(strict_types=1);

namespace app\commands;

use app\commands\GetStats\IgnoredWords;
use app\commands\Common\Output;
use app\commands\GetStats\Vacancy;
use app\commands\GetStats\Stats;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetStats extends Command
{
    /** @var array */
    private $config;

    /** @var Output */
    private $output;

    /** @var Vacancy */
    private $vacancy;

    /** @var Stats */
    private $stats;

    /** @var IgnoredWords */
    private $ignoredWords;

    public function __construct(array $config)
    {
        $this->config = $config;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('get-stats')
            ->setDescription('Get tech stats');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->output = new Output($output, $this->config['paths']['get_stats_output_log']);
        $this->vacancy = new Vacancy();
        $this->stats = new Stats($this->config['tech_patterns'], $this->config['paths']['stats_json']);
        $this->ignoredWords = new IgnoredWords($this->config['paths']['last_ignored_words']);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            foreach ($this->config['sources'] as $sourceAlias => $sourceConfig) {
                $urls = $this->getVacancyUrlsFromSource($sourceAlias, $sourceConfig);
                $this->parsingVacancyStats($urls, $sourceAlias, $sourceConfig);
                $this->stats->sort($sourceAlias);
            }

            $this->stats->save();
            $this->ignoredWords->save();
            $this->output->info('Save stats and ignored words finished');
        } catch (\Throwable $e) {
            $this->output->error(
                PHP_EOL . '(' . get_class($e) . ') ' . $e->getMessage()
                . PHP_EOL . $e->getTraceAsString()
            );
        }
    }

    private function getVacancyUrlsFromSource(string $sourceAlias, array $sourceConfig): array
    {
        $urls = [];

        $this->output->info('Begin parsing vacancy urls from source ' . $sourceAlias);
        $progress = new ProgressBar($this->output->getOutput());
        $progress->start();
        $pageNumber = $sourceConfig['first_page_number'];
        while(true) {
            if (!($newUrls = $this->vacancy->getUrlsListFromPage(
                strtr($sourceConfig['pages_url'], ['{pageNumber}' => $pageNumber]),
                $sourceConfig['vacancy_urls_selector']
            ))) { break; }

            $urls = array_merge($urls, $newUrls);
            $progress->advance(count($newUrls));
            $pageNumber++;
        }
        $progress->finish();
        $this->output->eol();
        $this->output->info('Received vacancies number: ' . count($urls));

        return $urls;
    }

    private function parsingVacancyStats(array $vacancyUrls, string $sourceAlias, array $sourceConfig): void
    {
        $this->output->info('Begin parsing vacancy stats');
        $progress = new ProgressBar($this->output->getOutput(), count($vacancyUrls));
        $progress->start();
        foreach ($vacancyUrls as $url) {
            $this->output->toLog('Parsing vacancy ' . $url);
            $text = $this->vacancy->getTextByUrl($url, $sourceConfig['vacancy_text_selector']);
            if($text) {
                $this->stats->parseFromVacancyText($text, $sourceAlias);
                $this->ignoredWords->parseFromVacancyText($text);
            } else {
                $this->output->eol();
                $this->output->error("Get vacancy data failed ({$url})");
            }

            $progress->advance();
        }
        $progress->finish();
        $this->output->eol();
    }
}