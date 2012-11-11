<?php

require_once __DIR__ .'/bootstrap.php';

use Rjob\Queue;
use Rjob\Job;

$job = new myJob();

$queue = new Queue($predis, 'simple');
$jobId = $queue->addJob($job);
echo 'job added '.$jobId .PHP_EOL;