<?php

namespace App\Console\Commands;

use App\Models\Cars;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupCarImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cars:cleanup-images {--dry-run : Solo simular la eliminación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina imágenes de coches que no están en la base de datos.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $files = Storage::disk('public')->files('cars');
        $dbImages = Cars::whereNotNull('image')->pluck('image')->toArray();

        $count = 0;
        $size = 0;

        $this->info("Analizando " . count($files) . " imágenes...");

        foreach ($files as $file) {
            if (!in_array($file, $dbImages)) {
                $fileSize = Storage::disk('public')->size($file);

                if ($dryRun) {
                    $this->line("[DRY RUN] Se eliminaría: {$file} (" . number_format($fileSize / 1024, 2) . " KB)");
                } else {
                    Storage::disk('public')->delete($file);
                    $this->warn("Eliminado: {$file}");
                }

                $count++;
                $size += $fileSize;
            }
        }

        if ($count === 0) {
            $this->info("No se encontraron imágenes huérfanas.");
        } else {
            $msg = $dryRun ? "Se eliminarían" : "Se eliminaron";
            $this->info("{$msg} {$count} imágenes. Espacio liberado: " . number_format($size / 1024 / 1024, 2) . " MB.");
        }
    }
}
