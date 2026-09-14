<?php

namespace App\Services;

use App\Models\B2bCustomer;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class CustomerImportService
{
    /**
     * Import customers from a file (.xlsx, .csv, .txt).
     *
     * @param string $filePath
     * @return array
     */
    public function import(string $filePath): array
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($ext === 'xlsx') {
            $rows = $this->parseXlsx($filePath);
        } else {
            $rows = $this->parseCsv($filePath);
        }

        if (empty($rows)) {
            return [
                'total' => 0,
                'created' => 0,
                'updated' => 0,
                'errors' => ['Il file è vuoto o non è stato possibile leggere alcuna riga valida.']
            ];
        }

        return $this->processRows($rows);
    }

    /**
     * Process parsed matrix of rows.
     */
    protected function processRows(array $rows): array
    {
        $header = array_shift($rows);
        $headerLower = array_map(function ($h) {
            return strtolower(trim((string)$h));
        }, $header);

        // Identifica gli indici delle colonne
        $codeCol = -1;
        $nameCol = -1;
        $vatCol = -1;

        foreach ($headerLower as $idx => $name) {
            if (in_array($name, ['cf_codicecf', 'codicecf', 'codice_cliente', 'codice', 'code', 'id_cliente'])) {
                $codeCol = $idx;
            } elseif (in_array($name, ['cf_nome', 'nome', 'ragione_sociale', 'ragionesociale', 'business_name', 'cliente', 'azienda'])) {
                $nameCol = $idx;
            } elseif (in_array($name, ['piva', 'vat', 'partita_iva', 'vat_number', 'cf_piva'])) {
                $vatCol = $idx;
            }
        }

        // Se non trovati via header esatto, prova con euristica posizionale (es. formato Bicap standard: Tipo, Codice, Nome)
        if ($codeCol === -1 || $nameCol === -1) {
            if (count($header) >= 3 && str_contains(strtolower($header[0]), 'tipo')) {
                $codeCol = 1;
                $nameCol = 2;
            } elseif (count($header) >= 2) {
                // Presumi colonna 0 = codice, colonna 1 = nome
                $codeCol = 0;
                $nameCol = 1;
            }
        }

        if ($codeCol === -1 || $nameCol === -1) {
            return [
                'total' => count($rows),
                'created' => 0,
                'updated' => 0,
                'errors' => ['Impossibile identificare le colonne del Codice Cliente (CF_CodiceCF) e Ragione Sociale (CF_Nome).']
            ];
        }

        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($rows as $rowIndex => $row) {
            $code = isset($row[$codeCol]) ? trim((string)$row[$codeCol]) : '';
            $name = isset($row[$nameCol]) ? trim((string)$row[$nameCol]) : '';
            $vat = ($vatCol !== -1 && isset($row[$vatCol])) ? trim((string)$row[$vatCol]) : null;

            if (empty($code) && empty($name)) {
                continue;
            }

            try {
                // 1. Cerca per codice cliente esistente
                $customer = null;
                if (!empty($code)) {
                    $customer = B2bCustomer::where('code', $code)->first();
                }

                // 2. Se non trovato per codice, cerca per ragione sociale esatta
                if (!$customer && !empty($name)) {
                    $customer = B2bCustomer::whereRaw('LOWER(TRIM(business_name)) = ?', [strtolower($name)])->first();
                }

                // 3. Se presente P.IVA, prova per P.IVA
                if (!$customer && !empty($vat)) {
                    $customer = B2bCustomer::where('vat_number', $vat)->first();
                }

                if ($customer) {
                    $customer->update([
                        'code' => $code ?: $customer->code,
                        'business_name' => $name ?: $customer->business_name,
                        'vat_number' => $vat ?: $customer->vat_number,
                    ]);
                    $updated++;
                } else {
                    B2bCustomer::create([
                        'code' => $code,
                        'business_name' => $name ?: "Cliente {$code}",
                        'vat_number' => $vat,
                    ]);
                    $created++;
                }
            } catch (\Throwable $e) {
                $errors[] = "Riga " . ($rowIndex + 2) . ": " . $e->getMessage();
                Log::error("[CustomerImportService] Errore riga " . ($rowIndex + 2) . ": " . $e->getMessage());
            }
        }

        return [
            'total' => $created + $updated,
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors
        ];
    }

    /**
     * Parse CSV / TXT files.
     */
    protected function parseCsv(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) return [];

        $firstLine = fgets($handle);
        rewind($handle);

        // Rileva delimitatore
        $delimiters = [';', ',', "\t", '|'];
        $bestDelim = ';';
        $maxCount = 0;
        foreach ($delimiters as $d) {
            $count = substr_count($firstLine, $d);
            if ($count > $maxCount) {
                $maxCount = $count;
                $bestDelim = $d;
            }
        }

        $rows = [];
        while (($row = fgetcsv($handle, 0, $bestDelim, '"', "\\")) !== false) {
            // Rimuovi eventuale BOM UTF-8 dalla prima cella
            if (empty($rows) && isset($row[0])) {
                $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
            }
            $rows[] = $row;
        }
        fclose($handle);

        return $rows;
    }

    /**
     * Parse XLSX using ZipArchive and XML parsing.
     */
    protected function parseXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        // 1. Estrai sharedStrings
        $sharedStrings = [];
        $stringsXmlContent = $zip->getFromName('xl/sharedStrings.xml');
        if ($stringsXmlContent !== false) {
            $xml = @simplexml_load_string($stringsXmlContent);
            if ($xml) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string)$si->t;
                    } else {
                        $fullText = '';
                        foreach ($si->r as $r) {
                            $fullText .= (string)$r->t;
                        }
                        $sharedStrings[] = $fullText;
                    }
                }
            }
        }

        // 2. Estrai sheet1.xml
        $sheetXmlContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXmlContent === false) {
            return [];
        }

        $sheetXml = @simplexml_load_string($sheetXmlContent);
        if (!$sheetXml || !isset($sheetXml->sheetData)) {
            return [];
        }

        $rows = [];
        foreach ($sheetXml->sheetData->row as $rowNode) {
            $rowData = [];
            foreach ($rowNode->c as $cell) {
                $ref = (string)$cell['r'];
                preg_match('/^([A-Z]+)(\d+)$/', $ref, $m);
                $colLetters = $m[1] ?? 'A';
                $colIdx = $this->columnLettersToIndex($colLetters);

                $valType = (string)$cell['t'];
                $val = (string)$cell->v;

                if ($valType === 's') {
                    $stringIdx = (int)$val;
                    $cellVal = $sharedStrings[$stringIdx] ?? '';
                } elseif ($valType === 'inlineStr') {
                    $cellVal = (string)($cell->is->t ?? '');
                } else {
                    $cellVal = $val;
                }

                $rowData[$colIdx] = trim($cellVal);
            }

            // Normalizza array con chiavi numeriche sequenziali
            if (!empty($rowData)) {
                $maxKey = max(array_keys($rowData));
                $normalizedRow = [];
                for ($k = 0; $k <= $maxKey; $k++) {
                    $normalizedRow[$k] = $rowData[$k] ?? '';
                }
                $rows[] = $normalizedRow;
            }
        }

        return $rows;
    }

    /**
     * Convert Excel column letters (A, B, ..., Z, AA, AB) to 0-based index.
     */
    protected function columnLettersToIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $len = strlen($letters);
        $idx = 0;
        for ($i = 0; $i < $len; $i++) {
            $idx = $idx * 26 + (ord($letters[$i]) - ord('A') + 1);
        }
        return $idx - 1;
    }

    /**
     * Download Clienti_Bicap.xlsx from FTPS and sync database records.
     */
    public function syncFromFtps(): array
    {
        $host = env('FTPS_GIACENZE_HOST', '51.75.145.169');
        $username = env('FTPS_GIACENZE_USERNAME', 'bicapb2b');
        $password = env('FTPS_GIACENZE_PASSWORD', 'lB24RiL=^D');

        $candidates = ['Output/Clienti_Bicap.xlsx', 'Output/Clienti.xlsx', 'Output/Clienti_Bicap.csv', 'Output/Clienti.csv'];

        $conn = @ftp_ssl_connect($host, 21, 15);
        if (!$conn) {
            $conn = @ftp_connect($host, 21, 15);
        }

        if (!$conn) {
            $msg = "Impossibile connettersi al server FTPS {$host} per il file clienti.";
            Log::error("[CustomerImportService] {$msg}");
            return ['success' => false, 'message' => $msg];
        }

        $login = @ftp_login($conn, $username, $password);
        if (!$login) {
            $msg = "Autenticazione FTP fallita per utente {$username}";
            Log::error("[CustomerImportService] {$msg}");
            @ftp_close($conn);
            return ['success' => false, 'message' => $msg];
        }

        @ftp_pasv($conn, true);

        $foundRemote = null;
        $files = @ftp_nlist($conn, 'Output') ?: [];
        foreach ($candidates as $cand) {
            foreach ($files as $f) {
                if (strtolower(basename($f)) === strtolower(basename($cand))) {
                    $foundRemote = 'Output/' . basename($f);
                    break 2;
                }
            }
        }

        if (!$foundRemote) {
            $foundRemote = 'Output/Clienti_Bicap.xlsx';
        }

        $ext = pathinfo($foundRemote, PATHINFO_EXTENSION) ?: 'xlsx';
        $tempPath = sys_get_temp_dir() . '/Clienti_temp_' . uniqid() . '.' . $ext;

        $downloadOk = @ftp_get($conn, $tempPath, $foundRemote, FTP_BINARY);
        @ftp_close($conn);

        if (!$downloadOk || !file_exists($tempPath) || filesize($tempPath) === 0) {
            $msg = "File clienti non trovato o download non riuscito da FTPS ({$foundRemote}).";
            Log::warning("[CustomerImportService] {$msg}");
            if (file_exists($tempPath)) @unlink($tempPath);
            return ['success' => false, 'message' => $msg];
        }

        $result = $this->import($tempPath);
        @unlink($tempPath);

        $msg = "Clienti sincronizzati da FTPS: {$result['total']} elaborati ({$result['created']} creati, {$result['updated']} aggiornati con codice gestionale).";
        Log::info("[CustomerImportService] {$msg}");

        return ['success' => true, 'message' => $msg, 'result' => $result];
    }
}
