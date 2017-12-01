<?php

return [
    'paths' => [
        'get_stats_output_log' => APP_ROOT_PATH . '/results/get_stats_output.log',
        'stats_json' => APP_ROOT_PATH . '/results/stats_{year}.json',
        'last_ignored_words' => APP_ROOT_PATH . '/results/last_ignored_words.txt',
        'chart' => APP_ROOT_PATH . '/results/charts/{number}-{category}.png',
    ],

    'tech_patterns' => array_merge(
        require __DIR__ . '/tech_patterns/php.php',
        require __DIR__ . '/tech_patterns/storages.php',
        require __DIR__ . '/tech_patterns/server.php',
        require __DIR__ . '/tech_patterns/js.php',
        require __DIR__ . '/tech_patterns/css.php',
        require __DIR__ . '/tech_patterns/tools.php',
        require __DIR__ . '/tech_patterns/others.php'
    ),
];