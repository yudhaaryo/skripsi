<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        
        $schedule->command('cek:terlambat')->dailyAt('07:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'');
        require base_path('routes/console.php');
    }
}
