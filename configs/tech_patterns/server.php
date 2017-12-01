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
];