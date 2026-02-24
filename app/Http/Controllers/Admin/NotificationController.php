<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        $unreadCount = Auth::user()->unreadNotifications()->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markRead(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        // Redirect to the notification's URL if available
        $url = $notification->data['url'] ?? route('admin.notifications.index');
        return redirect($url);
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * API endpoint for notification bell polling
     */
    public function recent()
    {
        $notifications = Auth::user()->notifications()->take(10)->get();
        $unreadCount = Auth::user()->unreadNotifications()->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->data['title'] ?? '',
                    'message' => $n->data['message'] ?? '',
                    'icon' => $n->data['icon'] ?? 'fas fa-bell',
                    'color' => $n->data['color'] ?? 'gray',
                    'url' => $n->data['url'] ?? '#',
                    'read' => $n->read_at !== null,
                    'time' => $n->created_at->diffForHumans(),
                ];
            }),
        ]);
    }
}
