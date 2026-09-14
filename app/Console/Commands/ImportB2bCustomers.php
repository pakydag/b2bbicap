<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CustomerImportService;

class ImportB2bCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'b2b:import-customers {file : Percorso del file da importare (.xlsx o .csv)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa o aggiorna l\'anagrafica clienti B2B con codice cliente e ragione sociale da file Excel (.xlsx) o CSV';

    /**
     * Execute the console command.
     */
    public function handle(CustomerImportService $importer): int
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File non trovato: {$filePath}");
            return 1;
        }

        $this->info("Inizio importazione clienti da: {$filePath} ...");

        $result = $importer->import($filePath);

        $this->info("Importazione completata!");
        $this->table(
            ['Totale Elaborati', 'Clienti Creati', 'Clienti Aggiornati', 'Errori'],
            [[$result['total'], $result['created'], $result['updated'], count($result['errors'])]]
        );

        if (!empty($result['errors'])) {
            $this->warn("Alcune righe hanno generato avvisi:");
            foreach (array_slice($result['errors'], 0, 10) as $err) {
                $this->line("- {$err}");
            }
            if (count($result['errors']) > 10) {
                $this->line("... e altri " . (count($result['errors']) - 10) . " errori.");
            }
        }

        return 0;
    }
}
