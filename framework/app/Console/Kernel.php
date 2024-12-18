<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\NotificationsCommand',
        'App\Console\Commands\EmailNotification',
        'App\Console\Commands\PushNotification',
        'App\Console\Commands\VersionAutoUpdate',

    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('notification:generate')->dailyAt('10:00');
        $schedule->command('notification:generate')->dailyAt('16:00');
        $schedule->command('notification:generate')->dailyAt('21:00');
        $schedule->command('push:notification')->dailyAt('10:00');
        $schedule->command('push:notification')->dailyAt('16:00');
        $schedule->command('push:notification')->dailyAt('21:00');
        $schedule->command('email:notification')->dailyAt('10:00');
        $schedule->command('email:notification')->dailyAt('16:00');
        $schedule->command('email:notification')->dailyAt('21:00');
        // $schedule->command('backup:clean')->daily()->at('01:00');
        // $schedule->command('backup:run')->daily()->at('01:30');
    }

    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
