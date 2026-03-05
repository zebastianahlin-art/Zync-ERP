<?php

declare(strict_types=1);

namespace App\Integrations;

/**
 * ImapAdapter — Stub-adapter för IMAP e-posthämtning.
 *
 * Implementera fetchEmails() och processInvoiceEmail() med ett riktigt
 * IMAP-bibliotek (t.ex. PHP imap-extension eller laminas-mail).
 */
class ImapAdapter implements IntegrationInterface
{
    private string $host;
    private string $username;
    private string $password;

    public function __construct(string $host = '', string $username = '', string $password = '')
    {
        $this->host     = $host ?: ($_ENV['IMAP_HOST'] ?? '');
        $this->username = $username ?: ($_ENV['IMAP_USERNAME'] ?? '');
        $this->password = $password ?: ($_ENV['IMAP_PASSWORD'] ?? '');
    }

    public function getName(): string
    {
        return 'IMAP E-post';
    }

    public function isConfigured(): bool
    {
        return $this->host !== '' && $this->username !== '' && $this->password !== '';
    }

    public function testConnection(): bool
    {
        $this->log('testConnection', ['host' => $this->host, 'user' => $this->username]);
        // TODO: Implementera riktig IMAP-anslutningstest
        return $this->isConfigured();
    }

    /**
     * Hämta olästa e-postmeddelanden från inkorg.
     *
     * @return array<int, array<string, mixed>> Lista med e-postmeddelanden
     */
    public function fetchEmails(): array
    {
        $this->log('fetchEmails', []);
        // TODO: Implementera IMAP-anslutning och hämtning av olästa meddelanden
        return [];
    }

    /**
     * Bearbeta ett e-postmeddelande och extrahera fakturainformation.
     *
     * @param array<string, mixed> $email E-postdata från fetchEmails()
     * @return array<string, mixed>|null Extraherad fakturastruktur, eller null om ej en faktura
     */
    public function processInvoiceEmail(array $email): ?array
    {
        $this->log('processInvoiceEmail', ['subject' => $email['subject'] ?? 'N/A']);
        // TODO: Implementera fakturaigenkänning (OCR, XML-parsing, etc.)
        return null;
    }

    /** @param array<string, mixed> $context */
    private function log(string $method, array $context): void
    {
        error_log('[ImapAdapter::' . $method . '] ' . json_encode($context, JSON_UNESCAPED_UNICODE));
    }
}
