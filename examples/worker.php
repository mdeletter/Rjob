<?php

require_once __DIR__ .'/bootstrap.php';

use Rjob\Queue;
use Rjob\Worker;

$workerId = $argv[1] ? $argv[1] : 1;

$queue = new Queue($predis, 'simple');
$worker = new Worker($workerId, $predis, $queue);
$worker->run();