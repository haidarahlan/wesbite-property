<?php

namespace App\Services;

use App\Models\Unit;
use App\Models\UnitPhoto;
use App\Models\UnitStatusHistory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UnitService
{
    /**
     * Membuat unit baru beserta foto dan riwayat status awal.
     */
    public function createUnit(array $data, ?int $userId = null): Unit
    {
        return DB::transaction(function () use ($data, $userId) {
            $data['slug'] = Str::slug($data['title']) . '-' . strtolower($data['code']);
            $data['is_published'] = $data['is_published'] ?? true;

            $unit = Unit::create($data);

            // Simpan audit trail status awal
            UnitStatusHistory::create([
                'unit_id' => $unit->id,
                'user_id' => $userId,
                'from_status' => null,
                'to_status' => $unit->status,
                'notes' => 'Unit pertama kali didaftarkan ke sistem.',
            ]);

            // Upload foto jika ada
            if (!empty($data['photos'])) {
                $this->uploadPhotos($unit, $data['photos'], $data['primary_photo_index'] ?? 0);
            }

            return $unit->load(['photos', 'developer', 'statusHistories']);
        });
    }

    /**
     * Mengupdate unit dan mencatat riwayat perubahan status jika berubah.
     */
    public function updateUnit(Unit $unit, array $data, ?int $userId = null): Unit
    {
        return DB::transaction(function () use ($unit, $data, $userId) {
            $oldStatus = $unit->status;
            $newStatus = $data['status'] ?? $oldStatus;

            // Jika status berubah, catat riwayatnya
            if ($oldStatus !== $newStatus) {
                UnitStatusHistory::create([
                    'unit_id' => $unit->id,
                    'user_id' => $userId,
                    'from_status' => $oldStatus,
                    'to_status' => $newStatus,
                    'notes' => $data['status_notes'] ?? 'Status diubah melalui panel admin.',
                ]);
            }

            $unit->update($data);

            // Upload foto tambahan jika ada
            if (!empty($data['photos'])) {
                $currentCount = $unit->photos()->count();
                $this->uploadPhotos($unit, $data['photos'], null, $currentCount);
            }

            // Atur foto utama jika ditentukan
            if (!empty($data['primary_photo_id'])) {
                $this->setPrimaryPhoto($unit, (int) $data['primary_photo_id']);
            }

            return $unit->fresh(['photos', 'developer', 'statusHistories']);
        });
    }

    /**
     * Upload banyak foto ke storage dan simpan referensinya ke database.
     */
    public function uploadPhotos(Unit $unit, array $photos, ?int $primaryIndex = null, int $startIndex = 0): void
    {
        $hasExistingPrimary = $unit->photos()->where('is_primary', true)->exists();

        foreach ($photos as $index => $file) {
            if ($file instanceof UploadedFile) {
                $path = $file->store('units/' . $unit->id, 'public');
                $isPrimary = (!$hasExistingPrimary && $index === 0) || ($primaryIndex !== null && $index === $primaryIndex);

                UnitPhoto::create([
                    'unit_id' => $unit->id,
                    'photo_path' => $path,
                    'is_primary' => $isPrimary,
                    'sort_order' => $startIndex + $index,
                ]);

                if ($isPrimary) {
                    $hasExistingPrimary = true;
                }
            }
        }
    }

    /**
     * Menetapkan foto utama untuk unit.
     */
    public function setPrimaryPhoto(Unit $unit, int $photoId): void
    {
        $unit->photos()->update(['is_primary' => false]);
        $unit->photos()->where('id', $photoId)->update(['is_primary' => true]);
    }

    /**
     * Menghapus foto unit.
     */
    public function deletePhoto(UnitPhoto $photo): void
    {
        if (Storage::disk('public')->exists($photo->photo_path)) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        $wasPrimary = $photo->is_primary;
        $unitId = $photo->unit_id;
        $photo->delete();

        // Jika foto utama yang dihapus, jadikan foto pertama yang tersisa sebagai utama
        if ($wasPrimary) {
            $nextPhoto = UnitPhoto::where('unit_id', $unitId)->orderBy('sort_order', 'asc')->first();
            if ($nextPhoto) {
                $nextPhoto->update(['is_primary' => true]);
            }
        }
    }

    /**
     * Soft delete unit.
     */
    public function softDeleteUnit(Unit $unit): void
    {
        $unit->delete();
    }

    /**
     * Hapus permanen (force delete) unit dan foto fisiknya dari disk.
     */
    public function forceDeleteUnit(Unit $unit): void
    {
        foreach ($unit->photos as $photo) {
            if (Storage::disk('public')->exists($photo->photo_path)) {
                Storage::disk('public')->delete($photo->photo_path);
            }
        }

        $unit->forceDelete();
    }
}
