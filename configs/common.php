<?php

return [
    'paths' => [
        'get_stats_output_log' => APP_ROOT_PATH . '/results/get_stats_output.log',
        'stats_json' => APP_ROOT_PATH . '/results/stats_{year}.json',
        'last_ignored_words' => APP_ROOT_PATH . '/results/last_ignored_words.txt',
        'chart' => APP_ROOT_PATH . '/results/charts/{source}/{year}/{month}/{number}-{category}.png',
    ],

    'sources' => [
        'RU_work_hh.ru' => [
            'pages_url' => 'http://hh.ru/search/vacancy?items_on_page=100&enable_snippets=true&text=PHP&no_magic=true&clusters=true&search_period=30&currency_code=USD&page=%d',
            'vacancy_urls_selector' => 'div.vacancy-serp-item__info div.resume-search-item__name a',
            'vacancy_title_selector' => 'h1',
            'vacancy_text_selector' => 'div.b-vacancy-desc-wrapper, div.l-paddings.b-vacancy-desc.g-user-content, .vacancy-description',
        ],
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