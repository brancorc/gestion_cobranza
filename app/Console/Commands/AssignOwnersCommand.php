<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OwnersImport;

class AssignOwnersCommand extends Command
{
    protected $signature = 'owners:assign {filename}';
    protected $description = 'Asigna socios a los lotes existentes desde un archivo de Excel.';

    public function handle()
    {
        $filename = $this->argument('filename');
        $filePath = storage_path('app/migrations/' . $filename);

        if (!file_exists($filePath)) {
            $this->error("El archivo '{$filename}' no se encontró en storage/app/migrations.");
            return 1;
        }

        $this->info("Iniciando asignación de socios desde '{$filename}'...");

        try {
            Excel::import(new OwnersImport(), $filePath);
            $this->info("¡Asignación de socios completada!");
        } catch (\Exception $e) {
            $this->error("Ocurrió un error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}