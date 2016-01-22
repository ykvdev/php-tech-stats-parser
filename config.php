<?php

return [
    'PHP frameworks' => [
        'Zend Framework' => ['\szf(\s|\d)', 'zend'],
        'Symfony' => ['symfony', 'symphony'],
        'Yii' => ['yii'],
        'CakePHP' => ['cakephp'],
        'Laravel' => ['laravel'],
        'CodeIgniter' => ['codeigniter', 'code\s+igniter'],
        'Kohana' => ['kohana'],
        'Lararel' => ['lararel'],
        'Phalcon' => ['phalcon'],
        'Aura' => ['aura'],
        'Silex' => ['silex'],
        'FuelPHP' => ['fuel'],
        'Slim' => ['slim'],
        'PHPixie' => ['phpixie'],
        'Fat-Free' => ['fat-free', 'fatfree', 'fat\s+free'],
        'Lumen' => ['lumen'],
        'Lithium (li3)' => ['li3', 'lithium'],
    ],

    'PHP CMS' => [
        'Drupal' => ['drupal'],
        'Wordpress' => ['wordpress', '\swp\s'],
        'Typo' => ['typo\d'],
        'Terminal4' => ['terminal\d'],
        'Joomla' => ['joomla'],
        'Magento' => ['magento'],
        'Bitrix' => ['bitrix'],
        'OpenCart' => ['opencart', 'open\s+cart'],
        'MODX' => ['modx'],
        'NetCat' => ['netcat', 'net\s+cat'],
        'DataLife Engine' => ['datalife', 'data\s+life'],
        'UMI.CMS' => ['umi']
    ],

    'PHP шаблонизаторы' => [
        'Smarty' => ['smarty'],
        'Twig' => ['twig'],
        'Volt' => ['volt'],
    ],

    'Frontend (JS/CSS/HTML)' => [
        'jQuery' => ['jquery'],
        'Bootstrap' => ['bootstrap'],
        'Angular' => ['angular'],
        'Bower' => ['bower'],
        'TypeScript' => ['typescript', 'type\s+script'],
        'NPM' => ['npm'],
        'LESS' => ['less'],
        'SASS' => ['sass'],
        'Gulp' => ['gulp'],
        'Grunt' => ['grunt'],
        'Yeoman' => ['yeoman'],
        'Knockout' => ['knockout'],
        'Backbone' => ['backbone'],
        'Marionette.js' => ['marionette'],
        'Ember' => ['ember'],
        'io.js' => ['io.js'],
        'SockJS' => ['sockjs'],
        'Underscore' => ['underscore'],
        'ReactJS' => ['reactjs', 'react\s+js'],
        'ExtJS' => ['extjs', 'ext\s+js'],
        'ECMAScript' => ['ecmascript'],
        'Dojo' => ['dojo'],
        'Meteor.js' => ['meteor'],
    ],

    'Databases' => [
        'MySQL' => ['mysql', 'sql'],
        'PostgreSQL' => ['postgre'],
        'MongoDB' => ['mongodb'],
        'Redis' => ['redis'],
        'Memcache' => ['memcache'],
        'MariaDB' => ['mariadb', 'maria\s+db'],
        'Tarantool' => ['tarantool'],
        'ElasticSearch' => ['elasticsearch', 'elastic\s+search'],
        'Cassandra' => ['cassandra'],
        'Oracle' => ['oracle'],
    ],

    'Очереди' => [
        'Rabbit MQ' => ['rabbit\s+mq'],
        'Beanstalk' => ['beanstalk'],
        'Gearman' => ['gearman'],
    ],

    'Поисковые движки' => [
        'Coveo' => ['coveo'],
        'Sphinx' => ['sphinx'],
        'Lucene' => ['lucene'],
        'Solr' => ['solr'],
    ],

    'VCS' => [
        'Mercurial' => ['mercurial', '\shg\s'],
        'Git' => ['git'],
        'Subversion' => ['subversion', 'svn']
    ],

    'Серверные технологии' => [
        'Bash' => ['bash', 'shh', 'shell'],
        'LAMP' => ['lamp'],
        'WAMP' => ['wamp'],
        'XAMPP' => ['xampp'],
        'Apache' => ['apache'],
        'NGINX' => ['nginx'],
        'Docker' => ['docker'],
        'Puppet' => ['puppet'],
        'LVM' => ['lvm'],
        'Ansible' => ['ansible'],
        'Vagrant' => ['vagrant'],
        'AWS' => ['aws'],
        'Heroku' => ['heroku'],
    ],

    'Прочее' => [
        'XML' => ['xml'],
        'SOAP' => ['soap'],
        'REST' => ['rest'],
        'Doctrine' => ['doctrine'],
        'Composer' => ['composer'],
        'RegExp' => ['regexp'],
        'Node.js' => ['node.js', 'nodejs', 'node\s+js'],
        'ООП' => ['oop', 'ооп', 'object orientated'],
        'MVC' => ['mvc'],
        'PSR' => ['psr'],
        'Ruby' => ['ruby'],
        'Python' => ['python'],
        'Perl' => ['perl'],
        'ActionScript' => ['actionscript', 'action\s+script'],
        'Erlang' => ['erlang'],
        'Haskell' => ['haskell'],
        'Objective-C' => ['objective-c'],
        'Scala' => ['scala'],
        'TDD' => ['tdd'],
        'BDD' => ['bdd'],
        'Xdebug' => ['xdebug'],
        'SEO' => ['seo'],
    ],

    'CI' => [
        'Bamboo' => ['bamboo'],
        'Stash' => ['stash'],
        'TeamCity' => ['teamcity', 'team\s+city'],
        'Jenkins' => ['jenkins']
    ],

    'Issue trackers' => [
        'JIRA' => ['jira'],
        'Confluence' => ['confluence'],
        'Redmine' => ['redmine'],
        'Mantis' => ['mantis'],
        'Bugzilla' => ['bugzilla'],
        'Youtrack' => ['youtrack']
    ],

    'Тестирование' => [
        'Selenium' => ['selenium', 'селениум'],
        'PHP Unit' => ['php\s+unit', 'phpunit'],
        'Selendroid' => ['selendroid'],
        'Appium' => ['appium'],
    ],

    'Должность' => [
        'Юниор' => ['junior', 'юниор', 'начинающий'],
        'Мидл' => ['middle', 'мидл', 'средний'],
        'Сеньер' => ['senior', 'сеньер', 'сеньёр', 'мастер'],
        'Тимлид' => [
            'тимлид', 'тим-лид', 'тим\s+лид',
            'техлид', 'тех-лид', 'тех\s+лид',
            'teemlead', 'teem\s+lead', 'teem-lead',
            'techlead', 'tech\s+lead', 'tech-lead',
        ],
    ],

    'Методологии разработки' => [
        'Agile' => ['agile'],
        'Scrum' => ['scrum'],
        'kanban' => ['kanban'],
    ],
];