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

            $revenue   = (float) $pdo->query(
                "SELECT COALESCE(SUM(total), 0) FROM saas_invoices WHERE is_deleted = 0 AND status = 'paid'
                  AND MONTH(paid_at) = MONTH(CURDATE()) AND YEAR(paid_at) = YEAR(CURDATE())"
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
        } catch (\Throwable) {
            return [
                'total_tenants'   => 0,
                'active_tenants'  => 0,
                'trial_tenants'   => 0,
                'suspended'       => 0,
                'monthly_revenue' => 0.0,
                'open_tickets'    => 0,
                'recent_tenants'  => [],
                'recent_tickets'  => [],
            ];
        }

        return [
            'total_tenants'   => $total,
            'active_tenants'  => $active,
            'trial_tenants'   => $trial,
            'suspended'       => $suspended,
            'monthly_revenue' => $revenue,
            'open_tickets'    => $openTickets,
            'recent_tenants'  => $recentTenants,
            'recent_tickets'  => $recentTickets,
        ];
    }
}
