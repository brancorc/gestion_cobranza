<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MasterDataImport; // <-- CAMBIADO

class MigrateExcelData extends Command
{
    protected $signature = 'migrate:excel {filename?}'; // Acepta un nombre de archivo opcional
    protected $description = 'Migra datos desde un archivo de Excel estandarizado en storage/app/migrations';

    public function handle()
    {
        $filename = $this->argument('filename');

        // Si no se especifica un nombre, se busca el primer archivo en la carpeta
        if (!$filename) {
            $files = \Illuminate\Support\Facades\File::files(storage_path('app/migrations'));
            if (empty($files)) {
                $this->error('No se encontraron archivos en storage/app/migrations.');
                return 1;
            }
            $filename = $files[0]->getFilename();
        }

        $filePath = storage_path('app/migrations/' . $filename);

        if (!file_exists($filePath)) {
            $this->error("El archivo '{$filename}' no existe en la carpeta storage/app/migrations.");
            return 1;
        }

        $this->info("Iniciando la migración de datos desde '{$filename}'...");

        try {
            Excel::import(new MasterDataImport(), $filePath);
            $this->info("¡Migración completada exitosamente!");
        } catch (\Exception $e) {
            $this->error("Ocurrió un error durante la migración: " . $e->getMessage());
            $this->error("Archivo: " . $e->getFile() . " - Línea: " . $e->getLine());
            return 1;
        }
        
        return 0;
    }
}