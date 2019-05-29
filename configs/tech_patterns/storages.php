<?php

return [
    'SQL СУБД' => [
        'MySQL' => 'mysql',
        'PostgreSQL' => 'postgre',
        'SQLite' => 'sql.{0,1}lite',
        'MSSQL' => 'ms.{0,1}sql',
        'MariaDB' => 'maria.{0,1}db',
        'Firebird' => 'firebird',
    ],

    'NoSQL СУБД' => [
        'MongoDB' => 'mongo',
        'RethinkDB' => 'rethink.{0,1}db',
        'CouchDB' => 'couch.{0,1}db',
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
        'Redis' => 'redis',
        'Tarantool' => 'tarantool',
        'Riak' => 'riak',
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
];