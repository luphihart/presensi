<?php

namespace App\Console\Commands;

use App\Services\DisciplinePointService;
use Illuminate\Console\Command;

class ResetMonthlyPoints extends Command
{
    protected $signature = 'discipline:reset-monthly';
    protected $description = 'Reset monthly points and ranks for all students at the start of a new month';

    public function handle(DisciplinePointService $disciplineService): int
    {
        $this->info('Resetting monthly points for all active students...');
        $disciplineService->resetMonthlyPoints();
        $this->info('Monthly points reset successfully!');
        return Command::SUCCESS;
    }
}
