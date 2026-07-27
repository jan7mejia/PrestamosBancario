-- -----------------------------------------------------
-- Base de Datos: prestamo_bancario
-- Compatible con MySQL Workbench
-- -----------------------------------------------------

CREATE SCHEMA IF NOT EXISTS `prestamo_bancario` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `prestamo_bancario`;

-- Tabla de Usuarios (Sistema Login)
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `users_email_unique` (`email` ASC)
) ENGINE = InnoDB;

-- Tabla de Clientes
CREATE TABLE IF NOT EXISTS `clients` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `ci` VARCHAR(20) NOT NULL,
  `phone` VARCHAR(20) NULL DEFAULT NULL,
  `address` TEXT NULL DEFAULT NULL,
  `latitude` DECIMAL(10, 8) NULL DEFAULT NULL,
  `longitude` DECIMAL(11, 8) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `clients_ci_unique` (`ci` ASC)
) ENGINE = InnoDB;

-- Tabla de Préstamos
CREATE TABLE IF NOT EXISTS `loans` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` BIGINT UNSIGNED NOT NULL,
  `amount` DECIMAL(15, 2) NOT NULL,
  `interest_rate` DECIMAL(5, 2) NOT NULL,
  `term_months` INT NOT NULL,
  `amortization_system` VARCHAR(50) NOT NULL DEFAULT 'frances',
  `status` VARCHAR(20) NOT NULL DEFAULT 'active',
  `start_date` DATE NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `loans_client_id_foreign` (`client_id` ASC),
  CONSTRAINT `fk_loans_clients`
    FOREIGN KEY (`client_id`)
    REFERENCES `clients` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- Tabla de Amortizaciones (Cuotas)
CREATE TABLE IF NOT EXISTS `amortizations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `loan_id` BIGINT UNSIGNED NOT NULL,
  `installment_number` INT NOT NULL,
  `due_date` DATE NOT NULL,
  `installment_amount` DECIMAL(15, 2) NOT NULL,
  `principal_amount` DECIMAL(15, 2) NOT NULL,
  `interest_amount` DECIMAL(15, 2) NOT NULL,
  `remaining_balance` DECIMAL(15, 2) NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `paid_at` DATETIME NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `amortizations_loan_id_foreign` (`loan_id` ASC),
  CONSTRAINT `fk_amortizations_loans`
    FOREIGN KEY (`loan_id`)
    REFERENCES `loans` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB;
