<?php

namespace App\Policies;

use App\Models\Lot;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LotPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lot $lot): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lot $lot): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lot $lot): bool
    {
        // Si el usuario es admin, puede eliminar cualquier cosa.
        if ($user->isAdmin()) {
            return true;
        }

        // Si es un usuario normal, solo puede eliminar si no tiene planes de pago.
        return !$lot->paymentPlans()->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Lot $lot): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Lot $lot): bool
    {
        return $user->isAdmin();
    }
}
