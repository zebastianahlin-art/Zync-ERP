<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Database;
use App\Core\TenantContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * TenantMiddleware — Identifierar aktiv tenant och sätter TenantContext.
 *
 * Tenant identifieras via:
 * 1. HTTP-header X-Tenant-ID
 * 2. Subdomain (t.ex. foretag.zync-erp.se → subdomain = 'foretag')
 *
 * Om ingen tenant hittas fortsätter requesten utan tenant-kontext (single-tenant-läge).
 */
class TenantMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $tenantId = null;

        // 1. Explicit header (t.ex. från API-anrop)
        $headerValues = $request->getHeader('X-Tenant-ID');
        if (!empty($headerValues)) {
            $tenantId = (int) $headerValues[0];
        }

        // 2. Subdomain-identifiering
        if ($tenantId === null) {
            $host = $request->getUri()->getHost();
            $parts = explode('.', $host);

            // Förväntar format: {subdomain}.zync-erp.se (minst 3 delar)
            if (count($parts) >= 3) {
                $subdomain = $parts[0];
                if ($subdomain !== 'www') {
                    try {
                        $stmt = Database::pdo()->prepare(
                            "SELECT id FROM saas_tenants WHERE subdomain = ? AND status = 'active' LIMIT 1"
                        );
                        $stmt->execute([$subdomain]);
                        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
                        if ($row !== false) {
                            $tenantId = (int) $row['id'];
                        }
                    } catch (\Throwable) {
                        // Databas ej tillgänglig – fortsätt utan tenant
                    }
                }
            }
        }

        // Sätt tenant i context om hittad
        if ($tenantId !== null) {
            try {
                $stmt = Database::pdo()->prepare(
                    "SELECT t.*, GROUP_CONCAT(m.module_slug) AS module_slugs
                       FROM saas_tenants t
                  LEFT JOIN saas_tenant_modules m ON m.tenant_id = t.id AND m.is_active = 1
                      WHERE t.id = ?
                   GROUP BY t.id"
                );
                $stmt->execute([$tenantId]);
                $tenant = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($tenant !== false) {
                    // Konvertera module_slugs till array
                    $tenant['modules'] = $tenant['module_slugs']
                        ? array_map('trim', explode(',', (string) $tenant['module_slugs']))
                        : [];
                    unset($tenant['module_slugs']);

                    TenantContext::getInstance()->setTenant($tenant);
                    $request = $request->withAttribute('tenant', $tenant);
                }
            } catch (\Throwable) {
                // Tyst fel – fortsätt utan tenant
            }
        }

        return $handler->handle($request);
    }
}
