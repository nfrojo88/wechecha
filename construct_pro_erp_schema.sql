-- ============================================================
-- Construct-Pro ERP — Full Schema (Phase 1 + 2)
-- MariaDB / MySQL  |  utf8mb4_unicode_ci
-- Run this in phpMyAdmin or via mysql CLI:
--   mysql -u root construct_pro_erp < construct_pro_erp_schema.sql
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ---------------------------------------------------------------
-- Core Auth tables (Laravel defaults)
-- ---------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`              VARCHAR(255) NOT NULL,
  `email`             VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL,
  `password`          VARCHAR(255) NOT NULL,
  `is_active`         TINYINT(1) NOT NULL DEFAULT 1,
  `remember_token`    VARCHAR(100) NULL,
  `created_at`        TIMESTAMP NULL,
  `updated_at`        TIMESTAMP NULL,
  `deleted_at`        TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `password_resets` (
  `email`      VARCHAR(255) NOT NULL,
  `token`      VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL,
  INDEX `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid`        VARCHAR(255) NOT NULL,
  `connection`  TEXT NOT NULL,
  `queue`       TEXT NOT NULL,
  `payload`     LONGTEXT NOT NULL,
  `exception`   LONGTEXT NOT NULL,
  `failed_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` VARCHAR(255) NOT NULL,
  `tokenable_id`   BIGINT UNSIGNED NOT NULL,
  `name`           VARCHAR(255) NOT NULL,
  `token`          VARCHAR(64) NOT NULL,
  `abilities`      TEXT NULL,
  `last_used_at`   TIMESTAMP NULL,
  `created_at`     TIMESTAMP NULL,
  `updated_at`     TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  INDEX `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Spatie Laravel Permission tables
