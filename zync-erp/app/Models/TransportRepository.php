<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class TransportRepository
{
    public function stats(): array
    {
        $pdo = Database::pdo();
        return [
            'planned'    => (int) $pdo->query("SELECT COUNT(*) FROM transport_orders WHERE status = 'planned'    AND is_deleted = 0")->fetchColumn(),
            'in_transit' => (int) $pdo->query("SELECT COUNT(*) FROM transport_orders WHERE status = 'in_transit' AND is_deleted = 0")->fetchColumn(),
            'delivered'  => (int) $pdo->query("SELECT COUNT(*) FROM transport_orders WHERE status = 'delivered'  AND is_deleted = 0")->fetchColumn(),
            'carriers'   => (int) $pdo->query('SELECT COUNT(*) FROM transport_carriers WHERE is_deleted = 0')->fetchColumn(),
        ];
    }

    public function allOrders(): array
    {
        return Database::pdo()->query(
            'SELECT tro.*, tc.name AS carrier_name, c.name AS customer_name
             FROM transport_orders tro
             LEFT JOIN transport_carriers tc ON tro.carrier_id  = tc.id
             LEFT JOIN customers c           ON tro.customer_id = c.id
             WHERE tro.is_deleted = 0
             ORDER BY tro.created_at DESC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function recentOrders(int $limit = 5): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT tro.*, tc.name AS carrier_name, c.name AS customer_name
             FROM transport_orders tro
             LEFT JOIN transport_carriers tc ON tro.carrier_id  = tc.id
             LEFT JOIN customers c           ON tro.customer_id = c.id
             WHERE tro.is_deleted = 0
             ORDER BY tro.created_at DESC
             LIMIT ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findOrder(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT tro.*, tc.name AS carrier_name, c.name AS customer_name
             FROM transport_orders tro
             LEFT JOIN transport_carriers tc ON tro.carrier_id  = tc.id
             LEFT JOIN customers c           ON tro.customer_id = c.id
             WHERE tro.id = ? AND tro.is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function createOrder(array $data): int
    {
        $pdo    = Database::pdo();
        $year   = date('Y');
        $count  = (int) $pdo->query('SELECT COUNT(*) FROM transport_orders')->fetchColumn() + 1;
        $number = $data['transport_number'] ?: ('TR-' . $year . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT));

        $stmt = $pdo->prepare(
            'INSERT INTO transport_orders
             (transport_number, type, carrier_id, customer_id, supplier_id, sales_order_id, article_id,
              pickup_address, delivery_address, pickup_date, delivery_date,
              weight, volume, tracking_number, status, cost, currency, notes, created_by)
             VALUES
             (:transport_number, :type, :carrier_id, :customer_id, :supplier_id, :sales_order_id, :article_id,
              :pickup_address, :delivery_address, :pickup_date, :delivery_date,
              :weight, :volume, :tracking_number, :status, :cost, :currency, :notes, :created_by)'
        );
        $stmt->execute([
            'transport_number' => $number,
            'type'             => $data['type']          ?? 'outbound',
            'carrier_id'       => $data['carrier_id']    ?: null,
            'customer_id'      => $data['customer_id']   ?: null,
            'supplier_id'      => $data['supplier_id']   ?: null,
            'sales_order_id'   => $data['sales_order_id'] ?: null,
            'article_id'       => $data['article_id']    ?: null,
            'pickup_address'   => $data['pickup_address']   ?: null,
            'delivery_address' => $data['delivery_address'] ?: null,
            'pickup_date'      => $data['pickup_date']   ?: null,
            'delivery_date'    => $data['delivery_date'] ?: null,
            'weight'           => $data['weight'] !== '' ? $data['weight'] : null,
            'volume'           => $data['volume'] !== '' ? $data['volume'] : null,
            'tracking_number'  => $data['tracking_number'] ?: null,
            'status'           => $data['status'] ?? 'planned',
            'cost'             => $data['cost'] !== '' ? $data['cost'] : null,
            'currency'         => $data['currency'] ?? 'SEK',
            'notes'            => $data['notes']    ?: null,
            'created_by'       => $data['created_by'] ?? null,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public function updateOrder(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE transport_orders SET
             transport_number = :transport_number, type = :type, carrier_id = :carrier_id,
             customer_id = :customer_id, supplier_id = :supplier_id, sales_order_id = :sales_order_id,
             pickup_address = :pickup_address, delivery_address = :delivery_address,
             pickup_date = :pickup_date, delivery_date = :delivery_date,
             weight = :weight, volume = :volume, tracking_number = :tracking_number,
             status = :status, cost = :cost, currency = :currency, notes = :notes
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'transport_number' => $data['transport_number'],
            'type'             => $data['type']          ?? 'outbound',
            'carrier_id'       => $data['carrier_id']    ?: null,
            'customer_id'      => $data['customer_id']   ?: null,
            'supplier_id'      => $data['supplier_id']   ?: null,
            'sales_order_id'   => $data['sales_order_id'] ?: null,
            'pickup_address'   => $data['pickup_address']   ?: null,
            'delivery_address' => $data['delivery_address'] ?: null,
            'pickup_date'      => $data['pickup_date']   ?: null,
            'delivery_date'    => $data['delivery_date'] ?: null,
            'weight'           => $data['weight'] !== '' ? $data['weight'] : null,
            'volume'           => $data['volume'] !== '' ? $data['volume'] : null,
            'tracking_number'  => $data['tracking_number'] ?: null,
            'status'           => $data['status'] ?? 'planned',
            'cost'             => $data['cost'] !== '' ? $data['cost'] : null,
            'currency'         => $data['currency'] ?? 'SEK',
            'notes'            => $data['notes']    ?: null,
            'id'               => $id,
        ]);
    }

    public function deleteOrder(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE transport_orders SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function updateOrderStatus(int $id, string $status): void
    {
        $now   = date('Y-m-d H:i:s');
        $extra = '';
        if ($status === 'in_transit') {
            $extra = ', actual_pickup = :ts';
        } elseif ($status === 'delivered') {
            $extra = ', actual_delivery = :ts';
        }

        $sql    = "UPDATE transport_orders SET status = :status{$extra} WHERE id = :id AND is_deleted = 0";
        $stmt   = Database::pdo()->prepare($sql);
        $params = ['status' => $status, 'id' => $id];
        if ($extra !== '') {
            $params['ts'] = $now;
        }
        $stmt->execute($params);
    }

    // ─── Carriers ────────────────────────────────────────────────────────

    public function allCarriers(): array
    {
        return Database::pdo()->query(
            'SELECT * FROM transport_carriers WHERE is_deleted = 0 ORDER BY name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findCarrier(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM transport_carriers WHERE id = ? AND is_deleted = 0'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function createCarrier(array $data): int
    {
        $pdo  = Database::pdo();
        $stmt = $pdo->prepare(
            'INSERT INTO transport_carriers
             (name, code, type, contact_person, phone, email, contract_number, contract_valid_until, is_active, notes, created_by)
             VALUES
             (:name, :code, :type, :contact_person, :phone, :email, :contract_number, :contract_valid_until, :is_active, :notes, :created_by)'
        );
        $stmt->execute([
            'name'                 => $data['name'],
            'code'                 => $data['code']          ?: null,
            'type'                 => $data['type']          ?? 'external',
            'contact_person'       => $data['contact_person'] ?: null,
            'phone'                => $data['phone']         ?: null,
            'email'                => $data['email']         ?: null,
            'contract_number'      => $data['contract_number']      ?: null,
            'contract_valid_until' => $data['contract_valid_until'] ?: null,
            'is_active'            => isset($data['is_active']) ? 1 : 0,
            'notes'                => $data['notes']     ?: null,
            'created_by'           => $data['created_by'] ?? null,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public function updateCarrier(int $id, array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE transport_carriers SET
             name = :name, code = :code, type = :type, contact_person = :contact_person,
             phone = :phone, email = :email, contract_number = :contract_number,
             contract_valid_until = :contract_valid_until, is_active = :is_active, notes = :notes
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([
            'name'                 => $data['name'],
            'code'                 => $data['code']          ?: null,
            'type'                 => $data['type']          ?? 'external',
            'contact_person'       => $data['contact_person'] ?: null,
            'phone'                => $data['phone']         ?: null,
            'email'                => $data['email']         ?: null,
            'contract_number'      => $data['contract_number']      ?: null,
            'contract_valid_until' => $data['contract_valid_until'] ?: null,
            'is_active'            => isset($data['is_active']) ? 1 : 0,
            'notes'                => $data['notes'] ?: null,
            'id'                   => $id,
        ]);
    }

    public function deleteCarrier(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE transport_carriers SET is_deleted = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function allCustomers(): array
    {
        return Database::pdo()->query(
            'SELECT id, name FROM customers WHERE is_deleted = 0 ORDER BY name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allSuppliers(): array
    {
        return Database::pdo()->query(
            'SELECT id, name FROM suppliers WHERE is_deleted = 0 ORDER BY name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function allArticles(): array
    {
        try {
            return Database::pdo()->query(
                'SELECT id, article_number, name, unit FROM articles WHERE is_deleted = 0 ORDER BY article_number ASC'
            )->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Synkroniserar transportören med leverantörsregistret.
     * Om $supplierId = null skapas en ny leverantör med type='carrier'.
     * Returnerar leverantörens ID.
     */
    public function syncCarrierWithSupplier(int $carrierId, string $name, string $email, string $phone, int $createdBy, ?int $existingSupplierId): int
    {
        $pdo = Database::pdo();

        if ($existingSupplierId) {
            // Länka befintlig leverantör
            $pdo->prepare('UPDATE transport_carriers SET supplier_id = ? WHERE id = ?')
                ->execute([$existingSupplierId, $carrierId]);
            return $existingSupplierId;
        }

        // Skapa ny leverantör
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO suppliers (name, email, phone, type, is_active, created_by)
                 VALUES (:name, :email, :phone, 'carrier', 1, :created_by)"
            );
            $stmt->execute([
                'name'       => $name,
                'email'      => $email ?: null,
                'phone'      => $phone ?: null,
                'created_by' => $createdBy,
            ]);
            $supplierId = (int) $pdo->lastInsertId();
            if ($supplierId > 0) {
                $pdo->prepare('UPDATE transport_carriers SET supplier_id = ? WHERE id = ?')
                    ->execute([$supplierId, $carrierId]);
            }
            return $supplierId;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
