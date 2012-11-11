<?php

namespace Rjob;

use Rjob\Queue;

/**
 * Worker instance
 */
class Worker
{
    protected $id;

    protected $predis;

    protected $queue;

    public function __construct($id, $predis, $queue)
    {
        $this->id     = $id;
        $this->predis = $predis;
        $this->queue  = $queue;
    }

    public function run()
    {
        set_time_limit(0);

        // Setting the Worker status
        $this->predis->hset('worker.status', $this->id, 'Started');
        $this->predis->hset('worker.status.last_time', $this->id, time());

        $version = $this->predis->get('worker.version');

        $timeLimit = 60 * 60 * 1; // Minimum of 1 hour
        $timeLimit += rand(0, 60 * 10); // Adding additional time

        // Set the start time
        $startTime = time();
        $endTime = $startTime + $timeLimit;

        while(time() < $endTime)
        {
            $this->predis->hset('worker.status', $this->id, 'Running');

            $job = $this->queue->listen();
            if ($job === null) {
                continue;
            }

            $job->execute();

            if($this->predis->get('worker.version') != $version) {
                echo "New Version Detected... \n";
                echo "Reloading... \n";
                $this->predis->hset('worker.status', $this->id, 'Stopped');
                exit();
            }
        }
    }
}