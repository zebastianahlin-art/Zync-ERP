<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Repository for SaaS multi-tenant administration.
 */
class SaasRepository
{
    // ── Tenants ────────────────────────────────────────────────────────────────

    /** Return all tenants (non-deleted), with optional filters. */
    public function allTenants(array $filters = []): array
    {
        $where  = ['t.is_deleted = 0'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]  = 't.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['plan'])) {
            $where[]  = 't.plan = ?';
            $params[] = $filters['plan'];
        }
        if (!empty($filters['search'])) {
            $where[]  = '(t.company_name LIKE ? OR t.contact_email LIKE ? OR t.contact_name LIKE ?)';
            $like     = '%' . $filters['search'] . '%';
            $params   = array_merge($params, [$like, $like, $like]);
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        $stmt = Database::pdo()->prepare(
            "SELECT t.*,
                    (SELECT COUNT(*) FROM saas_invoices i WHERE i.tenant_id = t.id AND i.is_deleted = 0) AS invoice_count
             FROM saas_tenants t
             $whereClause
             ORDER BY t.created_at DESC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Find a single tenant by ID, including its enabled modules. */
    public function findTenant(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM saas_tenants WHERE id = ? AND is_deleted = 0 LIMIT 1'
        );
        $stmt->execute([$id]);
        $tenant = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$tenant) {
            return null;
        }

        $tenant['modules'] = $this->tenantModules($id);
        return $tenant;
    }

