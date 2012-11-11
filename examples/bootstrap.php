<?php

require_once dirname(__DIR__).'/vendor/autoload.php';
require_once 'jobs/MyJob.php';

$predis = new Predis\Client(array(
    'scheme' => 'tcp',
    'host'   => '127.0.0.1',
    'port'   => 6379
));