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
                ->orderBy('id');
        } else {
            $leaderboardQuery->orderByDesc('total_points')
                ->orderByDesc('monthly_points')
                ->orderBy('id');
        }

        $allRankings = $leaderboardQuery->get();

        // Calculate current student rank dynamically based on current tab sorting
        $myRank = null;
        if ($student) {
            $rankCounter = 1;
            foreach ($allRankings as $item) {
                if ($item->id === $student->id) {
                    $myRank = $rankCounter;
                    break;
                }
                $rankCounter++;
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
