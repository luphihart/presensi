<?php

use App\Console\Commands\CalculateDailyAbsences;
use App\Console\Commands\SendBirthdayGreetings;
use App\Console\Commands\BackupDatabase;
use Illuminate\Support\Facades\Schedule;

// Run daily absences calculation at 23:00
Schedule::command(CalculateDailyAbsences::class)
    ->dailyAt('23:00')
    ->withoutOverlapping();

// Send birthday greetings every morning at 06:00
Schedule::command(SendBirthdayGreetings::class)
    ->dailyAt('06:00')
    ->withoutOverlapping();

// Run automated database backup daily at 02:00
Schedule::command(BackupDatabase::class)
    ->dailyAt('02:00')
    ->withoutOverlapping();

// Recalculate student leaderboard daily at 06:00
Schedule::command(\App\Console\Commands\UpdateLeaderboard::class)
    ->dailyAt('06:00')
    ->withoutOverlapping();

// Reset monthly points on the 1st of every month at 00:01
Schedule::command(\App\Console\Commands\ResetMonthlyPoints::class)
    ->monthlyOn(1, '00:01')
    ->withoutOverlapping();

// Process background queue jobs via cron every minute (cPanel shared hosting environment)
Schedule::command('queue:work --stop-when-empty --max-time=50')
    ->everyMinute()
    ->withoutOverlapping();
