<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class FixedAssetRepository
{
    public function all(): array
    {
        return Database::pdo()->query(
            "SELECT fa.*, d.name AS department_name, coa.account_number, coa.name AS account_name
             FROM fixed_assets fa
             LEFT JOIN departments d ON fa.department_id = d.id
             LEFT JOIN chart_of_accounts coa ON fa.account_id = coa.id
             WHERE fa.is_deleted = 0
             ORDER BY fa.asset_number"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT fa.*, d.name AS department_name, coa.account_number, coa.name AS account_name,
                    u.full_name AS created_by_name
             FROM fixed_assets fa
             LEFT JOIN departments d ON fa.department_id = d.id
             LEFT JOIN chart_of_accounts coa ON fa.account_id = coa.id
             LEFT JOIN users u ON fa.created_by = u.id
             WHERE fa.id = ? AND fa.is_deleted = 0"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO fixed_assets
             (name, description, asset_number, purchase_date, purchase_price, current_value,
              depreciation_method, depreciation_years, department_id, account_id, status, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['asset_number'],
            $data['purchase_date'],
            (float) ($data['purchase_price'] ?? 0),
            (float) ($data['current_value'] ?? $data['purchase_price'] ?? 0),
            $data['depreciation_method'] ?? 'linear',
            (int) ($data['depreciation_years'] ?? 5),
            !empty($data['department_id']) ? (int) $data['department_id'] : null,
            !empty($data['account_id']) ? (int) $data['account_id'] : null,
            $data['status'] ?? 'active',
            $data['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE fixed_assets SET
             name = ?, description = ?, asset_number = ?, purchase_date = ?, purchase_price = ?,
             current_value = ?, depreciation_method = ?, depreciation_years = ?,
             department_id = ?, account_id = ?, status = ?
             WHERE id = ? AND is_deleted = 0"
        );
        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['asset_number'],
            $data['purchase_date'],
            (float) ($data['purchase_price'] ?? 0),
            (float) ($data['current_value'] ?? 0),
            $data['depreciation_method'] ?? 'linear',
            (int) ($data['depreciation_years'] ?? 5),
            !empty($data['department_id']) ? (int) $data['department_id'] : null,
            !empty($data['account_id']) ? (int) $data['account_id'] : null,
            $data['status'] ?? 'active',
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        Database::pdo()->prepare(
            "UPDATE fixed_assets SET is_deleted = 1 WHERE id = ?"
        )->execute([$id]);
    }

    public function getDepreciations(int $assetId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT * FROM asset_depreciations
             WHERE asset_id = ?
             ORDER BY depreciation_date DESC"
        );
        $stmt->execute([$assetId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addDepreciation(int $assetId, float $amount, string $date): void
    {
        $pdo = Database::pdo();

        $stmtAcc = $pdo->prepare(
            "SELECT COALESCE(SUM(amount), 0) FROM asset_depreciations WHERE asset_id = ?"
        );
        $stmtAcc->execute([$assetId]);
        $accumulated = (float) $stmtAcc->fetchColumn();
        $accumulated += $amount;

        $pdo->prepare(
            "INSERT INTO asset_depreciations (asset_id, depreciation_date, amount, accumulated)
             VALUES (?, ?, ?, ?)"
        )->execute([$assetId, $date, $amount, $accumulated]);

        // Update current_value
        $asset = $this->find($assetId);
        if ($asset) {
            $newValue = max(0, (float) $asset['current_value'] - $amount);
            $pdo->prepare(
                "UPDATE fixed_assets SET current_value = ? WHERE id = ?"
            )->execute([$newValue, $assetId]);
        }
    }

    public function calculateDepreciation(int $assetId): float
    {
        $asset = $this->find($assetId);
        if (!$asset || $asset['depreciation_years'] <= 0) {
            return 0.0;
        }

        if ($asset['depreciation_method'] === 'declining') {
            // Double-declining balance: current_value * (2 / years)
            return round((float) $asset['current_value'] * (2 / $asset['depreciation_years']), 2);
        }

        // Linear: purchase_price / years (annual)
        return round((float) $asset['purchase_price'] / $asset['depreciation_years'], 2);
    }
}
