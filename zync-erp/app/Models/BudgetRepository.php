<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class BudgetRepository
{
    public function allForYear(int $year): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT ab.*, coa.account_number, coa.name AS account_name
             FROM account_budgets ab
             JOIN chart_of_accounts coa ON ab.account_id = coa.id
             WHERE ab.fiscal_year = ? AND ab.is_deleted = 0
             ORDER BY coa.account_number, ab.month"
        );
        $stmt->execute([$year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT ab.*, coa.account_number, coa.name AS account_name
             FROM account_budgets ab
             JOIN chart_of_accounts coa ON ab.account_id = coa.id
             WHERE ab.id = ? AND ab.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO account_budgets (account_id, fiscal_year, month, amount)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            (int) $data['account_id'],
            (int) $data['fiscal_year'],
            (int) $data['month'],
            (float) ($data['amount'] ?? 0),
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE account_budgets SET account_id = ?, fiscal_year = ?, month = ?, amount = ?
             WHERE id = ? AND is_deleted = 0"
        );
        $stmt->execute([
            (int) $data['account_id'],
            (int) $data['fiscal_year'],
            (int) $data['month'],
            (float) ($data['amount'] ?? 0),
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        Database::pdo()->prepare(
            "UPDATE account_budgets SET is_deleted = 1 WHERE id = ?"
        )->execute([$id]);
    }

    public function getForAccount(int $accountId, int $year): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT * FROM account_budgets
             WHERE account_id = ? AND fiscal_year = ? AND is_deleted = 0
             ORDER BY month"
        );
        $stmt->execute([$accountId, $year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
