<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class FinanceReportRepository
{
    /**
     * Returns journal entry lines with account info for ledger view.
     * Optionally filtered by a single account number.
     */
    public function getLedgerEntries(string $from, string $to, ?int $accountId = null): array
    {
        $where = "je.is_deleted = 0 AND je.entry_date BETWEEN ? AND ?";
        $params = [$from, $to];

        if ($accountId !== null) {
            $where .= " AND jel.account_id = ?";
            $params[] = $accountId;
        }

        $stmt = Database::pdo()->prepare(
            "SELECT jel.*, je.entry_date, je.voucher_number, je.description AS entry_description,
                    coa.account_number, coa.name AS account_name,
                    cc.code AS cost_center_code
             FROM journal_entry_lines jel
             JOIN journal_entries je ON jel.entry_id = je.id
             JOIN chart_of_accounts coa ON jel.account_id = coa.id
             LEFT JOIN cost_centers cc ON jel.cost_center_id = cc.id
             WHERE {$where}
             ORDER BY coa.account_number, je.entry_date, je.voucher_number"
        );
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * All transactions for one account with running balance, opening and closing balance.
     */
    public function getAccountLedger(int $accountId, string $from, string $to): array
    {
        // Opening balance: all entries before $from
        $stmtOpen = Database::pdo()->prepare(
            "SELECT COALESCE(SUM(jel.debit), 0) - COALESCE(SUM(jel.credit), 0) AS opening_balance
             FROM journal_entry_lines jel
             JOIN journal_entries je ON jel.entry_id = je.id
             WHERE jel.account_id = ? AND je.is_deleted = 0 AND je.entry_date < ?"
        );
        $stmtOpen->execute([$accountId, $from]);
        $openingBalance = (float) $stmtOpen->fetchColumn();

        $stmtLines = Database::pdo()->prepare(
            "SELECT jel.*, je.entry_date, je.voucher_number, je.description AS entry_description,
                    coa.account_number, coa.name AS account_name,
                    cc.code AS cost_center_code
             FROM journal_entry_lines jel
             JOIN journal_entries je ON jel.entry_id = je.id
             JOIN chart_of_accounts coa ON jel.account_id = coa.id
             LEFT JOIN cost_centers cc ON jel.cost_center_id = cc.id
             WHERE jel.account_id = ? AND je.is_deleted = 0 AND je.entry_date BETWEEN ? AND ?
             ORDER BY je.entry_date, je.voucher_number"
        );
        $stmtLines->execute([$accountId, $from, $to]);
        $lines = $stmtLines->fetchAll(PDO::FETCH_ASSOC);

        // Compute running balance
        $runningBalance = $openingBalance;
        foreach ($lines as &$line) {
            $runningBalance += (float) $line['debit'] - (float) $line['credit'];
            $line['running_balance'] = $runningBalance;
        }
        unset($line);

        $closingBalance = $runningBalance;

        return [
            'opening_balance' => $openingBalance,
            'closing_balance' => $closingBalance,
            'lines' => $lines,
        ];
    }

    /**
     * Balance sheet: assets (1xxx) and liabilities+equity (2xxx).
     */
    public function getBalanceSheet(string $from, string $to): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT coa.account_number, coa.name AS account_name, coa.account_class,
                    COALESCE(SUM(jel.debit), 0) AS total_debit,
                    COALESCE(SUM(jel.credit), 0) AS total_credit,
                    COALESCE(SUM(jel.debit), 0) - COALESCE(SUM(jel.credit), 0) AS balance
             FROM chart_of_accounts coa
             LEFT JOIN journal_entry_lines jel ON jel.account_id = coa.id
             LEFT JOIN journal_entries je ON jel.entry_id = je.id
                 AND je.is_deleted = 0 AND je.entry_date BETWEEN ? AND ?
             WHERE coa.is_active = 1 AND coa.account_class IN ('1','2')
             GROUP BY coa.id, coa.account_number, coa.name, coa.account_class
             HAVING (total_debit > 0 OR total_credit > 0)
             ORDER BY coa.account_number"
        );
        $stmt->execute([$from, $to]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $assets = [];
        $liabilities = [];
        foreach ($rows as $row) {
            if ($row['account_class'] === '1') {
                $assets[] = $row;
            } else {
                $liabilities[] = $row;
            }
        }

        return ['assets' => $assets, 'liabilities' => $liabilities];
    }

    /**
     * Trial balance joined with budget amounts for the given year.
     */
    public function getTrialBalanceWithBudget(string $from, string $to, int $year): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT coa.account_number, coa.name AS account_name, coa.account_class,
                    COALESCE(SUM(jel.debit), 0) AS total_debit,
                    COALESCE(SUM(jel.credit), 0) AS total_credit,
                    COALESCE(SUM(jel.debit), 0) - COALESCE(SUM(jel.credit), 0) AS balance,
                    COALESCE(bud.budget_total, 0) AS budget_total
             FROM chart_of_accounts coa
             LEFT JOIN journal_entry_lines jel ON jel.account_id = coa.id
             LEFT JOIN journal_entries je ON jel.entry_id = je.id
                 AND je.is_deleted = 0 AND je.entry_date BETWEEN ? AND ?
             LEFT JOIN (
                 SELECT account_id, SUM(amount) AS budget_total
                 FROM account_budgets
                 WHERE fiscal_year = ? AND is_deleted = 0
                 GROUP BY account_id
             ) bud ON bud.account_id = coa.id
             WHERE coa.is_active = 1
             GROUP BY coa.id, coa.account_number, coa.name, coa.account_class, bud.budget_total
             HAVING (total_debit > 0 OR total_credit > 0 OR budget_total > 0)
             ORDER BY coa.account_number"
        );
        $stmt->execute([$from, $to, $year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Trial balance with previous year comparison.
     */
    public function getPreviousYearComparison(string $from, string $to): array
    {
        $year = (int) date('Y', strtotime($from));
        $prevFrom = ($year - 1) . substr($from, 4);
        $prevTo   = ($year - 1) . substr($to, 4);

        $stmt = Database::pdo()->prepare(
            "SELECT coa.account_number, coa.name AS account_name, coa.account_class,
                    COALESCE(SUM(CASE WHEN je.entry_date BETWEEN ? AND ? THEN jel.debit ELSE 0 END), 0) AS total_debit,
                    COALESCE(SUM(CASE WHEN je.entry_date BETWEEN ? AND ? THEN jel.credit ELSE 0 END), 0) AS total_credit,
                    COALESCE(SUM(CASE WHEN je.entry_date BETWEEN ? AND ? THEN jel.debit - jel.credit ELSE 0 END), 0) AS balance,
                    COALESCE(SUM(CASE WHEN je.entry_date BETWEEN ? AND ? THEN jel.debit - jel.credit ELSE 0 END), 0) AS prev_balance
             FROM chart_of_accounts coa
             LEFT JOIN journal_entry_lines jel ON jel.account_id = coa.id
             LEFT JOIN journal_entries je ON jel.entry_id = je.id AND je.is_deleted = 0
             WHERE coa.is_active = 1
             GROUP BY coa.id, coa.account_number, coa.name, coa.account_class
             HAVING (total_debit > 0 OR total_credit > 0 OR prev_balance <> 0)
             ORDER BY coa.account_number"
        );
        $stmt->execute([$from, $to, $from, $to, $from, $to, $prevFrom, $prevTo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
