<?php

return [
    'paths' => [
        'get_stats_output_log' => APP_ROOT_PATH . '/results/get_stats_output.log',
        'stats_json' => APP_ROOT_PATH . '/results/stats_{year}.json',
        'last_ignored_words' => APP_ROOT_PATH . '/results/last_ignored_words.txt',
        'chart' => APP_ROOT_PATH . '/results/charts/{source}/{year}/{month}/{number}-{category}.png',
    ],

    'sources' => [
        'ru_work_hh.ru' => [
            'pages_url' => 'http://hh.ru/search/vacancy?items_on_page=100&enable_snippets=true&text=PHP&no_magic=true&clusters=true&search_period=30&currency_code=USD&page={pageNumber}',
            'first_page_number' => 0,
            'vacancy_urls_selector' => 'div.vacancy-serp-item__info div.resume-search-item__name a',
            'vacancy_text_selector' => 'div.b-vacancy-desc-wrapper, div.l-paddings.b-vacancy-desc.g-user-content, .vacancy-description',
        ],

        'ru_freelance_weblancer.net' => [
            'pages_url' => 'https://www.weblancer.net/jobs/?action=search&status=&type=&keywords=php&page={pageNumber}',
            'first_page_number' => 1,
            'vacancy_urls_selector' => 'div.page_content div.cols_table div.col-sm-10 h2 a',
            'vacancy_text_selector' => 'div.page_content div.col-12.text_field',
        ],
        // https://freelansim.ru/tasks?_=1559198686815&page=2&q=php

        'ua_work_rabota.ua' => [
            'pages_url' => 'https://rabota.ua/zapros/php/%D1%83%D0%BA%D1%80%D0%B0%D0%B8%D0%BD%D0%B0/pg{pageNumber}',
            'first_page_number' => 1,
            'vacancy_urls_selector' => 'td article div.fd-f-left h3 a',
            'vacancy_text_selector' => 'div#ctl00_content_vcVwPopup_VacancyViewInner1_pnlBody',
        ],
        //https://www.work.ua/ru/jobs-php/?page=2
        //https://hh.ua/search/vacancy?area=5&clusters=true&currency_code=UAH&enable_snippets=true&items_on_page=100&no_magic=true&text=php&page=1

        'ua_freelance_freelance.ua' => [
            'pages_url' => 'https://freelance.ua/?page={pageNumber}&q=php',
            'first_page_number' => 1,
            'vacancy_urls_selector' => 'section.l-mainContent div.j-list header.l-project-title a',
            'vacancy_text_selector' => 'section.l-mainContent article',
        ],

        'by_work_jobs.tut.by' => [
            'pages_url' => 'https://jobs.tut.by/search/vacancy?area=16&clusters=true&currency_code=BYR&enable_snippets=true&items_on_page=100&no_magic=true&text=php&page={pageNumber}',
            'first_page_number' => 0,
            'vacancy_urls_selector' => 'div.vacancy-serp-item__info div.resume-search-item__name a',
            'vacancy_text_selector' => 'div.b-vacancy-desc-wrapper, div.l-paddings.b-vacancy-desc.g-user-content, .vacancy-description',
        ],

        'by_freelance_freelancehunt.by' => [
            'pages_url' => 'https://freelancehunt.by/projects/skill/php/1.html?page={pageNumber}',
            'first_page_number' => 1,
            'vacancy_urls_selector' => 'table.project-list tr td a.bigger',
            'vacancy_text_selector' => 'div.container div.well div.linkify-marker',
        ],

        'west_work_dice.com' => [
            'pages_url' => 'https://www.dice.com/jobs/advancedResult?q=%28php%29&sort=date&p={pageNumber}',
            'first_page_number' => 1,
            'vacancy_urls_selector' => 'div#search-results-control div.serp-result-content h3 a',
            'vacancy_text_selector' => 'div#jobdescSec',
        ],
        // https://geekbrains.ru/posts/relocation_work_resources

        'west_freelance_freelancer.com' => [
            'pages_url' => 'https://www.freelancer.com/jobs/{pageNumber}/?keyword=php',
            'first_page_number' => 1,
            'vacancy_urls_selector' => '#project-list div.JobSearchCard-primary div.JobSearchCard-primary-heading a',
            'vacancy_text_selector' => 'div.Card-body div.PageProjectViewLogout-detail, div.logoutHero-column',
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