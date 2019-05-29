<?php

return [
    'Web-сервера' => [
        'Apache' => 'apache',
        'NGINX' => 'nginx',
        'IIS' => 'iis',
        'lighttpd' => 'lighttpd|light.{0,1}http',
    ],

    'Виртуализация' => [
        'Docker' => 'docker',
        'Puppet' => 'puppet',
        'Vagrant' => 'vagrant',
        'OpenVZ' => 'open.{0,1}vz',
        'VirtualBox' => 'virtual.{0,1}box',
        'VMware' => 'vm.{0,1}ware',
        'Parallels Desktop' => 'parallels',
    ],

    'Кеширование' => [
        'APC' => 'apc',
        'XCache' => 'xcache',
        'Varnish' => 'varnish',
        'Memcache' => 'memcache',
    ],

    'Логирование и мониторинг' => [
        'Munin' => 'munin',
        'Graylog' => 'gray.{0,1}log',
        'Grafana' => 'grafana',
        'Logstash' => 'log.{0,1}stash',
        'logrotate' => 'logrotate',
        'Cacti' => 'cacti|cacty',
    ],
];