<?php

namespace App\Enums;

enum NotificationType: string
{
    case LeaveStatus = 'leave_status';
    case AbsenceReminder = 'absence_reminder';
    case Birthday = 'birthday';
    case NewLeaveRequest = 'new_leave_request';
    case System = 'system';
}
