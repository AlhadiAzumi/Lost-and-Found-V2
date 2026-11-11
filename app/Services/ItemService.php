<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class ItemService
{
    public function __construct(private NotificationService $notifications)
    {
    }

    public function createReport(array $data, User $reporter, ?UploadedFile $image = null): Item
    {
        $imagePath = $image?->store('items', 'public');

        if ($image !== null && $imagePath === false) {
            throw new RuntimeException('Foto laporan gagal disimpan. Silakan coba kembali.');
        }

        try {
            $item = DB::transaction(function () use ($data, $reporter, $imagePath): Item {
                $location = $this->resolveLocation($data);

                $item = Item::create([
                    'user_id' => $reporter->id,
                    'category_id' => $data['category_id'],
                    'location_id' => $location->id,
                    'type' => $data['type'],
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'location' => $data['location'],
                    'storage_location' => $data['storage_location'] ?? null,
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                    'date_event' => $data['date_event'],
                    'image' => $imagePath,
                    'is_sensitive' => $data['is_sensitive'] ?? false,
                    'security_question' => $data['security_question'] ?? null,
                    'security_answer' => $data['security_answer'] ?? null,
                    'status' => 'pending',
                ]);

                ActivityLog::create([
                    'user_id' => $reporter->id,
                    'action' => 'report_submitted',
                    'description' => "Mengirim laporan barang {$item->title}.",
                ]);

                return $item;
            });
        } catch (Throwable $exception) {
            if (is_string($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        $this->notifications->reportSubmitted($item);

        return $item;
    }

    public function updateReport(Item $item, array $data, ?UploadedFile $image = null): Item
    {
        $imagePath = $image?->store('items', 'public');

        if ($image !== null && $imagePath === false) {
            throw new RuntimeException('Foto laporan gagal disimpan. Silakan coba kembali.');
        }

        try {
            $updated = DB::transaction(function () use ($item, $data, $imagePath): Item {
                $lockedItem = Item::query()->lockForUpdate()->findOrFail($item->id);

                if ($lockedItem->user_id !== Auth::id()
                    || ! in_array($lockedItem->status, ['pending', 'rejected'], true)) {
                    throw ValidationException::withMessages([
                        'item' => 'Laporan ini tidak dapat diubah.',
                    ]);
                }

                $location = $this->resolveLocation($data);
                $lockedItem->update([
                    'category_id' => $data['category_id'],
                    'location_id' => $location->id,
                    'type' => $data['type'],
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'location' => $data['location'],
                    'storage_location' => $data['storage_location'] ?? null,
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                    'date_event' => $data['date_event'],
                    'image' => $imagePath ?? $lockedItem->image,
                    'is_sensitive' => $data['is_sensitive'] ?? false,
                    'security_question' => $data['security_question'] ?? null,
                    'security_answer' => $data['security_answer'] ?? null,
                    'status' => 'pending',
                    'rejection_reason' => null,
                    'approved_by' => null,
                ]);

                return $lockedItem;
            });
        } catch (Throwable $exception) {
            if (is_string($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        if (is_string($imagePath) && filled($item->image)) {
            Storage::disk('public')->delete($item->image);
        }

        return $updated;
    }

    public function moderate(Item $item, User $moderator, string $status, ?string $reason): Item
    {
        $updated = DB::transaction(function () use ($item, $moderator, $status, $reason): Item {
            $lockedItem = Item::query()->lockForUpdate()->findOrFail($item->id);

            if ($lockedItem->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'Hanya laporan yang menunggu moderasi yang dapat diproses.',
                ]);
            }

            $lockedItem->update([
                'status' => $status,
                'rejection_reason' => $status === 'rejected' ? $reason : null,
                'approved_by' => $status === 'approved' ? $moderator->id : null,
            ]);

            ActivityLog::create([
                'user_id' => $moderator->id,
                'action' => 'report_'.$status,
                'description' => "Memproses laporan {$lockedItem->title}: {$status}.",
            ]);

            return $lockedItem;
        });

        $this->notifications->reportStatusChanged($updated);

        return $updated;
    }

    public function close(Item $item, User $owner): Item
    {
        return DB::transaction(function () use ($item, $owner): Item {
            $lockedItem = Item::query()->lockForUpdate()->findOrFail($item->id);

            if ($lockedItem->user_id !== $owner->id
                || $lockedItem->type !== 'lost'
                || ! in_array($lockedItem->status, ['pending', 'approved'], true)) {
                throw ValidationException::withMessages([
                    'item' => 'Laporan ini tidak dapat ditutup.',
                ]);
            }

            $lockedItem->update(['status' => 'closed']);

            return $lockedItem;
        });
    }

    private function resolveLocation(array $data): Location
    {
        if (isset($data['location_id'])) {
            $location = Location::query()->findOrFail($data['location_id']);

            if ($location->name !== $data['location']) {
                throw ValidationException::withMessages([
                    'location_id' => 'Lokasi yang dipilih tidak sesuai dengan nama lokasi.',
                ]);
            }

            return $location;
        }

        return Location::query()->firstOrCreate(['name' => $data['location']]);
    }
}
