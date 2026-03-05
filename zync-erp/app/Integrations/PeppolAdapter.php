<?php

declare(strict_types=1);

namespace App\Integrations;

/**
 * PeppolAdapter — Stub-adapter för Peppol e-fakturanätverket.
 *
 * Implementera sendInvoice() och receiveInvoice() med ett riktigt
 * Peppol-bibliotek (t.ex. via OpenPeppol-kompatibelt API).
 */
class PeppolAdapter implements IntegrationInterface
{
    private string $accessPointUrl;
    private string $apiKey;

    public function __construct(string $accessPointUrl = '', string $apiKey = '')
    {
        $this->accessPointUrl = $accessPointUrl ?: ($_ENV['PEPPOL_AP_URL'] ?? '');
        $this->apiKey         = $apiKey ?: ($_ENV['PEPPOL_API_KEY'] ?? '');
    }

    public function getName(): string
    {
        return 'Peppol';
    }

    public function isConfigured(): bool
    {
        return $this->accessPointUrl !== '' && $this->apiKey !== '';
    }

    public function testConnection(): bool
    {
        $this->log('testConnection', ['url' => $this->accessPointUrl]);
        // TODO: Implementera riktig HTTP-anslutningstest mot Peppol AP
        return $this->isConfigured();
    }

    /**
     * Skicka en utgående faktura via Peppol-nätverket.
     *
     * @param array<string, mixed> $invoiceData UBL-kompatibel fakturastruktur
     * @return array<string, mixed> Svar med transmissionId
     */
    public function sendInvoice(array $invoiceData): array
    {
        $this->log('sendInvoice', ['invoice_number' => $invoiceData['invoice_number'] ?? 'N/A']);
        // TODO: Implementera UBL-generering och POST till Peppol AP
        return [
            'status'        => 'stub',
            'transmissionId' => 'STUB-' . uniqid(),
            'message'       => 'Peppol sendInvoice är en stub-implementation.',
        ];
    }

    /**
     * Hämta inkommande fakturor via Peppol-nätverket.
     *
     * @return array<int, array<string, mixed>>
     */
    public function receiveInvoice(): array
    {
        $this->log('receiveInvoice', []);
        // TODO: Implementera hämtning av inkommande UBL-fakturor
        return [];
    }

    /** @param array<string, mixed> $context */
    private function log(string $method, array $context): void
    {
        error_log('[PeppolAdapter::' . $method . '] ' . json_encode($context, JSON_UNESCAPED_UNICODE));
    }
}
