<?php

namespace App\Livewire\Shared;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationCenter extends Component
{
    public bool $isOpen = false;

    public function toggle(): void
    {
        $this->isOpen = !$this->isOpen;
    }

    public function markAsRead(int $notificationId): void
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->update(['is_read' => true, 'read_at' => now()]);
        }
    }

    public function markAllAsRead(): void
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function render()
    {
        $user = Auth::user();
        $notifications = $user ? Notification::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get() : collect();

        $unreadCount = $notifications->where('is_read', false)->count();

        return view('livewire.shared.notification-center', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