    /** Create a new tenant. Returns new ID. */
    public function createTenant(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO saas_tenants
             (company_name, org_number, contact_name, contact_email, contact_phone,
              address, subdomain, status, trial_ends_at, plan, max_users, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['company_name'],
            $data['org_number']    ?: null,
            $data['contact_name']  ?: null,
            $data['contact_email'],
            $data['contact_phone'] ?: null,
            $data['address']       ?: null,
            $data['subdomain']     ?: null,
            $data['status']        ?? 'trial',
            $data['trial_ends_at'] ?: null,
            $data['plan']          ?? 'starter',
            (int) ($data['max_users'] ?? 10),
            $data['notes']         ?: null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    /** Update an existing tenant. */
    public function updateTenant(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE saas_tenants SET
                company_name  = ?,
                org_number    = ?,
                contact_name  = ?,
                contact_email = ?,
                contact_phone = ?,
                address       = ?,
                subdomain     = ?,
                status        = ?,
                trial_ends_at = ?,
                plan          = ?,
                max_users     = ?,
                notes         = ?
             WHERE id = ? AND is_deleted = 0'
        );
        $stmt->execute([
            $data['company_name'],
            $data['org_number']    ?: null,
            $data['contact_name']  ?: null,
            $data['contact_email'],
            $data['contact_phone'] ?: null,
            $data['address']       ?: null,
            $data['subdomain']     ?: null,
            $data['status']        ?? 'trial',
            $data['trial_ends_at'] ?: null,
            $data['plan']          ?? 'starter',
            (int) ($data['max_users'] ?? 10),
            $data['notes']         ?: null,
            $id,
        ]);
    }

    /** Soft-delete a tenant. */
    public function deleteTenant(int $id): void
    {
        Database::pdo()->prepare('UPDATE saas_tenants SET is_deleted = 1 WHERE id = ?')->execute([$id]);
    }

    // ── Tenant Modules ─────────────────────────────────────────────────────────

    /** List all enabled module slugs for a tenant. */
    public function tenantModules(int $tenantId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM saas_tenant_modules WHERE tenant_id = ? ORDER BY module_slug ASC'
        );
        $stmt->execute([$tenantId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Activate a module for a tenant (upsert). */
    public function activateModule(int $tenantId, string $moduleSlug): void
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO saas_tenant_modules (tenant_id, module_slug, is_active)
             VALUES (?, ?, 1)
             ON DUPLICATE KEY UPDATE is_active = 1, activated_at = CURRENT_TIMESTAMP'
        );
        $stmt->execute([$tenantId, $moduleSlug]);
    }

    /** Deactivate a module for a tenant. */
    public function deactivateModule(int $tenantId, string $moduleSlug): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE saas_tenant_modules SET is_active = 0 WHERE tenant_id = ? AND module_slug = ?'
        );
        $stmt->execute([$tenantId, $moduleSlug]);
    }

    // ── Invoices ───────────────────────────────────────────────────────────────

    /** Return all SaaS invoices, optionally filtered. */
    public function allInvoices(array $filters = []): array
    {
        $where  = ['i.is_deleted = 0'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]  = 'i.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['tenant_id'])) {
            $where[]  = 'i.tenant_id = ?';
            $params[] = (int) $filters['tenant_id'];
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);
        $stmt = Database::pdo()->prepare(
            "SELECT i.*, t.company_name
             FROM saas_invoices i
             JOIN saas_tenants t ON t.id = i.tenant_id
             $whereClause
             ORDER BY i.created_at DESC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Find a single invoice with tenant info. */
    public function findInvoice(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT i.*, t.company_name, t.contact_email, t.contact_name
             FROM saas_invoices i
             JOIN saas_tenants t ON t.id = i.tenant_id
             WHERE i.id = ? AND i.is_deleted = 0
             LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Create a new invoice. Returns new ID. */
    public function createInvoice(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO saas_invoices
             (tenant_id, invoice_number, period_start, period_end, amount, vat, total, status, due_date, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            (int) $data['tenant_id'],
            $data['invoice_number'],
            $data['period_start'],
            $data['period_end'],
            (float) ($data['amount'] ?? 0),
            (float) ($data['vat'] ?? 0),
            (float) ($data['total'] ?? 0),
            $data['status'] ?? 'draft',
            $data['due_date'],
            $data['notes'] ?: null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    /** Update an invoice. */
    public function updateInvoice(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE saas_invoices SET
                tenant_id      = ?,
                period_start   = ?,
                period_end     = ?,
                amount         = ?,
                vat            = ?,
                total          = ?,
                status         = ?,
                due_date       = ?,
                notes          = ?
             WHERE id = ? AND is_deleted = 0'
        );
        $stmt->execute([
            (int) $data['tenant_id'],
            $data['period_start'],
            $data['period_end'],
            (float) ($data['amount'] ?? 0),
            (float) ($data['vat'] ?? 0),
            (float) ($data['total'] ?? 0),
            $data['status'] ?? 'draft',
            $data['due_date'],
            $data['notes'] ?: null,
            $id,
        ]);
    }

    /** Update invoice status, setting paid_at when status = 'paid'. */
    public function updateInvoiceStatus(int $id, string $status): void
    {
        $paidAt = $status === 'paid' ? ', paid_at = CURDATE()' : '';
        $stmt   = Database::pdo()->prepare(
            "UPDATE saas_invoices SET status = ?{$paidAt} WHERE id = ?"
        );
        $stmt->execute([$status, $id]);
    }

    /** Generate a unique invoice number: SAAS-YYYY-NNNN. */
    public function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $stmt = Database::pdo()->prepare(
            "SELECT COUNT(*) FROM saas_invoices WHERE invoice_number LIKE ?"
        );
        $stmt->execute(["SAAS-{$year}-%"]);
        $count = (int) $stmt->fetchColumn() + 1;
        return sprintf('SAAS-%s-%04d', $year, $count);
    }

    // ── Support Tickets ────────────────────────────────────────────────────────

    /** Return all support tickets, optionally filtered. */
    public function allTickets(array $filters = []): array
    {
        $where  = ['t.is_deleted = 0'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]  = 't.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['priority'])) {
            $where[]  = 't.priority = ?';
            $params[] = $filters['priority'];
        }
        if (!empty($filters['tenant_id'])) {
            $where[]  = 't.tenant_id = ?';
            $params[] = (int) $filters['tenant_id'];
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);
        $stmt = Database::pdo()->prepare(
            "SELECT t.*, tn.company_name
             FROM saas_support_tickets t
             JOIN saas_tenants tn ON tn.id = t.tenant_id
             $whereClause
             ORDER BY
                CASE t.priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 ELSE 4 END,
                t.created_at DESC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Find a single ticket with its comments. */
    public function findTicket(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT t.*, tn.company_name, u.username AS assigned_username
             FROM saas_support_tickets t
             JOIN saas_tenants tn ON tn.id = t.tenant_id
             LEFT JOIN users u ON u.id = t.assigned_to
             WHERE t.id = ? AND t.is_deleted = 0
             LIMIT 1'
        );
        $stmt->execute([$id]);
        $ticket = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$ticket) {
            return null;
        }

        $cmt = Database::pdo()->prepare(
            'SELECT c.*, u.username
             FROM saas_support_comments c
             LEFT JOIN users u ON u.id = c.user_id
             WHERE c.ticket_id = ?
             ORDER BY c.created_at ASC'
        );
        $cmt->execute([$id]);
        $ticket['comments'] = $cmt->fetchAll(\PDO::FETCH_ASSOC);

        return $ticket;
    }

    /** Create a new support ticket. Returns new ID. */
    public function createTicket(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO saas_support_tickets
             (tenant_id, ticket_number, subject, description, priority, status, category, assigned_to)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            (int) $data['tenant_id'],
            $data['ticket_number'],
            $data['subject'],
            $data['description'],
            $data['priority']    ?? 'normal',
            $data['status']      ?? 'open',
            $data['category']    ?? 'question',
            !empty($data['assigned_to']) ? (int) $data['assigned_to'] : null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    /** Update a ticket. */
    public function updateTicket(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE saas_support_tickets SET
                subject     = ?,
                description = ?,
                priority    = ?,
                status      = ?,
                category    = ?,
                assigned_to = ?
             WHERE id = ? AND is_deleted = 0'
        );
        $stmt->execute([
            $data['subject'],
            $data['description'],
            $data['priority']    ?? 'normal',
            $data['status']      ?? 'open',
            $data['category']    ?? 'question',
            !empty($data['assigned_to']) ? (int) $data['assigned_to'] : null,
            $id,
        ]);
    }

    /** Update ticket status, setting resolved_at when appropriate. */
    public function updateTicketStatus(int $id, string $status): void
    {
        $resolvedAt = in_array($status, ['resolved', 'closed'], true) ? ', resolved_at = NOW()' : '';
        $stmt       = Database::pdo()->prepare(
            "UPDATE saas_support_tickets SET status = ?{$resolvedAt} WHERE id = ?"
        );
        $stmt->execute([$status, $id]);
    }

    /** Add a comment to a ticket. */
    public function addComment(int $ticketId, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO saas_support_comments (ticket_id, user_id, comment, is_internal)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $ticketId,
            !empty($data['user_id']) ? (int) $data['user_id'] : null,
            $data['comment'],
            isset($data['is_internal']) ? (int) $data['is_internal'] : 0,
        ]);
    }

    /** Generate a unique ticket number: SUP-YYYY-NNNN. */
    public function generateTicketNumber(): string
    {
        $year = date('Y');
        $stmt = Database::pdo()->prepare(
            "SELECT COUNT(*) FROM saas_support_tickets WHERE ticket_number LIKE ?"
        );
        $stmt->execute(["SUP-{$year}-%"]);
        $count = (int) $stmt->fetchColumn() + 1;
        return sprintf('SUP-%s-%04d', $year, $count);
    }

    // ── Plans ──────────────────────────────────────────────────────────────────

    /** Return all subscription plans. */
    public function allPlans(bool $activeOnly = false): array
    {
        try {
            $where = $activeOnly ? 'WHERE is_active = 1' : '';
            return Database::pdo()
                ->query("SELECT * FROM saas_plans $where ORDER BY sort_order ASC, id ASC")
                ->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable) {
            return [];
        }
    }

    /** Find a single plan by ID. */
    public function findPlan(int $id): ?array
    {
        try {
            $stmt = Database::pdo()->prepare('SELECT * FROM saas_plans WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\Throwable) {
            return null;
        }
    }

    /** Find a plan by slug. */
    public function findPlanBySlug(string $slug): ?array
    {
        try {
            $stmt = Database::pdo()->prepare('SELECT * FROM saas_plans WHERE slug = ? LIMIT 1');
            $stmt->execute([$slug]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\Throwable) {
            return null;
        }
    }

    /** Create a new plan. Returns new ID. */
    public function createPlan(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO saas_plans
             (name, slug, description, price_monthly, price_yearly, max_users, max_storage_gb, included_modules, features, is_active, sort_order)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['description'] ?: null,
            (float) ($data['price_monthly'] ?? 0),
            (float) ($data['price_yearly']  ?? 0),
            (int)   ($data['max_users']     ?? 10),
            (int)   ($data['max_storage_gb'] ?? 10),
            isset($data['included_modules']) ? (is_string($data['included_modules']) ? $data['included_modules'] : json_encode($data['included_modules'])) : null,
            isset($data['features']) ? (is_string($data['features']) ? $data['features'] : json_encode($data['features'])) : null,
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            (int) ($data['sort_order'] ?? 0),
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    /** Update an existing plan. */
    public function updatePlan(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE saas_plans SET
                name             = ?,
                slug             = ?,
                description      = ?,
                price_monthly    = ?,
                price_yearly     = ?,
                max_users        = ?,
                max_storage_gb   = ?,
                included_modules = ?,
                features         = ?,
                is_active        = ?,
                sort_order       = ?
             WHERE id = ?'
        );
        $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['description'] ?: null,
            (float) ($data['price_monthly'] ?? 0),
            (float) ($data['price_yearly']  ?? 0),
            (int)   ($data['max_users']     ?? 10),
            (int)   ($data['max_storage_gb'] ?? 10),
            isset($data['included_modules']) ? (is_string($data['included_modules']) ? $data['included_modules'] : json_encode($data['included_modules'])) : null,
            isset($data['features']) ? (is_string($data['features']) ? $data['features'] : json_encode($data['features'])) : null,
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            (int) ($data['sort_order'] ?? 0),
            $id,
        ]);
    }

    /** Delete a plan (hard delete — plans are not soft-deleted). */
    public function deletePlan(int $id): void
    {
        Database::pdo()->prepare('DELETE FROM saas_plans WHERE id = ?')->execute([$id]);
    }

    /** Count tenants on each plan. */
    public function tenantCountByPlan(): array
    {
        try {
            return Database::pdo()
                ->query(
                    "SELECT plan, COUNT(*) AS cnt
                     FROM saas_tenants
                     WHERE is_deleted = 0
                     GROUP BY plan"
                )
                ->fetchAll(\PDO::FETCH_KEY_PAIR);
        } catch (\Throwable) {
            return [];
        }
    }

    // ── Tenant History ─────────────────────────────────────────────────────────

    /** Return history log for a tenant. */
    public function tenantHistory(int $tenantId): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                "SELECT h.*, u.username AS changed_by_username
                 FROM saas_tenant_history h
                 LEFT JOIN users u ON u.id = h.changed_by
                 WHERE h.tenant_id = ?
                 ORDER BY h.created_at DESC"
            );
            $stmt->execute([$tenantId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable) {
            return [];
        }
    }

    /** Add a history entry for a tenant. */
    public function addTenantHistory(int $tenantId, string $action, ?string $oldValue, ?string $newValue, ?int $changedBy = null, ?string $notes = null): void
    {
        try {
            $stmt = Database::pdo()->prepare(
                'INSERT INTO saas_tenant_history (tenant_id, action, old_value, new_value, changed_by, notes)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$tenantId, $action, $oldValue, $newValue, $changedBy, $notes]);
        } catch (\Throwable) {
            // Tyst fel — historik är inte kritisk
        }
    }

    // ── Tenant Settings ────────────────────────────────────────────────────────

    /** Return all settings for a tenant. */
    public function tenantSettings(int $tenantId): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                'SELECT setting_key, value FROM saas_tenant_settings WHERE tenant_id = ?'
            );
            $stmt->execute([$tenantId]);
            return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
        } catch (\Throwable) {
            return [];
        }
    }

    /** Upsert a single setting for a tenant. */
    public function updateTenantSetting(int $tenantId, string $key, ?string $value): void
    {
        try {
            $stmt = Database::pdo()->prepare(
                'INSERT INTO saas_tenant_settings (tenant_id, setting_key, value)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE value = VALUES(value)'
            );
            $stmt->execute([$tenantId, $key, $value]);
        } catch (\Throwable) {
            // Tyst fel
        }
    }

    // ── Invoice Stats & Batch ──────────────────────────────────────────────────

    /** Return invoice KPI statistics. */
    public function invoiceStats(): array
    {
        $pdo = Database::pdo();
        try {
            $total   = (float) $pdo->query("SELECT COALESCE(SUM(total), 0) FROM saas_invoices WHERE is_deleted = 0 AND status = 'paid'")->fetchColumn();
            $unpaid  = (float) $pdo->query("SELECT COALESCE(SUM(total), 0) FROM saas_invoices WHERE is_deleted = 0 AND status IN ('draft','sent')")->fetchColumn();
            $overdue = (float) $pdo->query("SELECT COALESCE(SUM(total), 0) FROM saas_invoices WHERE is_deleted = 0 AND status = 'overdue'")->fetchColumn();
            $countUnpaid  = (int) $pdo->query("SELECT COUNT(*) FROM saas_invoices WHERE is_deleted = 0 AND status IN ('draft','sent')")->fetchColumn();
            $countOverdue = (int) $pdo->query("SELECT COUNT(*) FROM saas_invoices WHERE is_deleted = 0 AND status = 'overdue'")->fetchColumn();
            $mrr = (float) $pdo->query(
                "SELECT COALESCE(SUM(total), 0) FROM saas_invoices
                 WHERE is_deleted = 0 AND status = 'paid'
                 AND MONTH(paid_at) = MONTH(CURDATE()) AND YEAR(paid_at) = YEAR(CURDATE())"
            )->fetchColumn();
        } catch (\Throwable) {
            return ['total_paid' => 0, 'unpaid' => 0, 'overdue' => 0, 'count_unpaid' => 0, 'count_overdue' => 0, 'mrr' => 0];
        }

        return [
            'total_paid'    => $total,
            'unpaid'        => $unpaid,
            'overdue'       => $overdue,
            'count_unpaid'  => $countUnpaid,
            'count_overdue' => $countOverdue,
            'mrr'           => $mrr,
        ];
    }

    /**
     * Generate monthly invoices for all active tenants that don't already
     * have an invoice for the given month/year.
     *
     * @return int Number of invoices created
     */
    public function generateMonthlyInvoices(int $month, int $year): int
    {
        $pdo      = Database::pdo();
        $created  = 0;
        $periodStart = sprintf('%04d-%02d-01', $year, $month);
        $periodEnd   = date('Y-m-t', strtotime($periodStart));
        $dueDate     = date('Y-m-d', strtotime($periodEnd . ' +14 days'));

        try {
            $tenants = $pdo->query(
                "SELECT t.*, p.price_monthly, p.name AS plan_name
                 FROM saas_tenants t
                 LEFT JOIN saas_plans p ON p.slug = t.plan
                 WHERE t.is_deleted = 0 AND t.status = 'active'"
            )->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($tenants as $tenant) {
                // Check if invoice already exists for this period
                $check = $pdo->prepare(
                    "SELECT COUNT(*) FROM saas_invoices
                     WHERE tenant_id = ? AND period_start = ? AND is_deleted = 0"
                );
                $check->execute([(int) $tenant['id'], $periodStart]);
                if ((int) $check->fetchColumn() > 0) {
                    continue;
                }

                $amount = (float) ($tenant['price_monthly'] ?? 0);
                $vat    = round($amount * 0.25, 2);
                $total  = $amount + $vat;
                $invoiceNumber = $this->generateInvoiceNumber();

                $insert = $pdo->prepare(
                    'INSERT INTO saas_invoices
                     (tenant_id, invoice_number, period_start, period_end, amount, vat, total, status, due_date, notes)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $insert->execute([
                    (int) $tenant['id'],
                    $invoiceNumber,
                    $periodStart,
                    $periodEnd,
                    $amount,
                    $vat,
                    $total,
                    'draft',
                    $dueDate,
                    'Auto-genererad för ' . ($tenant['plan_name'] ?? $tenant['plan']) . ' – ' . date('F Y', strtotime($periodStart)),
                ]);
                $created++;
            }
        } catch (\Throwable) {
            // Returnera antal skapade hittills
        }

        return $created;
    }

    /** Return tenants whose trial ends within the next $days days. */
    public function tenantsWithExpiringTrial(int $days = 7): array
    {
        try {
            $stmt = Database::pdo()->prepare(
                "SELECT * FROM saas_tenants
                 WHERE is_deleted = 0
                   AND status = 'trial'
                   AND trial_ends_at IS NOT NULL
                   AND trial_ends_at <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
                   AND trial_ends_at >= CURDATE()
                 ORDER BY trial_ends_at ASC"
            );
            $stmt->execute([$days]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable) {
            return [];
        }
    }

    // ── Dashboard Stats ────────────────────────────────────────────────────────

    /** Return high-level SaaS dashboard statistics. */
    public function dashboardStats(): array
    {
        $pdo = Database::pdo();

        try {
            $total     = (int) $pdo->query('SELECT COUNT(*) FROM saas_tenants WHERE is_deleted = 0')->fetchColumn();
            $active    = (int) $pdo->query("SELECT COUNT(*) FROM saas_tenants WHERE is_deleted = 0 AND status = 'active'")->fetchColumn();
            $trial     = (int) $pdo->query("SELECT COUNT(*) FROM saas_tenants WHERE is_deleted = 0 AND status = 'trial'")->fetchColumn();
            $suspended = (int) $pdo->query("SELECT COUNT(*) FROM saas_tenants WHERE is_deleted = 0 AND status = 'suspended'")->fetchColumn();
            $cancelled = (int) $pdo->query("SELECT COUNT(*) FROM saas_tenants WHERE is_deleted = 0 AND status = 'cancelled'")->fetchColumn();

            $revenue   = (float) $pdo->query(
                "SELECT COALESCE(SUM(total), 0) FROM saas_invoices WHERE is_deleted = 0 AND status = 'paid'
                  AND MONTH(paid_at) = MONTH(CURDATE()) AND YEAR(paid_at) = YEAR(CURDATE())"
            )->fetchColumn();

            $unpaidInvoices = (int) $pdo->query(
                "SELECT COUNT(*) FROM saas_invoices WHERE is_deleted = 0 AND status IN ('draft','sent')"
            )->fetchColumn();

            $overdueInvoices = (int) $pdo->query(
                "SELECT COUNT(*) FROM saas_invoices WHERE is_deleted = 0 AND status = 'overdue'"
            )->fetchColumn();

            $openTickets = (int) $pdo->query(
                "SELECT COUNT(*) FROM saas_support_tickets WHERE is_deleted = 0 AND status IN ('open','in_progress','waiting')"
            )->fetchColumn();

            $recentTenants = $pdo->query(
                "SELECT * FROM saas_tenants WHERE is_deleted = 0 ORDER BY created_at DESC LIMIT 5"
            )->fetchAll(\PDO::FETCH_ASSOC);

            $recentTickets = $pdo->query(
                "SELECT t.*, tn.company_name FROM saas_support_tickets t
                 JOIN saas_tenants tn ON tn.id = t.tenant_id
                 WHERE t.is_deleted = 0 ORDER BY t.created_at DESC LIMIT 5"
            )->fetchAll(\PDO::FETCH_ASSOC);

            // Kunder per plan (för CSS-staplar)
            $planRows = $pdo->query(
                "SELECT plan, COUNT(*) AS cnt FROM saas_tenants WHERE is_deleted = 0 GROUP BY plan"
            )->fetchAll(\PDO::FETCH_ASSOC);
            $byPlan = [];
            foreach ($planRows as $row) {
                $byPlan[$row['plan']] = (int) $row['cnt'];
            }

            // Trial som snart löper ut (< 7 dagar)
            $expiringTrials = $this->tenantsWithExpiringTrial(7);
        } catch (\Throwable) {
            return [
                'total_tenants'    => 0,
                'active_tenants'   => 0,
                'trial_tenants'    => 0,
                'suspended'        => 0,
                'cancelled'        => 0,
                'monthly_revenue'  => 0.0,
                'unpaid_invoices'  => 0,
                'overdue_invoices' => 0,
                'open_tickets'     => 0,
                'recent_tenants'   => [],
                'recent_tickets'   => [],
                'by_plan'          => [],
                'expiring_trials'  => [],
            ];
        }

        return [
            'total_tenants'    => $total,
            'active_tenants'   => $active,
            'trial_tenants'    => $trial,
            'suspended'        => $suspended,
            'cancelled'        => $cancelled,
            'monthly_revenue'  => $revenue,
            'unpaid_invoices'  => $unpaidInvoices,
            'overdue_invoices' => $overdueInvoices,
            'open_tickets'     => $openTickets,
            'recent_tenants'   => $recentTenants,
            'recent_tickets'   => $recentTickets,
            'by_plan'          => $byPlan,
            'expiring_trials'  => $expiringTrials,
        ];
    }
}
