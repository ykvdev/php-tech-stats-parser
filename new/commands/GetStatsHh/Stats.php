<?php

namespace app\commands\GetStatsHh;

class Stats
{
    /** @var array */
    private $patterns;

    /** @var string */
    private $statsJsonPath;

    /** @var array */
    private $stats;

    /**
     * @param array $patterns
     * @param string $statsJsonPath
     */
    public function __construct(array $patterns, $statsJsonPath) {
        $this->patterns = $patterns;
        $this->statsJsonPath = $statsJsonPath;
    }

    /**
     * @param string $text
     */
    public function parseByVacancyText(&$text) {
        $vacancyTechs = [];
        foreach($this->patterns as $category => $techs) {
            foreach($techs as $tech => $pattern) {
                if(!in_array($tech, $vacancyTechs) && preg_match('/' . $pattern . '/Ui', $text)) {
                    $this->stats[$category][$tech] = isset($this->stats[$category][$tech])
                        ? $this->stats[$category][$tech] + 1 : 1;
                    $vacancyTechs[] = $tech;
                    $text = preg_replace('/' . $pattern . '/Ui', '', $text);
                }
            }
        }
    }

    public function sort() {
        $sortedStats = [];
        foreach (array_keys($this->patterns) as $category) if (isset($this->stats[$category])) {
            $sortedStats[$category] = $this->stats[$category];
            arsort($sortedStats[$category]);
        }

        $this->stats = $sortedStats;
    }

    /**
     * @throws \Exception
     */
    public function save() {
        $statsFilePath = strtr($this->statsJsonPath, ['{year}' => date('Y')]);
        $statsFromFile = [];
        if(file_exists($statsFilePath)) {
            $statsFromFile = json_decode(file_get_contents($statsFilePath), true);
            $statsFromFile = !is_array($statsFromFile) ? [] : $statsFromFile;
        }

        $statsToFile = $statsFromFile;
        $statsToFile[date('n')] = $this->stats;
        ksort($statsToFile);

        if(file_put_contents($statsFilePath, json_encode($statsToFile)) === false) {
            throw new \Exception('Save stats failed');
        }
    }
}