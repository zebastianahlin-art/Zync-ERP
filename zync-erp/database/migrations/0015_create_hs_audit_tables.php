<?php
declare(strict_types=1);
return function (\PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS hs_audit_templates (
        id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        created_by      BIGINT UNSIGNED NULL,
        is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
        name            VARCHAR(255) NOT NULL,
        description     TEXT NULL,
        category        ENUM('workplace','fire_safety','electrical','chemical','ergonomic','environmental','general') NOT NULL DEFAULT 'general',
        version         INT NOT NULL DEFAULT 1,
        is_active       TINYINT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (id),
        INDEX idx_hs_at_category (category)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS hs_audit_template_items (
        id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        template_id     BIGINT UNSIGNED NOT NULL,
        sort_order      INT NOT NULL DEFAULT 0,
        section         VARCHAR(255) NULL,
        question        TEXT NOT NULL,
        description     TEXT NULL,
        response_type   ENUM('yes_no','ok_not_ok','scale_1_5','text','na') NOT NULL DEFAULT 'yes_no',
        is_required     TINYINT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (id),
        INDEX idx_hs_ati_template (template_id),
        CONSTRAINT fk_hs_ati_template FOREIGN KEY (template_id) REFERENCES hs_audit_templates(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS hs_audits (
        id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        created_by      BIGINT UNSIGNED NULL,
        is_deleted      TINYINT(1) NOT NULL DEFAULT 0,
        template_id     BIGINT UNSIGNED NULL,
        title           VARCHAR(255) NOT NULL,
        description     TEXT NULL,
        location        VARCHAR(255) NULL,
        department_id   BIGINT UNSIGNED NULL,
        assigned_to     BIGINT UNSIGNED NOT NULL,
        status          ENUM('planned','in_progress','completed','cancelled') NOT NULL DEFAULT 'planned',
        scheduled_date  DATE NOT NULL,
        completed_date  DATE NULL,
        score           DECIMAL(5,2) NULL,
        notes           TEXT NULL,
        PRIMARY KEY (id),
        INDEX idx_hs_a_status (status),
        INDEX idx_hs_a_assigned (assigned_to),
        INDEX idx_hs_a_department (department_id),
        INDEX idx_hs_a_scheduled (scheduled_date),
        CONSTRAINT fk_hs_a_template FOREIGN KEY (template_id) REFERENCES hs_audit_templates(id),
        CONSTRAINT fk_hs_a_assigned FOREIGN KEY (assigned_to) REFERENCES users(id),
        CONSTRAINT fk_hs_a_department FOREIGN KEY (department_id) REFERENCES departments(id),
        CONSTRAINT fk_hs_a_created FOREIGN KEY (created_by) REFERENCES users(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS hs_audit_responses (
        id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        audit_id        BIGINT UNSIGNED NOT NULL,
        template_item_id BIGINT UNSIGNED NOT NULL,
        response        VARCHAR(50) NULL,
        comment         TEXT NULL,
        photo_path      VARCHAR(500) NULL,
        PRIMARY KEY (id),
        INDEX idx_hs_ar_audit (audit_id),
        CONSTRAINT fk_hs_ar_audit FOREIGN KEY (audit_id) REFERENCES hs_audits(id) ON DELETE CASCADE,
        CONSTRAINT fk_hs_ar_item FOREIGN KEY (template_item_id) REFERENCES hs_audit_template_items(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
};
