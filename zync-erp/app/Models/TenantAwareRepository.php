<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\TenantContext;

/**
 * TenantAwareRepository — Basklass för multi-tenant repositories.
 *
 * Filtrar automatiskt queries med tenant_id om en aktiv tenant
 * är satt i TenantContext. I single-tenant-läge (ingen aktiv tenant)
 * returneras data för alla tenants.
 *
 * Exempel:
 *   class MyRepository extends TenantAwareRepository {
 *       public function all(): array {
 *           [$where, $params] = $this->tenantWhere('t.tenant_id');
 *           $stmt = $this->pdo()->prepare("SELECT * FROM my_table t $where");
 *           $stmt->execute($params);
 *           return $stmt->fetchAll(\PDO::FETCH_ASSOC);
 *       }
 *   }
 */
abstract class TenantAwareRepository
{
    /**
     * Hämta PDO-instansen.
     */
    protected function pdo(): \PDO
    {
        return Database::pdo();
    }

    /**
     * Returnera aktiv tenant_id eller null (single-tenant-läge).
     */
    protected function currentTenantId(): ?int
    {
        $tenantId = TenantContext::getInstance()->get('id');
        return $tenantId !== null ? (int) $tenantId : null;
    }

    /**
     * Bygg WHERE-klausul för tenant-filtrering.
     *
     * @param  string $column  Kolumnnamn inklusive tabellalias, t.ex. 't.tenant_id'
     * @param  array  $extra   Extra WHERE-villkor (utan 'WHERE' keyword)
     * @param  array  $params  Extra parametrar (matchande $extra)
     * @return array{0: string, 1: array<mixed>}  [WHERE-klausul, parametrar]
     */
    protected function tenantWhere(string $column = 'tenant_id', array $extra = [], array $params = []): array
    {
        $conditions = $extra;
        $allParams  = $params;

        $tenantId = $this->currentTenantId();
        if ($tenantId !== null) {
            $conditions[] = "{$column} = ?";
            $allParams[]  = $tenantId;
        }

        $whereClause = !empty($conditions)
            ? 'WHERE ' . implode(' AND ', $conditions)
            : '';

        return [$whereClause, $allParams];
    }

    /**
     * Hämta tenant_id för insert-operationer.
     * Kastar undantag om ingen tenant är aktiv och tenantId krävs.
     *
     * @throws \RuntimeException
     */
    protected function requireTenantId(): int
    {
        $tenantId = $this->currentTenantId();
        if ($tenantId === null) {
            throw new \RuntimeException('Ingen aktiv tenant — kan inte utföra tenant-specifik operation.');
        }
        return $tenantId;
    }

    /**
     * Hjälpmetod: returnera tenant_id för insert (null = single-tenant, välj explicit).
     */
    protected function tenantIdForInsert(?int $explicitTenantId = null): ?int
    {
        if ($explicitTenantId !== null) {
            return $explicitTenantId;
        }
        return $this->currentTenantId();
    }
}
