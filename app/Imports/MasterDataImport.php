<?php

namespace App\Imports;

use App\Models\Client;
use App\Models\Lot;
use App\Models\Owner;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PaymentPlanController;
use Carbon\Carbon;

class MasterDataImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $serviceTerreno = Service::firstOrCreate(['name' => 'Terreno']);
        $adminUser = User::first();

        if (!$adminUser) {
            throw new \Exception("No se encontró un usuario administrador. Ejecuta el UserSeeder.");
        }

        foreach ($rows as $row) {
            $clientName = trim($this->cleanValue($row['nombre']));
            if (empty($clientName)) {
                $client = null;
            } else {
                $client = Client::firstOrCreate(['name' => $clientName], ['phone' => $this->cleanValue($row['telefono'])]);
            }

            if (empty($row['mz']) || empty($row['lote'])) {
                continue;
            }

            DB::transaction(function () use ($row, $client, $serviceTerreno, $adminUser) {
                // 1. Crear o encontrar Lote (sin socio por ahora, se asignará en Fase 2)
                $lot = Lot::firstOrCreate(
                    ['block_number' => trim($row['mz']), 'lot_number' => trim($row['lote'])],
                    [
                        'client_id' => $client ? $client->id : null,
                        'total_price' => floatval($this->cleanValue($row['costo_de_lote'])),
                        'status' => $client ? 'vendido' : 'disponible',
                    ]
                );

                // 2. Crear Plan de Pago (si hay datos)
                $totalPagos = $this->cleanValue($row['total_de_pagos']);
                $fechaPrimera = $this->cleanValue($row['fecha_pago_primera']);
                
                if ($client && $totalPagos && $fechaPrimera) {
                    $paymentPlan = $lot->paymentPlans()->firstOrCreate(
                        ['service_id' => $serviceTerreno->id],
                        [
                            'total_amount' => floatval($this->cleanValue($row['costo_de_lote'])),
                            'number_of_installments' => intval($totalPagos),
                            'start_date' => Carbon::parse($fechaPrimera),
                        ]
                    );

                    if ($paymentPlan->wasRecentlyCreated) {
                        // Generar cuotas con la lógica de redondeo correcta
                        $paymentPlanController = new PaymentPlanController();
                        $paymentPlanController->generateInstallments($paymentPlan);
                        
                        // Reconstruir el historial de pagos para estas cuotas recién creadas
                        $installments = $paymentPlan->installments()->orderBy('installment_number')->get();
                        $this->reconstructPayments($row, $client, $adminUser, $installments);
                    }
                }
            });
        }
    }

    private function cleanValue($value)
    {
        if (is_string($value) && in_array(strtolower(trim($value)), ['none', 'nan', ''])) {
            return null;
        }
        return $value;
    }

    private function reconstructPayments($row, $client, $adminUser, $installments)
    {
        $lastPaymentDate = $this->cleanValue($row['fecha_pago_ultima']);
        if (!$lastPaymentDate) return;

        $lastPaymentDate = Carbon::parse($lastPaymentDate);

        foreach ($installments as $installment) {
            // Solo procesar cuotas que están dentro del rango de pagos históricos
            if ($installment->due_date->lessThanOrEqualTo($lastPaymentDate)) {
                
                // El monto a pagar es el valor real de la cuota (base + intereses si los hubiera)
                $amountToPay = $installment->base_amount + $installment->interest_amount;

                // Crear la transacción histórica
                $transaction = $client->transactions()->create([
                    'user_id' => $adminUser->id,
                    'amount_paid' => $amountToPay,
                    'payment_date' => $installment->due_date, // Usar la fecha de vencimiento como fecha de pago
                    'folio_number' => 'MIGRADO-' . uniqid(),
                    'notes' => 'Pago migrado desde Excel.',
                ]);

                // Asociar la transacción a la cuota y marcarla como pagada
                $transaction->installments()->attach($installment->id, ['amount_applied' => $amountToPay]);
                $installment->update(['status' => 'pagada']);
            }
        }
    }
}