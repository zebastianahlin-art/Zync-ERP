<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class JournalEntryRepository
{
    public function all(?int $year = null, ?int $period = null): array
    {
        $where = "je.is_deleted = 0";
        $params = [];
        if ($year) { $where .= " AND je.fiscal_year = ?"; $params[] = $year; }
        if ($period) { $where .= " AND je.fiscal_period = ?"; $params[] = $period; }

        $stmt = Database::pdo()->prepare(
            "SELECT je.*, u.full_name AS created_by_name
             FROM journal_entries je
             LEFT JOIN users u ON je.created_by = u.id
             WHERE $where
             ORDER BY je.entry_date DESC, je.voucher_number DESC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT je.*, u.full_name AS created_by_name
             FROM journal_entries je
             LEFT JOIN users u ON je.created_by = u.id
             WHERE je.id = ? AND je.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $number = $this->nextVoucherNumber($data['voucher_series'] ?? 'A');
        $entryDate = $data['entry_date'];
        $stmt = Database::pdo()->prepare(
            "INSERT INTO journal_entries 
             (voucher_number, voucher_series, entry_date, description, source_type, source_id,
              fiscal_year, fiscal_period, notes, created_by)
             VALUES (?, ?, ?, ?, ?, ?, YEAR(?), MONTH(?), ?, ?)"
        );
        $stmt->execute([
            $number,
            $data['voucher_series'] ?? 'A',
            $entryDate,
            $data['description'],
            $data['source_type'] ?? 'manual',
            $data['source_id'] ?? null,
            $entryDate,
            $entryDate,
            $data['notes'] ?? null,
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function addLine(int $entryId, array $data): void
    {
        Database::pdo()->prepare(
            "INSERT INTO journal_entry_lines (entry_id, account_id, cost_center_id, description, debit, credit)
             VALUES (?, ?, ?, ?, ?, ?)"
        )->execute([
            $entryId,
            $data['account_id'],
            $data['cost_center_id'] ?: null,
            $data['description'] ?? null,
            (float) ($data['debit'] ?? 0),
            (float) ($data['credit'] ?? 0),
        ]);
        $this->recalcTotals($entryId);
    }

    public function removeLine(int $entryId, int $lineId): void
    {
        Database::pdo()->prepare("DELETE FROM journal_entry_lines WHERE id = ? AND entry_id = ?")->execute([$lineId, $entryId]);
        $this->recalcTotals($entryId);
    }

    public function getLines(int $entryId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT jel.*, coa.account_number, coa.name AS account_name,
                    cc.code AS cost_center_code, cc.name AS cost_center_name
             FROM journal_entry_lines jel
             LEFT JOIN chart_of_accounts coa ON jel.account_id = coa.id
             LEFT JOIN cost_centers cc ON jel.cost_center_id = cc.id
             WHERE jel.entry_id = ?
             ORDER BY jel.id"
        );
        $stmt->execute([$entryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete(int $id): void
    {
        $entry = $this->find($id);
        if ($entry && $entry['is_locked']) return;
        Database::pdo()->prepare("UPDATE journal_entries SET is_deleted = 1 WHERE id = ? AND is_locked = 0")->execute([$id]);
    }

    // --- Huvudbok ---
    public function ledger(string $fromDate, string $toDate, ?string $accountFrom = null, ?string $accountTo = null): array
    {
        $where = "je.entry_date BETWEEN ? AND ? AND je.is_deleted = 0";
        $params = [$fromDate, $toDate];
        if ($accountFrom) { $where .= " AND coa.account_number >= ?"; $params[] = $accountFrom; }
        if ($accountTo) { $where .= " AND coa.account_number <= ?"; $params[] = $accountTo; }

        $stmt = Database::pdo()->prepare(
            "SELECT coa.account_number, coa.name AS account_name,
                    je.voucher_number, je.entry_date, jel.description,
                    jel.debit, jel.credit,
                    cc.code AS cost_center_code
             FROM journal_entry_lines jel
             JOIN journal_entries je ON jel.entry_id = je.id
             JOIN chart_of_accounts coa ON jel.account_id = coa.id
             LEFT JOIN cost_centers cc ON jel.cost_center_id = cc.id
             WHERE $where
             ORDER BY coa.account_number, je.entry_date, je.voucher_number"
        );
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- Resultaträkning ---
    public function trialBalance(string $fromDate, string $toDate): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT coa.account_number, coa.name AS account_name, coa.account_class,
                    COALESCE(SUM(jel.debit), 0) AS total_debit,
                    COALESCE(SUM(jel.credit), 0) AS total_credit,
                    COALESCE(SUM(jel.debit), 0) - COALESCE(SUM(jel.credit), 0) AS balance
             FROM chart_of_accounts coa
             LEFT JOIN journal_entry_lines jel ON coa.id = jel.account_id
             LEFT JOIN journal_entries je ON jel.entry_id = je.id AND je.entry_date BETWEEN ? AND ? AND je.is_deleted = 0
             GROUP BY coa.id, coa.account_number, coa.name, coa.account_class
             HAVING total_debit != 0 OR total_credit != 0
             ORDER BY coa.account_number"
        );
        $stmt->execute([$fromDate, $toDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- Kostnadsställerapport ---
    public function costCenterReport(string $fromDate, string $toDate): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT cc.code, cc.name, cc.budget,
                    COALESCE(SUM(jel.debit), 0) AS total_debit,
                    COALESCE(SUM(jel.credit), 0) AS total_credit,
                    COALESCE(SUM(jel.debit), 0) - COALESCE(SUM(jel.credit), 0) AS balance
             FROM cost_centers cc
             LEFT JOIN journal_entry_lines jel ON cc.id = jel.cost_center_id
             LEFT JOIN journal_entries je ON jel.entry_id = je.id AND je.entry_date BETWEEN ? AND ? AND je.is_deleted = 0
             WHERE cc.is_active = 1 AND cc.is_deleted = 0
             GROUP BY cc.id, cc.code, cc.name, cc.budget
             ORDER BY cc.code"
        );
        $stmt->execute([$fromDate, $toDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function recalcTotals(int $entryId): void
    {
        $stmt = Database::pdo()->prepare(
            "SELECT COALESCE(SUM(debit), 0) AS td, COALESCE(SUM(credit), 0) AS tc FROM journal_entry_lines WHERE entry_id = ?"
        );
        $stmt->execute([$entryId]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        $d = (float) $r['td'];
        $c = (float) $r['tc'];
        Database::pdo()->prepare(
            "UPDATE journal_entries SET total_debit = ?, total_credit = ?, is_balanced = ? WHERE id = ?"
        )->execute([$d, $c, abs($d - $c) < 0.01 ? 1 : 0, $entryId]);
    }

    private function nextVoucherNumber(string $series): string
    {
        $year = date('Y');
        $prefix = $series . $year;
        $last = Database::pdo()->query(
            "SELECT voucher_number FROM journal_entries WHERE voucher_number LIKE '{$prefix}%' ORDER BY voucher_number DESC LIMIT 1"
        )->fetchColumn();
        $seq = $last ? (int) substr($last, strlen($prefix)) + 1 : 1;
        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
