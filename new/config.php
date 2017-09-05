<?php

return [
    'paths' => [
        'get_stats_output_log' => APP_ROOT_PATH . '/results/get_stats_output.log',
        'stats_json' => APP_ROOT_PATH . '/results/stats_{year}.json',
        'last_ignored_words' => APP_ROOT_PATH . '/results/last_ignored_words.txt',
        'chart' => APP_ROOT_PATH . '/results/charts/{number}-{category}.png',
    ],

    'patterns' => [
        'PHP frameworks' => [
            'Zend Framework' => '(^|\W)zf(\W|\d|$)|zend\W*framework',
            'Symfony' => 'symfony|symphony',
            'Yii' => 'yii',
            'CakePHP' => 'cake\W*php',
            'Laravel' => 'laravel',
            'CodeIgniter' => 'code\W*igniter',
            'Kohana' => 'kohana',
            'Phalcon' => 'phalcon',
            'Zephir' => 'zephir',
            'Aura' => 'aura',
            'Silex' => 'silex',
            'FuelPHP' => 'fuel',
            'Slim' => 'slim',
            'PHPixie' => 'phpixie',
            'Fat-Free' => 'fat\W*free',
            'Lumen' => 'lumen',
            'Lithium (li3)' => 'li3|lithium',
        ],

        'PHP CMS' => [
            'Drupal' => 'drupal',
            'Wordpress' => 'wordpress|(\W|^)wp(\W|\d|$)',
            'Typo' => 'typo\W*\d',
            'Terminal4' => 'terminal\W*\d',
            'Joomla' => 'joomla',
            'Magento' => 'magento',
            'Bitrix' => 'bitrix',
            'OpenCart' => 'open\W*cart',
            'MODX' => 'mod\W*x',
            'NetCat' => 'net\W*cat',
            'DataLife Engine' => 'data\W*life',
            'UMI.CMS' => 'umi',
        ],

        'PHP форумы' => [
            'phpBB' => 'php\W*bb',
            'SMF' => 'smf',
            'Vbulletin' => 'v\W*bulletin',
    //        'IPS' => 'ips',
            'PunBB' => 'pun\W*bb',
            'Vanilla' => 'vanilla',
            'Xenforo' => 'xenforo',
            'ExBB' => 'ex\W*bb',
            'FluxBB' =>'flux\W*bb',
            'Flarum' => 'flarum',
        ],

        'Тестирование' => [
            'Selenium' => 'selenium|селениум',
            'PHP Unit' => 'php\W*unit',
            'Codeception' => 'code\W*ception',
        ],

        'PHP шаблонизаторы' => [
            'Smarty' => 'smarty',
            'Twig' => 'twig',
            'Volt' => 'volt',
            'Blade' => 'blade',
        ],

        'DB' => [
            'MySQL' => 'mysql',
            'PostgreSQL' => 'postgre',
            'SQLite' => 'sql\W*lite',
            'MSSQL' => 'ms\W*sql',
            'MongoDB' => 'mongo',
            'Redis' => 'redis',
            'MariaDB' => 'maria\W*db',
            'Tarantool' => 'tarantool',
            'ElasticSearch' => 'elastic\W*search',
            'Cassandra' => 'cassandra',
            'Oracle' => 'oracle',
            'RethinkDB' => 'rethink\W*db',
            'CouchDB' => 'couch\W*db',
            'Riak' => 'riak',
            'HBase' => 'hbase',
        ],

        'Очереди' => [
            'RabbitMQ' => 'rabbit\W*mq',
            'AMQP' => 'amqp',
            'ActiveMQ' => 'active\W*mq',
            'Beanstalk' => 'beanstalk',
            'Gearman' => 'gearman',
        ],

        'Поисковые движки' => [
            'Coveo' => 'coveo',
            'Sphinx' => 'sphinx',
            'Lucene' => 'lucene',
            'Solr' => 'solr',
        ],

        // -----------------------------------------------

        'Frontend' => [
            'jQuery' => 'j\W*query',
            'Bootstrap' => 'bootstrap',
            'Angular' => 'angular',
            'Bower' => 'bower',
            'TypeScript' => 'type\W*script',
            'LESS' => 'less',
            'SASS' => 'sass',
            'Gulp' => 'gulp',
            'Grunt' => 'grunt',
            'Yeoman' => 'yeoman',
            'Knockout' => 'knockout',
            'Backbone' => 'backbone',
            'Marionette.js' => 'marionette',
            'Ember' => 'ember',
            'io.js' => 'io\W*js',
            'SockJS' => 'sock\W*js',
            'Underscore' => 'underscore',
            'ReactJS' => 'react',
            'ExtJS' => 'ext\W*js',
            'ECMAScript' => 'ecma\W*script',
            'Dojo' => 'dojo',
            'Meteor.js' => 'meteor',
            'Jade' => 'jade',
            'Stylus' => 'stylus',
            'PostCSS' => 'postcss',
            'БЭМ' => 'бэм|bem',
            'RequireJS' => 'require\W*js',
            'CoffeeScript' => 'coffee\W*script',
            'Webpack' => 'web\W*pack',
            'Comet' => 'comet',
            'WebRTC' => 'web\W*rtc',
        ],

        // ------------------------------------------------------

        'VCS' => [
            'Mercurial' => 'mercurial|(\W|^)hg(\W|\d|$)',
            'Git' => 'git',
            'Subversion' => 'subversion|svn'
        ],

        'Web-сервера' => [
            'Apache' => 'apache',
            'NGINX' => 'nginx',
            'IIS' => 'iis',
            'lighttpd' => 'lighttpd|light\W*http',
        ],

        'Виртуализация' => [
            'Docker' => 'docker',
            'Puppet' => 'puppet',
            'Vagrant' => 'vagrant',
            'OpenVZ' => 'open\W*vz',
            'VirtualBox' => 'virtual\W*box',
            'VMware' => 'vm\W*ware',
            'Parallels Desktop' => 'parallels',
        ],

        'Логирование и мониторинг' => [
            'Munin' => 'munin',
            'Graylog' => 'gray\W*log',
            'Grafana' => 'grafana',
            'Logstash' => 'log\W*stash',
            'logrotate' => 'logrotate',
            'Cacti' => 'cacti|cacty',
        ],

        'Кеширование' => [
            'APC' => 'apc',
            'XCache' => 'xcache',
            'Varnish' => 'varnish',
            'Memcache' => 'memcache',
        ],

        'Профилирование и отладка' => [
            'XHprof' => 'xhprof',
            'Xdebug' => 'xdebug',
            'DBG' => 'dbg',
            'ZendDebug' => 'zend\W*debug',
            'Memtrack' => 'memtrack',
            'Pinba' => 'pinba',
        ],

        // -------------------------------------------

        'Прочее' => [
            'LAMP' => 'lamp',
            'WAMP' => 'wamp',
            'XAMPP' => 'xampp',
            'Bash' => 'bash|shh|shell',
            'Linux' => 'linux',
            'Unix' => 'unix',
            'XML' => 'xml',
            'SOAP' => 'soap',
            'REST' => 'rest',
            'Doctrine' => 'doctrine',
            'Composer' => 'composer',
            'RegExp' => 'regex|pcre|preg',
            'ООП' => 'oop|ооп|object orientated',
            'MVC' => 'mvc',
            'PSR' => 'psr',
            'SPL' => 'spl',
            'SOLID' => 'solid',
            'HHVM' => 'hhvm',
        ],

        // ------------------------------------------

        'CI' => [
            'Bamboo' => 'bamboo',
            'Stash' => 'stash',
            'TeamCity' => 'team\W*city',
            'Jenkins' => 'jenkins',
            'Phing' => 'phing',
            'Travis' => 'travis',
            'Capistrano' => 'capistrano',
        ],

        'Issue trackers' => [
            'JIRA' => 'jira',
            'Confluence' => 'confluence',
            'Redmine' => 'redmine',
            'Mantis' => 'mantis',
            'Bugzilla' => 'bugzilla',
            'Youtrack' => 'youtrack'
        ],

        'IDE' => [
            'PHPStorm' => 'php\W*storm',
            'NetBeans' => 'net\W*beans',
            'Eclipse' => 'eclipse',
            'Aptana' => 'aptana',
            'PHPDesigner' => 'php\W*designer',
            'SublimeText' => 'sublime',
            'Vim' => 'vim',
            'ZendStudio' => 'zend\W*studio',
            'Dreamweaver' => 'dream\W*weaver',
            'Komodo' => 'komodo',
        ],

        // -----------------------------------------

        'Должность' => [
            'Юниор' => 'junior|юниор|начинающий',
            'Мидл' => 'middle|мидл|средний',
            'Сеньер' => 'senior|сеньер|сеньёр|мастер',
            'Техлид' => 'тех\W*лид|tech\W*lead',
            'Тимлид' => 'тим\W*лид|teem\W*lead',
        ],

        'Методологии разработки' => [
            'Agile' => 'agile',
            'Scrum' => 'scrum',
            'Kanban' => 'kanban',
        ],

        'Техники разработки' => [
            'DDD' => 'ddd',
            'TDD' => 'tdd',
            'BDD' => 'bdd',
        ],
    ]
];