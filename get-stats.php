<?php

require_once __DIR__ . '/vendor/autoload.php';

$config = require __DIR__ . '/config.php';

$stats = [];
$ignoredWords = [];
$currPage = 0;
while(true) {
    echo "Get stats for page {$currPage}\n";

    $url = 'http://hh.ru/search/vacancy?items_on_page=100&enable_snippets=true&text=PHP&no_magic=true&clusters=true&search_period=30&currency_code=USD&page=' . $currPage;
    $html = getPage($url);
    $saw = new nokogiri($html);

    $vacancyLinks = $saw->get('div.search-result-item__head a')->toArray();
    if(!$vacancyLinks) {
        echo "THE END\n";
        break;
    }
    foreach($vacancyLinks as $link) {
        $vacancyHtml = getPage($link['href']);
        $vacancySaw = new nokogiri($vacancyHtml);

        $text = $vacancySaw->get('h1.b-vacancy-title')->toText() . ' ';
        $text .= $vacancySaw->get('div.b-vacancy-desc-wrapper')->toText();
        if(!$text) {
            echo "Get vacancy text failed {$link['href']}\n";
            continue;
        }

        $stats['total_vacancy_number'] = isset($stats['total_vacancy_number'])
            ? $stats['total_vacancy_number'] + 1 : 1;

        $vacancyTechs = [];
        foreach($config as $category => $techs) {
            foreach($techs as $tech => $patterns) {
                foreach($patterns as $pattern) {
                    if(!in_array($tech, $vacancyTechs) && preg_match('/' . $pattern . '/Ui', $text)) {
                        $stats[$category][$tech] = isset($stats[$category][$tech])
                            ? $stats[$category][$tech] + 1 : 1;
                        $vacancyTechs[] = $tech;
                        $text = preg_replace('/' . $pattern . '/Ui', '', $text);
                        break;
                    }
                }
            }
        }

        foreach(explode(' ', $text) as $word) {
            if(!in_array($word, $ignoredWords) && preg_match('/[a-z]{2,}/i', $word)) {
                $ignoredWords[] = trim($word);
            }
        }
    }

    $currPage++;
}

foreach($stats as &$techs) {
    if(is_array($techs)) {
        arsort($techs);
    }
}

function getPage($url) {
    for($i = 1; $i <= 5; $i++) {
        sleep(mt_rand(0, 5));
        $html = @file_get_contents($url);
        if($html) {
            return $html;
        }
    }

    return false;
}

// Save ignored words
file_put_contents(__DIR__ . '/results/last-ignored-words.txt', implode(PHP_EOL, $ignoredWords));

// Save json stats
$filePath = __DIR__ . '/results/' . date('Y') . '-stats.json';
$yearStats = @json_decode(file_get_contents($filePath), true);
$yearStats[date('m')] = $stats;
file_put_contents($filePath, json_encode($yearStats));