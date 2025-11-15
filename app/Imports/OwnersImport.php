<?php

namespace App\Imports;

use App\Models\Lot;
use App\Models\Owner;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OwnersImport implements ToCollection, WithHeadingRow // Ya no implementa WithMultipleSheets
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Convertir los encabezados a minúsculas y sin espacios
            $rowData = collect($row)->mapWithKeys(function ($value, $key) {
                // Se utiliza una estrategia más simple para acceder a las claves.
                // Asumimos que WithHeadingRow las convierte a minúsculas y sustituye espacios por guiones bajos.
                // Si no es el caso, la siguiente línea lo forzará.
                $key = strtolower(str_replace(' ', '_', $key));
                return [$key => $value];
            });

            // Los nombres de las columnas en minúsculas son: socio, mz, lote
            $ownerName = $rowData['socio'] ?? null;
            $blockNumber = $rowData['mz'] ?? null;
            $lotNumber = $rowData['lote'] ?? null;

            if (empty($ownerName) || empty($blockNumber) || empty($lotNumber)) {
                continue;
            }

            // Crear o encontrar el socio
            $owner = Owner::firstOrCreate(['name' => trim($ownerName)]);

            // Actualizar el lote correspondiente
            Lot::where('block_number', trim($blockNumber))
               ->where('lot_number', trim($lotNumber))
               ->update(['owner_id' => $owner->id]);
        }
    }
}