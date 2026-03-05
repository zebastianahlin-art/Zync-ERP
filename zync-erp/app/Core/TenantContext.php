<?php

declare(strict_types=1);

namespace App\Core;

/**
 * TenantContext — Singleton som håller aktiv tenant-information.
 *
 * Används av TenantMiddleware för multi-tenant SaaS-stöd.
 */
class TenantContext
{
    private static ?self $instance = null;

    /** @var array<string, mixed>|null */
    private ?array $tenant = null;

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Sätt aktiv tenant.
     *
     * @param array<string, mixed> $tenant
     */
    public function setTenant(array $tenant): void
    {
        $this->tenant = $tenant;
    }

    /**
     * Rensa aktiv tenant (används i tester).
     */
    public function clearTenant(): void
    {
        $this->tenant = null;
    }

    /**
     * Returnera true om en tenant är aktiv.
     */
    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }

    /**
     * Hämta ett specifikt fält från tenant-datan.
     */
    public function get(string $key): mixed
    {
        return $this->tenant[$key] ?? null;
    }

    /**
     * Returnera hela tenant-arrayen.
     *
     * @return array<string, mixed>|null
     */
    public function getTenant(): ?array
    {
        return $this->tenant;
    }

    /**
     * Kontrollera om en specifik modul är aktiverad för denna tenant.
     */
    public function isModuleEnabled(string $moduleSlug): bool
    {
        if ($this->tenant === null) {
            return true; // Ingen tenant = alla moduler tillåtna (single-tenant-läge)
        }

        $modules = $this->tenant['modules'] ?? [];

        if (!is_array($modules)) {
            return false;
        }

        foreach ($modules as $module) {
            if (is_array($module)) {
                if (($module['module_slug'] ?? '') === $moduleSlug && ($module['is_active'] ?? 0)) {
                    return true;
                }
            } elseif (is_string($module) && $module === $moduleSlug) {
                return true;
            }
        }

        return false;
    }
}
