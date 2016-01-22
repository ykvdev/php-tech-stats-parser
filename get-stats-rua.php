<?php

require_once __DIR__ . '/vendor/autoload.php';

$config = require __DIR__ . '/config.php';

$stats = [];
$ignoredWords = [];
$currPage = 1;
while(true) {
    echo "Get stats for page {$currPage}\n";

    $url = 'http://rabota.ua/jobsearch/vacancy_list?keyWords=PHP%2c+&pg=' . $currPage;
    $html = getPage($url);
    $saw = new nokogiri($html);

    $vacancyLinks = $saw->get('a.t')->toArray();
    if(!$vacancyLinks) {
        echo "THE END\n";
        break;
    }
    foreach($vacancyLinks as $link) {
        $link['href'] = 'http://rabota.ua' . $link['href'];
        $vacancyHtml = getPage($link['href']);
        $vacancySaw = new nokogiri($vacancyHtml);

        $text = $vacancySaw->get('#beforeContentZone_vcVwPopup_pnlVacancyLeftColumnHolder')->toText();
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

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36');
        $html = curl_exec($ch);
        curl_close($ch);

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