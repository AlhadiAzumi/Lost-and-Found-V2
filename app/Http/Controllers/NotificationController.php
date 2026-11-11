<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('notifications.index', [
            'notifications' => $request->user()->notifications()->latest()->paginate(15),
        ]);
    }

    public function markAsRead(Request $request, Notification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);
        $notification->markAsRead();

        return filled($notification->url)
            ? redirect()->to($notification->url)
            : back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
