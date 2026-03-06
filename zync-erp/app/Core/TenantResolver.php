<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use RuntimeException;

final class TenantResolver
{
    public static function resolve(): void
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';

        if ($host === '') {
            throw new RuntimeException('HTTP_HOST saknas. Kan inte avgöra tenant.');
        }

        $host = strtolower(trim(explode(':', $host)[0]));

        $db = Database::connection();

        $stmt = $db->prepare("
            SELECT id, name, slug, domain, is_active
            FROM tenants
            WHERE domain = :domain
            LIMIT 1
        ");

        $stmt->execute([
            ':domain' => $host,
        ]);

        $tenant = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tenant) {
            throw new RuntimeException('Ingen tenant hittades för domänen: ' . $host);
        }

        if ((int) ($tenant['is_active'] ?? 0) !== 1) {
            throw new RuntimeException('Tenant är inaktiv: ' . $host);
        }

        Tenant::set($tenant);
    }
}