<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\Item;
use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function notify(User $user, string $type, string $title, string $message, ?string $url = null): Notification
    {
        return $user->notifications()->create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'url' => $url,
        ]);
    }

    public function reportSubmitted(Item $item): void
    {
        $item->loadMissing('campus');
        $url = $this->campusRoute($item, 'admin.reports');

        User::query()
            ->whereIn('role', ['admin', 'satpam'])
            ->get()
            ->each(fn (User $admin) => $this->notify(
                $admin,
                'report_submitted',
                'Laporan Baru Masuk',
                "{$item->user?->name} mengirim laporan \"{$item->title}\". Silakan moderasi.",
                $url
            ));
    }

    public function reportStatusChanged(Item $item): void
    {
        $item->loadMissing('user', 'campus');
        $owner = $item->user;

        if ($owner === null) {
            return;
        }

        if ($item->status === 'approved') {
            $message = "Laporan \"{$item->title}\" telah disetujui dan sekarang tampil di katalog.";
            $title = 'Laporan Disetujui';
            $type = 'report_approved';
        } else {
            $message = "Laporan \"{$item->title}\" ditolak. Alasan: {$item->rejection_reason}";
            $title = 'Laporan Ditolak';
            $type = 'report_rejected';
        }

        $this->notify($owner, $type, $title, $message, $this->campusRoute($item, 'my.history'));
    }

    public function claimSubmitted(Claim $claim): void
    {
        $claim->loadMissing('item', 'user');
        $item = $claim->item;

        if ($item === null) {
            return;
        }

        $url = $this->campusRoute($item, 'admin.claims');
        User::query()
            ->whereIn('role', ['admin', 'satpam'])
            ->get()
            ->each(fn (User $admin) => $this->notify(
                $admin,
                'claim_submitted',
                'Klaim Baru Masuk',
                "{$claim->user?->name} mengajukan klaim untuk \"{$item->title}\". Silakan verifikasi.",
                $url
            ));
    }

    public function claimDecided(Claim $claim): void
    {
        $claim->loadMissing('user', 'item');
        $item = $claim->item;

        if ($claim->user === null || $item === null) {
            return;
        }

        if ($claim->status === 'approved') {
            $title = 'Klaim Disetujui';
            $type = 'claim_approved';
            $message = "Klaim kamu untuk \"{$item->title}\" disetujui. Kode Serah Terima: {$claim->handover_code}.";
        } else {
            $title = 'Klaim Ditolak';
            $type = 'claim_rejected';
            $message = "Klaim kamu untuk \"{$item->title}\" ditolak. Alasan: {$claim->admin_note}";
        }

        $this->notify($claim->user, $type, $title, $message, $this->campusRoute($item, 'my.history'));
    }

    public function handoverCompleted(Claim $claim): void
    {
        $claim->loadMissing('user', 'item');

        if ($claim->user === null || $claim->item === null) {
            return;
        }

        $this->notify(
            $claim->user,
            'handover_completed',
            'Barang Telah Diserahkan',
            "Barang \"{$claim->item->title}\" telah diserahkan setelah Kode Serah Terima diverifikasi.",
            $this->campusRoute($claim->item, 'my.history')
        );
    }

    private function campusRoute(Item $item, string $route): string
    {
        $campusSlug = $item->campus?->slug ?? config('campus.default_slug');

        return route('campus.'.$route, ['campus' => $campusSlug]);
    }
}
