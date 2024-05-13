<?php 
use Symfony\Component\Console\Schedule\Cron;
use Symfony\Component\Console\Schedule\Schedule;

$schedule = new Schedule();

// Schedule your command to run every minute
$schedule->command('app:my-command')->everyMinute();

return $schedule;