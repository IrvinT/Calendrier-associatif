# Table : cart_rule - precheck
SELECT count(*) INTO @exist
FROM information_schema.columns 
WHERE table_schema = '_DB_NAME_'
    AND table_name = '_DB_PREFIX_cart_rule'
    AND column_name = 'is_fee';

SET @query = IF(
    @exist <= 0,
    "ALTER TABLE `_DB_PREFIX_cart_rule` ADD COLUMN `is_fee` INT(10) UNSIGNED NOT NULL DEFAULT '0'", 
    "SELECT 'column is_fee exists' status"
);

PREPARE stmt FROM @query;
EXECUTE stmt;

# Table : cart_rule								
ALTER TABLE `_DB_PREFIX_cart_rule` ADD COLUMN `payment_restriction` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `_DB_PREFIX_cart_rule` ADD COLUMN `dimension_restriction` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `_DB_PREFIX_cart_rule` ADD COLUMN `zipcode_restriction` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `_DB_PREFIX_cart_rule` ADD COLUMN `maximum_amount` DECIMAL(17,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `_DB_PREFIX_cart_rule` ADD COLUMN `maximum_amount_tax` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `_DB_PREFIX_cart_rule` ADD COLUMN `maximum_amount_currency` INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `_DB_PREFIX_cart_rule` ADD COLUMN `maximum_amount_shipping` TINYINT(1) NOT NULL DEFAULT '0';

# Table : cart_rule_payment
CREATE TABLE `_DB_PREFIX_cart_rule_payment` (
    `id_cart_rule` INT(10) UNSIGNED NOT NULL,
    `id_module` INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_cart_rule`, `id_module`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=UTF8;

# Table : cart_rule_dimension_rule_group
CREATE TABLE `_DB_PREFIX_cart_rule_dimension_rule_group` (
    `id_dimension_rule_group` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_cart_rule` INT(10) UNSIGNED NOT NULL,
    `base` VARCHAR(32) NOT NULL,
    PRIMARY KEY (`id_dimension_rule_group`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=UTF8;

# Table : cart_rule_dimension_rule
CREATE TABLE `_DB_PREFIX_cart_rule_dimension_rule` (
    `id_dimension_rule` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_dimension_rule_group` INT(10) UNSIGNED NOT NULL,
    `type` VARCHAR(32) NOT NULL,
    `operator` CHAR(5) NOT NULL,
    `value` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id_dimension_rule`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=UTF8;

# Table : cart_rule_zipcode_rule_group
CREATE TABLE `_DB_PREFIX_cart_rule_zipcode_rule_group` (
    `id_zipcode_rule_group` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_cart_rule` INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_zipcode_rule_group`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=UTF8;

# Table : cart_rule_zipcode_rule
CREATE TABLE `_DB_PREFIX_cart_rule_zipcode_rule` (
    `id_zipcode_rule` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_zipcode_rule_group` INT(10) UNSIGNED NOT NULL,
    `type` VARCHAR(32) NOT NULL,
    `operator` CHAR(5) NOT NULL,
    `value` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id_zipcode_rule`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=UTF8;