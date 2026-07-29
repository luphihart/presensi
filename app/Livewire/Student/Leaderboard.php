<?php

namespace App\Livewire\Student;

use App\Models\Student;
use App\Models\StudentBadge;
use App\Services\DisciplinePointService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.student')]
class Leaderboard extends Component
{
    public string $tab = 'monthly'; // 'monthly' or 'all_time'

    public function setTab(string $tabName): void
    {
        if (in_array($tabName, ['monthly', 'all_time'], true)) {
            $this->tab = $tabName;
        }
    }

    public function render()
    {
        $user = Auth::user();
        $student = $user->student;

        $classRoom = $student?->classRoom;

        $leaderboardQuery = Student::with('user')
            ->where('is_active', true);

        if ($classRoom) {
            $leaderboardQuery->where('class_room_id', $classRoom->id);
        }

        if ($this->tab === 'monthly') {
            $leaderboardQuery->orderByDesc('monthly_points')
                ->orderByDesc('total_points')
                ->orderByDesc('current_streak')
                ->orderByRaw('CASE WHEN avg_check_in_seconds IS NULL THEN 1 ELSE 0 END, avg_check_in_seconds ASC')
                ->orderBy('id');
        } else {
            $leaderboardQuery->orderByDesc('total_points')
                ->orderByDesc('monthly_points')
                ->orderByDesc('current_streak')
                ->orderByRaw('CASE WHEN avg_check_in_seconds IS NULL THEN 1 ELSE 0 END, avg_check_in_seconds ASC')
                ->orderBy('id');
        }

        $classId = $classRoom?->id ?? 'global';
        $cacheKey = "leaderboard_rankings_{$classId}_{$this->tab}";

        $allRankings = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($leaderboardQuery) {
            return $leaderboardQuery->get();
        });

        // Compute dynamic ranks considering ties
        $myRank = null;
        $currentRank = 1;
        $prevKey = null;

        foreach ($allRankings as $index => $item) {
            $primary = $this->tab === 'monthly' ? $item->monthly_points : $item->total_points;
            $secondary = $this->tab === 'monthly' ? $item->total_points : $item->monthly_points;
            $key = "{$primary}_{$secondary}_{$item->current_streak}_" . ($item->avg_check_in_seconds ?? 'null');

            if ($prevKey !== null && $key === $prevKey) {
                $assignedRank = $currentRank;
            } else {
                $assignedRank = $index + 1;
                $currentRank = $assignedRank;
            }
            $prevKey = $key;

            $item->computed_rank = $assignedRank;

            if ($student && $item->id === $student->id) {
                $myRank = $assignedRank;
            }
        }

        $topThree = $allRankings->take(3);
        $remainingRankings = $allRankings->skip(3);

        // Fetch student badges
        $earnedBadges = $student ? StudentBadge::where('student_id', $student->id)
            ->pluck('earned_at', 'badge_key')
            ->toArray() : [];

        $allBadges = DisciplinePointService::BADGES_DEFINITION;

        return view('livewire.student.leaderboard', [
            'student' => $student,
            'classRoom' => $classRoom,
            'myRank' => $myRank,
            'topThree' => $topThree,
            'allRankings' => $allRankings,
            'remainingRankings' => $remainingRankings,
            'earnedBadges' => $earnedBadges,
            'allBadges' => $allBadges,
        ]);
    }
}