-- ---------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `permissions` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(255) NOT NULL,
  `guard_name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`, `guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `roles` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(255) NOT NULL,
  `guard_name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`, `guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` BIGINT UNSIGNED NOT NULL,
  `model_type`    VARCHAR(255) NOT NULL,
  `model_id`      BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `model_id`, `model_type`),
  INDEX `model_has_permissions_model_id_model_type_index` (`model_id`, `model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign`
    FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id`    BIGINT UNSIGNED NOT NULL,
  `model_type` VARCHAR(255) NOT NULL,
  `model_id`   BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `model_id`, `model_type`),
  INDEX `model_has_roles_model_id_model_type_index` (`model_id`, `model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign`
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` BIGINT UNSIGNED NOT NULL,
  `role_id`       BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign`
    FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign`
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Laravel migrations tracker
-- ---------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `migrations` (
  `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch`     INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2014_10_12_000000_create_users_table', 1),
('2014_10_12_100000_create_password_resets_table', 1),
('2019_08_19_000000_create_failed_jobs_table', 1),
('2019_12_14_000001_create_personal_access_tokens_table', 1),
('2026_07_01_082934_create_permission_tables', 1),
('2014_10_12_000001_create_projects_table', 1),
('2014_10_12_000002_create_stores_table', 1),
('2014_10_12_000003_add_foreign_keys_to_users_and_projects', 1),
('2014_10_12_000004_create_products_table', 1),
('2014_10_12_000005_create_inventory_table', 1),
('2014_10_12_000006_create_inventory_movements_table', 1);

-- ---------------------------------------------------------------
-- Core ERP tables
-- ---------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `projects` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`             VARCHAR(255) NOT NULL,
  `code`             VARCHAR(50) NOT NULL,
  `description`      TEXT NULL,
  `location`         VARCHAR(255) NULL,
  `client_name`      VARCHAR(255) NULL,
  `client_contact`   VARCHAR(255) NULL,
  `status`           ENUM('planning','bidding','active','on_hold','completed','cancelled','handover')
                       NOT NULL DEFAULT 'planning',
  `start_date`       DATE NULL,
  `end_date`         DATE NULL,
  `contract_value`   DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `budget_allocated` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `budget_consumed`  DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `created_by`       BIGINT UNSIGNED NOT NULL,
  `created_at`       TIMESTAMP NULL,
  `updated_at`       TIMESTAMP NULL,
  `deleted_at`       TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `projects_code_unique` (`code`),
  INDEX `projects_status_index` (`status`),
  CONSTRAINT `projects_created_by_foreign`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `stores` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(255) NOT NULL,
  `code`       VARCHAR(50) NOT NULL,
  `address`    TEXT NULL,
  `type`       VARCHAR(20) NOT NULL DEFAULT 'site',
  `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
  `project_id` BIGINT UNSIGNED NULL,
  `manager_id` BIGINT UNSIGNED NULL,
  `notes`      TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `deleted_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stores_code_unique` (`code`),
  CONSTRAINT `stores_project_id_foreign`
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stores_manager_id_foreign`
    FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add circular foreign keys
ALTER TABLE `users`
  ADD COLUMN `store_id` BIGINT UNSIGNED NULL AFTER `is_active`,
  ADD CONSTRAINT `users_store_id_foreign`
    FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL;

ALTER TABLE `projects`
  ADD COLUMN `default_store_id` BIGINT UNSIGNED NULL AFTER `budget_consumed`,
  ADD CONSTRAINT `projects_default_store_id_foreign`
    FOREIGN KEY (`default_store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS `products` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`             VARCHAR(255) NOT NULL,
  `code`             VARCHAR(50) NOT NULL,
  `category`         VARCHAR(100) NOT NULL,
  `unit`             VARCHAR(20) NOT NULL,
  `description`      TEXT NULL,
  `specification`    VARCHAR(500) NULL,
  `standard_cost`    DECIMAL(15,2) NULL,
  `current_cost`     DECIMAL(15,2) NULL,
  `selling_price`    DECIMAL(15,2) NULL,
  `min_stock_level`  DECIMAL(15,3) NOT NULL DEFAULT 0.000,
  `max_stock_level`  DECIMAL(15,3) NULL,
  `reorder_level`    DECIMAL(15,3) NOT NULL DEFAULT 0.000,
  `is_active`        TINYINT(1) NOT NULL DEFAULT 1,
  `product_type`     VARCHAR(20) NOT NULL DEFAULT 'material',
  `properties`       JSON NULL,
  `created_at`       TIMESTAMP NULL,
  `updated_at`       TIMESTAMP NULL,
  `deleted_at`       TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_code_unique` (`code`),
  INDEX `products_category_is_active_index` (`category`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `inventory` (
  `id`                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `store_id`           BIGINT UNSIGNED NOT NULL,
  `product_id`         BIGINT UNSIGNED NOT NULL,
  `quantity_on_hand`   DECIMAL(15,3) NOT NULL DEFAULT 0.000,
  `quantity_reserved`  DECIMAL(15,3) NOT NULL DEFAULT 0.000,
  `quantity_available` DECIMAL(15,3) AS (`quantity_on_hand` - `quantity_reserved`) STORED,
  `unit_cost`          DECIMAL(15,2) NULL,
  `total_value`        DECIMAL(15,2) AS (`quantity_on_hand` * COALESCE(`unit_cost`, 0)) STORED,
  `min_stock`          DECIMAL(15,3) NOT NULL DEFAULT 0.000,
  `last_movement_at`   TIMESTAMP NULL,
  `created_at`         TIMESTAMP NULL,
  `updated_at`         TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_store_product_unique` (`store_id`, `product_id`),
  INDEX `inventory_quantity_on_hand_index` (`quantity_on_hand`),
  CONSTRAINT `inventory_store_id_foreign`
    FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_product_id_foreign`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `inventory_movements` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `inventory_id`   BIGINT UNSIGNED NOT NULL,
  `type`           VARCHAR(20) NOT NULL,
  `quantity`       DECIMAL(15,3) NOT NULL,
  `reference_type` VARCHAR(255) NULL,
  `reference_id`   BIGINT UNSIGNED NULL,
  `performed_by`   BIGINT UNSIGNED NOT NULL,
  `remarks`        TEXT NULL,
  `created_at`     TIMESTAMP NULL,
  `updated_at`     TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `inventory_movements_type_index` (`type`),
  CONSTRAINT `inventory_movements_inventory_id_foreign`
    FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_movements_performed_by_foreign`
    FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- Seed: Roles & Permissions
-- ============================================================

INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES
('users.view','web',NOW(),NOW()),('users.create','web',NOW(),NOW()),('users.edit','web',NOW(),NOW()),('users.delete','web',NOW(),NOW()),
('stores.view','web',NOW(),NOW()),('stores.create','web',NOW(),NOW()),('stores.edit','web',NOW(),NOW()),('stores.delete','web',NOW(),NOW()),
('projects.view','web',NOW(),NOW()),('projects.create','web',NOW(),NOW()),('projects.edit','web',NOW(),NOW()),('projects.delete','web',NOW(),NOW()),
('products.view','web',NOW(),NOW()),('products.create','web',NOW(),NOW()),('products.edit','web',NOW(),NOW()),('products.delete','web',NOW(),NOW()),
('inventory.view','web',NOW(),NOW()),('inventory.create','web',NOW(),NOW()),('inventory.edit','web',NOW(),NOW()),('inventory.delete','web',NOW(),NOW()),
('schedule.view','web',NOW(),NOW()),('schedule.create','web',NOW(),NOW()),('schedule.edit','web',NOW(),NOW()),('schedule.delete','web',NOW(),NOW()),('schedule.approve','web',NOW(),NOW()),
('boq.view','web',NOW(),NOW()),('boq.create','web',NOW(),NOW()),('boq.edit','web',NOW(),NOW()),('boq.delete','web',NOW(),NOW()),('boq.approve','web',NOW(),NOW()),
('material_requests.view','web',NOW(),NOW()),('material_requests.create','web',NOW(),NOW()),('material_requests.edit','web',NOW(),NOW()),('material_requests.delete','web',NOW(),NOW()),('material_requests.approve','web',NOW(),NOW()),
('material_transfers.view','web',NOW(),NOW()),('material_transfers.create','web',NOW(),NOW()),('material_transfers.approve','web',NOW(),NOW()),
('purchases.view','web',NOW(),NOW()),('purchases.create','web',NOW(),NOW()),('purchases.edit','web',NOW(),NOW()),('purchases.delete','web',NOW(),NOW()),('purchases.approve','web',NOW(),NOW()),('purchases.receive','web',NOW(),NOW()),
('finance.view','web',NOW(),NOW()),('finance.create','web',NOW(),NOW()),('finance.edit','web',NOW(),NOW()),('finance.delete','web',NOW(),NOW()),('finance.approve','web',NOW(),NOW()),
('chart_of_accounts.view','web',NOW(),NOW()),('chart_of_accounts.create','web',NOW(),NOW()),('chart_of_accounts.edit','web',NOW(),NOW()),
('expenses.view','web',NOW(),NOW()),('expenses.create','web',NOW(),NOW()),('expenses.edit','web',NOW(),NOW()),('expenses.approve','web',NOW(),NOW()),
('banking.view','web',NOW(),NOW()),('banking.create','web',NOW(),NOW()),('banking.edit','web',NOW(),NOW()),
('hr.view','web',NOW(),NOW()),('hr.create','web',NOW(),NOW()),('hr.edit','web',NOW(),NOW()),('hr.delete','web',NOW(),NOW()),
('attendance.view','web',NOW(),NOW()),('attendance.create','web',NOW(),NOW()),('attendance.edit','web',NOW(),NOW()),
('payroll.view','web',NOW(),NOW()),('payroll.create','web',NOW(),NOW()),('payroll.approve','web',NOW(),NOW()),
('subcon.view','web',NOW(),NOW()),('subcon.create','web',NOW(),NOW()),('subcon.edit','web',NOW(),NOW()),('subcon.delete','web',NOW(),NOW()),('subcon.approve','web',NOW(),NOW()),
('bidding.view','web',NOW(),NOW()),('bidding.create','web',NOW(),NOW()),('bidding.edit','web',NOW(),NOW()),('bidding.delete','web',NOW(),NOW()),
('equipment.view','web',NOW(),NOW()),('equipment.create','web',NOW(),NOW()),('equipment.edit','web',NOW(),NOW()),('equipment.delete','web',NOW(),NOW()),
('reports.view','web',NOW(),NOW()),('reports.export','web',NOW(),NOW()),
('audit.view','web',NOW(),NOW()),
('planning.view','web',NOW(),NOW());

-- Roles
INSERT INTO `roles` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES
('global_admin','web',NOW(),NOW()),
('admin','web',NOW(),NOW()),
('gm','web',NOW(),NOW()),
('planning_manager','web',NOW(),NOW()),
('planning','web',NOW(),NOW()),
('technical_manager','web',NOW(),NOW()),
('site_engineer','web',NOW(),NOW()),
('foreman','web',NOW(),NOW()),
('store_manager','web',NOW(),NOW()),
('store_keeper','web',NOW(),NOW()),
('finance_head','web',NOW(),NOW()),
('finance','web',NOW(),NOW()),
('purchase_manager','web',NOW(),NOW()),
('purchase','web',NOW(),NOW()),
('market_research','web',NOW(),NOW()),
('hr','web',NOW(),NOW()),
('hr_officer','web',NOW(),NOW()),
('coordinator','web',NOW(),NOW()),
('contract_admin','web',NOW(),NOW()),
('bid_team','web',NOW(),NOW()),
('law','web',NOW(),NOW()),
('marketing','web',NOW(),NOW()),
('audit_team','web',NOW(),NOW()),
('secretary','web',NOW(),NOW()),
('general_service','web',NOW(),NOW());

-- Grant global_admin ALL permissions
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.id, r.id FROM `permissions` p, `roles` r WHERE r.name = 'global_admin';

-- Admin seeder user (password: Admin@1234!)
-- bcrypt hash of 'Admin@1234!'
INSERT INTO `users` (`name`, `email`, `password`, `is_active`, `created_at`, `updated_at`) VALUES
('System Administrator', 'admin@constructpro.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password (change via tinker)
 1, NOW(), NOW());

-- Assign global_admin role to first user
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`)
SELECT r.id, 'App\\Models\\User', 1 FROM `roles` r WHERE r.name = 'global_admin';
