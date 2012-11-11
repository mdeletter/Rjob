<?php

/**
 * php status.php simple
 */

require_once __DIR__ .'/bootstrap.php';

$queueName = $argv[1];
$delay = 1; // Default delay of 1 second

// Loop indefinitely
while(1)
{
	// Get the status
	$queue_lengths = array();
	$queue_lengths['high'] = $predis->llen('queue.'.$queueName.'.high');
	$queue_lengths['normal'] = $predis->llen('queue.'.$queueName.'.normal');
	$queue_lengths['low'] = $predis->llen('queue.'.$queueName.'.low');

	$queue_total = 0;
	// Change null values to 0's
	foreach($queue_lengths as $name => $size)
	{
		if($size == null)
		{
			$queue_lengths[$name] = 0;
		}

		$queue_total += $queue_lengths[$name];
	}

	// Trim out old workers that haven't "worked" in over an hour
	$workers_time = $predis->hgetall('worker.status.last_time');
	$time_limit = time() - 60 * 60 * 1;

	foreach($workers_time as $worker_id => $worker_ts)
	{
		if($worker_ts < $time_limit)
		{
			$predis->hdel('worker.status', $worker_id);
			$predis->hdel('worker.status.last_time', $worker_id);
		}
	}

	$workers = $predis->hgetall('worker.status');
	ksort($workers);

	// Display Queue status
	echo "\n----------------------\n";
	echo "Queue Statuses:\n\n";
	echo "	High:	".$queue_lengths['high']."\n";
	echo "	Normal:	".$queue_lengths['normal']."\n";
	echo "	Low:	".$queue_lengths['low']."\n\n";
	echo "	Total:	".$queue_total."\n\n";

	echo "Worker Statuses:\n\n";

	foreach($workers as $worker_id => $status)
	{
		if($worker_id > 0)
		{
			echo "	Worker [$worker_id]:	$status \n";
		}
	}

	echo "----------------------\n";

	// Sleep the delay
	sleep($delay);
}