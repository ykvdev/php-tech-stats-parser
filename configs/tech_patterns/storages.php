<?php

return [
    'SQL СУБД' => [
        'MySQL' => 'mysql',
        'PostgreSQL' => 'postgre',
        'SQLite' => 'sql.{0,1}lite',
        'MSSQL' => 'ms.{0,1}sql',
        'MariaDB' => 'maria.{0,1}db',
    ],

    'Документо-ориентированные СУБД' => [
        'MongoDB' => 'mongo',
        'RethinkDB' => 'rethink.{0,1}db',
        'CouchDB' => 'couch.{0,1}db',
    ],

    'Графовые СУБД' => [
        'AllegroGraph' => 'allegro.{0,1}graph',
        'ArangoDB' => 'arango',
        'FlockDB' => 'flock',
        'Giraph' => 'giraph',
        'HyperGraphDB' => 'hyper.{0,1}graph',
        'InfiniteGraph' => 'infinite.{0,1}graph',
        'InfoGrid' => 'info.{0,1}grid',
        'Neo4j' => 'neo4j',
        'OrientDB' => 'orient.{0,1}db',
        'SparkSee' => 'spark.{0,1}see',
        'Sqrrl' => 'sqrrl',
        'Titan' => 'titan',
        'Datomic' => 'datomic',
    ],

    'Ключ-значение СУБД' => [
        'Redis' => 'redis',
        'Tarantool' => 'tarantool',
        'Riak' => 'riak',
    ],

    'Гибридные NoSQL СУБД' => [
        'Cassandra' => 'cassandra',
        'HBase' => 'hbase',
    ],

    'Очереди' => [
        'RabbitMQ' => 'rabbit.{0,1}mq',
        'AMQP' => 'amqp',
        'ActiveMQ' => 'active.{0,1}mq',
        'Beanstalk' => 'beanstalk',
        'Gearman' => 'gearman',
    ],

    'Поисковые движки' => [
        'Coveo' => 'coveo',
        'Sphinx' => 'sphinx',
        'Lucene' => 'lucene',
        'Solr' => 'solr',
        'ElasticSearch' => 'elastic.{0,1}search',
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