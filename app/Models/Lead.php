<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_code',
        'name',
        'phone',
        'unit_id',
        'source',
        'status',
        'assigned_to',
        'notes',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class)->latest('survey_date');
    }

    public function chatProofs(): HasMany
    {
        return $this->hasMany(LeadChatProof::class)->latest();
    }

    protected static function booted(): void
    {
        static::saved(function (Lead $lead) {
            // Jika ada perubahan pada unit_id atau status
            if ($lead->wasChanged('status') || $lead->wasChanged('unit_id')) {
                $originalUnitId = $lead->getOriginal('unit_id');
                if ($originalUnitId && $originalUnitId != $lead->unit_id) {
                    static::recalculateUnitStatus($originalUnitId);
                }
            }

            if ($lead->unit_id) {
                static::recalculateUnitStatus($lead->unit_id);
            }
        });

        static::deleted(function (Lead $lead) {
            if ($lead->unit_id) {
                static::recalculateUnitStatus($lead->unit_id);
            }
        });

        static::restored(function (Lead $lead) {
            if ($lead->unit_id) {
                static::recalculateUnitStatus($lead->unit_id);
            }
        });
    }

    /**
     * Hitung ulang status unit berdasarkan status prospek/leads terkait:
     * - Closing -> Terjual
     * - Tertarik, Follow-up, Survei Terjadwal, Survei Selesai -> Booking
     * - Baru, Tidak Tertarik, Batal (atau jika tidak ada leads aktif) -> Tersedia
     */
    public static function recalculateUnitStatus(int $unitId): void
    {
        $unit = Unit::find($unitId);
        if (!$unit) {
            return;
        }

        // Ambil semua leads aktif untuk unit ini
        $leads = static::where('unit_id', $unitId)->get();

        $targetStatus = 'Tersedia';
        $note = 'Otomatis diubah menjadi Tersedia (tidak ada prospek booking/closing aktif).';

        // 1. Prioritas tertinggi: jika ada lead yang Closing -> Terjual
        $closingLead = $leads->firstWhere('status', 'Closing');
        if ($closingLead) {
            $targetStatus = 'Terjual';
            $note = "Otomatis diubah menjadi Terjual melalui prospek Closing {$closingLead->reference_code} ({$closingLead->name}).";
        } else {
            // 2. Prioritas kedua: jika ada lead dengan status Tertarik, Follow-up, Survei Terjadwal, Survei Selesai -> Booking
            $bookingLead = $leads->first(function ($ld) {
                return in_array($ld->status, ['Tertarik', 'Follow-up', 'Survei Terjadwal', 'Survei Selesai']);
            });

            if ($bookingLead) {
                $targetStatus = 'Booking';
                $note = "Otomatis diubah menjadi Booking melalui prospek {$bookingLead->status} {$bookingLead->reference_code} ({$bookingLead->name}).";
            }
        }

        if ($unit->status !== $targetStatus) {
            $oldStatus = $unit->status;
            $unit->update(['status' => $targetStatus]);

            UnitStatusHistory::create([
                'unit_id' => $unit->id,
                'user_id' => auth()->id(),
                'from_status' => $oldStatus,
                'to_status' => $targetStatus,
                'notes' => $note,
            ]);
        }
    }
}
