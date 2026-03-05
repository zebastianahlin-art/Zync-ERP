<?php

declare(strict_types=1);

namespace App\Integrations;

/**
 * IntegrationInterface — Kontrakt för externa integrations-adaptrar.
 */
interface IntegrationInterface
{
    /** Returnera integrationens namn. */
    public function getName(): string;

    /** Returnera true om integrationen är konfigurerad med nödvändiga inställningar. */
    public function isConfigured(): bool;

    /** Testa anslutningen och returnera true om den fungerar. */
    public function testConnection(): bool;
}
