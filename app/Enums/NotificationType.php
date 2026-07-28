<?php

namespace App\Enums;

enum NotificationType: string
{
    case LeaveStatus = 'leave_status';
    case AbsenceReminder = 'absence_reminder';
    case Birthday = 'birthday';
    case NewLeaveRequest = 'new_leave_request';
    case Announcement = 'announcement';
    case StreakMilestone = 'streak_milestone';
    case System = 'system';
}
