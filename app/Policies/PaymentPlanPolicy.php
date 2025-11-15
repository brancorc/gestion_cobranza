<?php

namespace App\Policies;

use App\Models\PaymentPlan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PaymentPlanPolicy
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
    public function view(User $user, PaymentPlan $paymentPlan): bool
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
    public function update(User $user, PaymentPlan $paymentPlan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PaymentPlan $plan): bool
    {
        // Si el usuario es admin, puede eliminar.
        if ($user->isAdmin()) {
            return true;
        }

        // Si es un usuario normal, solo si no hay transacciones.
        return !$plan->installments()->whereHas('transactions')->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PaymentPlan $paymentPlan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PaymentPlan $paymentPlan): bool
    {
        return $user->isAdmin();
    }
}
