<?php

declare(strict_types=1);

namespace App\Integrations;

/**
 * OpenBankingAdapter — Stub-adapter för Open Banking / PSD2-integration.
 *
 * Implementera fetchTransactions() och matchPayments() mot en
 * bankens Open Banking API (t.ex. via Tink, Aiia eller direkt PSD2 API).
 */
class OpenBankingAdapter implements IntegrationInterface
{
    private string $clientId;
    private string $clientSecret;
    private string $apiUrl;

    public function __construct(string $clientId = '', string $clientSecret = '', string $apiUrl = '')
    {
        $this->clientId     = $clientId ?: ($_ENV['OPEN_BANKING_CLIENT_ID'] ?? '');
        $this->clientSecret = $clientSecret ?: ($_ENV['OPEN_BANKING_CLIENT_SECRET'] ?? '');
        $this->apiUrl       = $apiUrl ?: ($_ENV['OPEN_BANKING_API_URL'] ?? '');
    }

    public function getName(): string
    {
        return 'Open Banking';
    }

    public function isConfigured(): bool
    {
        return $this->clientId !== '' && $this->clientSecret !== '' && $this->apiUrl !== '';
    }

    public function testConnection(): bool
    {
        $this->log('testConnection', ['api_url' => $this->apiUrl]);
        // TODO: Implementera token-hämtning och anslutningstest
        return $this->isConfigured();
    }

    /**
     * Hämta banktransaktioner för en given period.
     *
     * @param string $fromDate  Format: YYYY-MM-DD
     * @param string $toDate    Format: YYYY-MM-DD
     * @return array<int, array<string, mixed>> Lista med transaktioner
     */
    public function fetchTransactions(string $fromDate, string $toDate): array
    {
        $this->log('fetchTransactions', ['from' => $fromDate, 'to' => $toDate]);
        // TODO: Implementera OAuth2-flöde och API-anrop
        return [];
    }

    /**
     * Matcha betalningar mot obetalda fakturor.
     *
     * @param array<int, array<string, mixed>> $transactions Transaktioner från fetchTransactions()
     * @return array<int, array<string, mixed>> Matchade faktura-betalning-par
     */
    public function matchPayments(array $transactions): array
    {
        $this->log('matchPayments', ['transaction_count' => count($transactions)]);
        // TODO: Implementera matchningslogik baserat på belopp, OCR-referens, etc.
        return [];
    }

    /** @param array<string, mixed> $context */
    private function log(string $method, array $context): void
    {
        error_log('[OpenBankingAdapter::' . $method . '] ' . json_encode($context, JSON_UNESCAPED_UNICODE));
    }
}
