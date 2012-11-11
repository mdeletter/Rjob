<?php

namespace Rjob;

class Queue
{
    /**
     * Name of this Queue
     * @var string
     */
    protected $name;

    /**
     * Predis client
     * @var \Predis\Client
     */
    protected $predis;

    public function __construct(\Predis\Client $predis, $name)
    {
        $this->predis = $predis;
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    protected function getQueueKey()
    {
        return 'queue.'.$this->getName();
    }

    public function addJob(Job $job)
    {
        $key = $this->getQueueKey();

        $jobId = $this->predis->incr($key.'.job_id');

        $data = json_encode(array(
            'id'        => $jobId,
            'class'     => get_class($job),
            'priority'  => $job->getPriority(),
            'params'    => $job->getParams()
        ));

        $this->predis->rpush($key.'.'.$job->getPriority(), $data);

        return $jobId;
    }

    /**
     * Listen to this queue. This function is blocking!
     */
    public function listen($timeout = 10)
    {
        $key = $this->getQueueKey();
        $data = $this->predis->blpop(
            $key.'.'.Job::PRIORITY_HIGH,
            $key.'.'.Job::PRIORITY_NORMAL,
            $key.'.'.Job::PRIORITY_LOW,
            $timeout
        );

        if ($data !== null) {
            $data = json_decode($data[1]);

            $class = $data->class;

            $job = new $class($data->params);
            return $job->setId($data->id)->setPriority($data->priority);
        }
    }

}