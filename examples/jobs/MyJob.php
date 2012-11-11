<?php

use Rjob\Job;

class myJob extends Job
{
    public function execute()
    {
        echo sprintf('Executing %s job with id: %d', $this->getPriority(), $this->getId()). PHP_EOL;
    }
}