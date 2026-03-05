<?php

declare(strict_types=1);

/**
 * Migration: create certificates and certificate_types tables.
 *
 * Required by the HR → Certifikat module (CertificateController / CertificateRepository).
 */
return function (\PDO $pdo): void
{
    // ─── Certificate Types ────────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS certificate_types (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            is_deleted  TINYINT(1) NOT NULL DEFAULT 0,
            name        VARCHAR(200) NOT NULL,
            description TEXT NULL,
            validity_months SMALLINT UNSIGNED NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // ─── Certificates ─────────────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS certificates (
            id                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by            BIGINT UNSIGNED NULL,
            is_deleted            TINYINT(1) NOT NULL DEFAULT 0,
            employee_id           BIGINT UNSIGNED NOT NULL,
            certificate_type_id   BIGINT UNSIGNED NULL,
            issued_date           DATE NOT NULL,
            expiry_date           DATE NULL,
            file_path             VARCHAR(500) NULL,
            notes                 TEXT NULL,
            PRIMARY KEY (id),
            INDEX idx_certificates_employee (employee_id),
            INDEX idx_certificates_type (certificate_type_id),
            INDEX idx_certificates_expiry (expiry_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
};
