<?php

namespace App\Console\Commands;

use App\Services\DisciplinePointService;
use Illuminate\Console\Command;

class UpdateLeaderboard extends Command
{
    protected $signature = 'discipline:update-leaderboard';
    protected $description = 'Recalculate monthly rankings for all students per class';

    public function handle(DisciplinePointService $disciplineService): int
    {
        $this->info('Recalculating student leaderboard rankings...');
        $disciplineService->recalculateRanks();
        $this->info('Student leaderboard rankings recalculated successfully!');
        return Command::SUCCESS;
    }
}
