<?php

namespace app\commands\GetStats;

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
    public function parseFromVacancyText(&$text, $sourceAlias) {
        $vacancyTechs = [];
        foreach($this->patterns as $category => $techs) {
            foreach($techs as $tech => $pattern) {
                if(!in_array($tech, $vacancyTechs) && preg_match('/' . $pattern . '/Ui', $text)) {
                    $this->stats[$sourceAlias][date('n')][$category][$tech]
                        = isset($this->stats[$sourceAlias][date('n')][$category][$tech])
                        ? $this->stats[$sourceAlias][date('n')][$category][$tech] + 1 : 1;
                    $vacancyTechs[] = $tech;
                    $text = preg_replace('/' . $pattern . '/Ui', '', $text);
                }
            }
        }
    }

    public function sort(string $sourceAlias) {
        $sortedStats = [];
        foreach (array_keys($this->patterns) as $category) {
            if (isset($this->stats[$sourceAlias][date('n')][$category])) {
                $sortedStats[$category] = $this->stats[$sourceAlias][date('n')][$category];
                arsort($sortedStats[$category]);
            }
        }

        $this->stats[$sourceAlias][date('n')] = $sortedStats;
    }

    /**
     * @throws \Exception
     */
    public function save() {
        $statsFilePath = strtr($this->statsJsonPath, ['{year}' => date('Y')]);
        $statsData = file_exists($statsFilePath) ? json_decode(file_get_contents($statsFilePath), true) : [];

        foreach ($this->stats as $sourceAlias => $monthStats) {
            $statsData[$sourceAlias] = isset($statsData[$sourceAlias]) ? $statsData[$sourceAlias] : [];
            $statsData[$sourceAlias] = array_replace($statsData[$sourceAlias], $monthStats);
            ksort($statsData[$sourceAlias]);
        }

        if(file_put_contents($statsFilePath, json_encode($statsData)) === false) {
            throw new \Exception('Save stats failed');
        }
    }
}