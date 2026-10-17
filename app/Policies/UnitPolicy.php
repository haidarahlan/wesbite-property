<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;

class UnitPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Unit $unit): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     * Aturan:
     * - Managing Director: Selalu bisa edit semua unit (termasuk Terjual dan Booking).
     * - Role lain (Superadmin & Admin): Hanya bisa edit jika unit berstatus 'Tersedia'.
     *   Unit yang 'Booking' dan 'Terjual' tidak dapat diedit.
     */
    public function update(User $user, Unit $unit): bool
    {
        if ($user->isManagingDirector()) {
            return true;
        }

        if (!$user->isAdmin()) {
            return false;
        }

        // Selain Managing Director, hanya unit berstatus 'Tersedia' yang dapat diedit
        return $unit->status === 'Tersedia';
    }

    /**
     * Determine whether the user can soft delete the model.
     * Aturan:
     * - Unit berstatus 'Terjual' HANYA bisa dihapus oleh Managing Director.
     * - Unit selain Terjual (Tersedia / Booking) bisa dihapus oleh Admin dan Superadmin.
     */
    public function delete(User $user, Unit $unit): bool
    {
        if ($unit->status === 'Terjual') {
            return $user->isManagingDirector();
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Unit $unit): bool
    {
        // Jika Terjual, hanya Managing Director yang boleh hapus permanen
        if ($unit->status === 'Terjual') {
            return $user->isManagingDirector();
        }

        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Unit $unit): bool
    {
        return $user->isSuperAdmin();
    }
}
