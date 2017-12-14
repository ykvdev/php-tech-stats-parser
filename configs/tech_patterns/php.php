<?php

return [
    'PHP фреймворки' => [
        'Zend Framework' => '(^|\W)zf(\W|\d|$)|zend.{0,1}framework',
        'Symfony' => 'symfony|symphony',
        'Yii' => 'yii',
        'Laravel' => 'laravel',
        'CodeIgniter' => 'code.{0,1}igniter',
        'Kohana' => 'kohana',
        'Phalcon' => 'phalcon',
        'Aura' => 'aura',
        'FuelPHP' => 'fuel',
        'CakePHP' => 'cake.{0,1}php',
    ],

    'PHP микро-фреймворки' => [
        'Silex' => 'silex',
        'Slim' => 'slim',
        'Lumen' => 'lumen',
        'PHPixie' => 'phpixie',
        'Lithium (li3)' => 'li.{0,1}3|lithium',
        'Fat-Free' => 'fat.{0,1}free',
    ],

    'PHP CMS' => [
        'Drupal' => 'drupal',
        'Wordpress' => 'wordpress|(\W|^)wp(\W|\d|$)',
        'Typo' => 'typo.{0,1}\d',
        'Terminal4' => 'terminal.{0,1}\d',
        'Joomla' => 'joomla',
        'Magento' => 'magento',
        'Bitrix' => 'bitrix',
        'OpenCart' => 'open.{0,1}cart',
        'MODX' => 'mod.{0,1}x',
        'NetCat' => 'net.{0,1}cat',
        'DataLife Engine' => 'data.{0,1}life',
        'UMI.CMS' => 'umi',
    ],

    'PHP форумы' => [
        'phpBB' => 'php.{0,1}bb',
        'SMF' => 'smf',
        'Vbulletin' => 'v.{0,1}bulletin',
        //        'IPS' => 'ips',
        'PunBB' => 'pun.{0,1}bb',
        'Vanilla' => 'vanilla',
        'Xenforo' => 'xenforo',
        'ExBB' => 'ex.{0,1}bb',
        'FluxBB' =>'flux.{0,1}bb',
        'Flarum' => 'flarum',
    ],

    'PHP тестирование' => [
        'Selenium' => 'selenium|селениум',
        'PHP Unit' => 'php.{0,1}unit',
        'Codeception' => 'code.{0,1}ception',
        'Behat' => 'behat',
        'PhpSpec' => 'php.{0,1}spec',
        'Peridot' => 'peridot',
    ],

    'PHP шаблонизаторы' => [
        'Smarty' => 'smarty',
        'Twig' => 'twig',
        'Volt' => 'volt',
        'Blade' => 'blade',
    ],

    'PHP ORM' => [
        'Propel' => 'propel',
        'Doctrine' => 'doctrine',
        'RedBeanPHP' => 'red.{0,1}bean',
    ],

    'PHP менеджеры пакетов' => [
        'Composer' => 'composer',
        'PEAR' => 'pear',
    ],

    'PHP профилирование и отладка' => [
        'XHprof' => 'xhprof',
        'Xdebug' => 'xdebug',
        'DBG' => 'dbg',
        'ZendDebug' => 'zend.{0,1}debug',
        'Memtrack' => 'memtrack',
        'Pinba' => 'pinba',
    ],

    'PHP прочее' => [
        'Zephir' => 'zephir',
        'PSR' => 'psr',
        'SPL' => 'spl',
        'SOAP' => 'soap',
        'REST' => 'rest',
        'phpDoc' => 'php.{0,1}doc',
    ],
];