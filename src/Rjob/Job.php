<?php

namespace Rjob;

/**
 * Job
 */
abstract class Job
{
    const PRIORITY_HIGH   = 'high';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_LOW    = 'low';

    protected $id;

    protected $params;

    protected $logger;

    protected $priority = self::PRIORITY_NORMAL;

    public function __construct($params = array())
    {
        $this->params = $params;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    abstract public function execute();
}