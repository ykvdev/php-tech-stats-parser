<?php

require __DIR__ . '/vendor/autoload.php';

define('APP_ROOT_PATH', __DIR__);

$app = new \Symfony\Component\Console\Application('Blog CLI tech stats', '1.0');
$app->addCommands([
    new \app\commands\GetStatsHh()
]);
$app->run();