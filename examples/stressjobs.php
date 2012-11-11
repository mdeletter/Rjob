<?php

require_once __DIR__ .'/bootstrap.php';

use Rjob\Queue;
use Rjob\Job;


$priority = $argv[1] ? $argv[1] : \Rjob\Job::PRIORITY_NORMAL;

$job = new myJob();
$job->setPriority($priority);

$queue = new Queue($predis, 'simple');

$count = 0;
$limit = 10000;
while($count < $limit)
{
    $count++;
    $jobId = $queue->addJob($job);
    echo 'job added '.$jobId .PHP_EOL;
}