<?php

declare(strict_types=1);

namespace App\Jobs;

/**
 * GeneratePdfJob — Genererar en PDF-fil asynkront (placeholder-implementation).
 *
 * Förväntad payload:
 *   type      string  Typ av dokument (t.ex. 'invoice', 'report')
 *   record_id int     Post-ID som dokumentet gäller
 *   output    string  (valfri) Sökväg där filen ska sparas
 */
class GeneratePdfJob
{
    private string $storageDir;

    public function __construct()
    {
        $this->storageDir = dirname(__DIR__, 2) . '/storage/pdfs';
    }

    public function handle(array $payload): void
    {
        $type     = preg_replace('/[^a-z0-9_\-]/', '', strtolower((string) ($payload['type'] ?? 'document')));
        $recordId = (int) ($payload['record_id'] ?? 0);
        $output   = (string) ($payload['output'] ?? '');

        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }

        $filename = $output !== ''
            ? $output
            : $this->storageDir . "/{$type}_{$recordId}_" . date('Ymd_His') . '.pdf';

        // Placeholder: skapar en tom PDF-fil (ersätt med ett riktigt PDF-bibliotek)
        $placeholder = "%PDF-1.4\n% Genererad av ZYNC ERP – {$type} #{$recordId}\n%%EOF\n";
        $result = file_put_contents($filename, $placeholder);

        if ($result === false) {
            throw new \RuntimeException("GeneratePdfJob: kunde inte skriva fil: {$filename}");
        }
    }
}
