<?php

namespace app\commands\GetStatsHh;

class IgnoredWords
{
    /** @var string */
    private $lastIgnoredWordsPath;

    /** @var array */
    private $ignoredWords = [];

    /**
     * @param string $lastIgnoredWordsPath
     */
    public function __construct($lastIgnoredWordsPath) {
        $this->lastIgnoredWordsPath = $lastIgnoredWordsPath;
    }

    /**
     * @param string $text
     */
    public function parseFromVacancyText(&$text) {
        $text = preg_replace('/[^\da-z\-\s\/\\\\\|]/i', '', $text);
        foreach(preg_split('/(\s|\/|\\\\|\|)/', $text) as $word) {
            $word = trim($word);
            if(!in_array($word, $this->ignoredWords) && preg_match('/[a-z]{2,}/i', $word)) {
                $this->ignoredWords[] = $word;
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function save() {
        asort($this->ignoredWords);

        if(file_put_contents(
                $this->lastIgnoredWordsPath,
                implode(PHP_EOL, $this->ignoredWords)
            ) === false) {
            throw new \Exception('Save ignored words failed');
        }
    }
}