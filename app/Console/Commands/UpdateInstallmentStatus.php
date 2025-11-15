<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Installment;
use App\Models\Lot; // <-- Importar el modelo Lot
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateInstallmentStatus extends Command
{
    protected $signature = 'installments:update-status';
    protected $description = 'Actualiza cuotas a "vencida", calcula intereses y liquida lotes pagados.';
    private const MONTHLY_INTEREST_RATE = 0.05;

    public function handle()
    {
        Log::info('Iniciando tarea programada: UpdateInstallmentStatus.');
        $this->info('Iniciando actualización de estado de cuotas y lotes...');

        DB::beginTransaction();
        try {
            // --- FASE 1: Actualizar Cuotas Vencidas y Aplicar Intereses ---
            $this->updateOverdueInstallments();

            // --- FASE 2: Liquidar Lotes Completamente Pagados ---
            $this->liquidatePaidLots();

            DB::commit();
            $this->info('Proceso completado exitosamente.');
            Log::info('Tarea programada: UpdateInstallmentStatus finalizada con éxito.');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error durante la actualización: ' . $e->getMessage());
            Log::error('Error en UpdateInstallmentStatus', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return Command::FAILURE;
        }
    }

    private function updateOverdueInstallments()
    {
        $today = Carbon::today();
        $updatedCount = 0;
        $interestAppliedCount = 0;

        $installmentsToUpdate = Installment::where('status', 'pendiente')
            ->where('due_date', '<', $today)
            ->with('transactions')
            ->get();

        foreach ($installmentsToUpdate as $installment) {
            $installment->status = 'vencida';
            
            $totalPaid = $installment->transactions->sum('pivot.amount_applied');
            $capitalBalance = ($installment->amount ?? $installment->base_amount) - $totalPaid;

            if ($capitalBalance > 0) {
                $interest = $capitalBalance * self::MONTHLY_INTEREST_RATE;
                $installment->interest_amount += round($interest, 2); // Usar += para acumular si es necesario
                $interestAppliedCount++;
            }
            
            $installment->save();
            $updatedCount++;
        }

        $this->info("Cuotas actualizadas a 'vencida': {$updatedCount}");
        $this->info("Cuotas con interés aplicado/recalculado: {$interestAppliedCount}");
    }

    private function liquidatePaidLots()
    {
        $liquidatedLots = 0;
        
        // Obtener lotes 'vendidos' que ya no tienen cuotas pendientes o vencidas.
        $potentialLotsToLiquidate = Lot::where('status', 'vendido')
            ->whereDoesntHave('paymentPlans.installments', function ($query) {
                $query->whereIn('status', ['pendiente', 'vencida']);
            })
            ->has('paymentPlans')
            ->with('paymentPlans.installments.transactions') // Precargar para el cálculo final
            ->get();

        foreach ($potentialLotsToLiquidate as $lot) {
            $totalDebtForLot = 0;
            foreach ($lot->paymentPlans as $plan) {
                foreach ($plan->installments as $installment) {
                    $totalDue = ($installment->amount ?? $installment->base_amount) + $installment->interest_amount;
                    $totalPaid = $installment->transactions->sum('pivot.amount_applied');
                    $remaining = $totalDue - $totalPaid;
                    if ($remaining > 0.01) {
                        $totalDebtForLot += $remaining;
                    }
                }
            }

            // Si la deuda real es cero (o insignificante), se liquida el lote.
            if ($totalDebtForLot <= 0.01) {
                $lot->update(['status' => 'liquidado']);
                $liquidatedLots++;
            }
        }
        
        $this->info("Lotes liquidados en esta ejecución: {$liquidatedLots}");
    }
}