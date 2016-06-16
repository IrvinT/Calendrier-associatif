# Disable fees						
UPDATE `_DB_PREFIX_cart_rule` SET `active` = 0 WHERE `is_fee` & 1;

# Table : cart_rule								
ALTER TABLE `_DB_PREFIX_cart_rule` DROP COLUMN `is_fee`;
ALTER TABLE `_DB_PREFIX_cart_rule` DROP COLUMN `payment_restriction`;
ALTER TABLE `_DB_PREFIX_cart_rule` DROP COLUMN `dimension_restriction`;
ALTER TABLE `_DB_PREFIX_cart_rule` DROP COLUMN `zipcode_restriction`;
ALTER TABLE `_DB_PREFIX_cart_rule` DROP COLUMN `maximum_amount`;
ALTER TABLE `_DB_PREFIX_cart_rule` DROP COLUMN `maximum_amount_tax`;
ALTER TABLE `_DB_PREFIX_cart_rule` DROP COLUMN `maximum_amount_currency`;
ALTER TABLE `_DB_PREFIX_cart_rule` DROP COLUMN `maximum_amount_shipping`;

# Table : cart_rule_payment
DROP TABLE `_DB_PREFIX_cart_rule_payment`;

# Table : cart_rule_dimension_rule_group
DROP TABLE `_DB_PREFIX_cart_rule_dimension_rule_group`;

# Table : cart_rule_dimension_rule
DROP TABLE `_DB_PREFIX_cart_rule_dimension_rule`;

# Table : cart_rule_zipcode_rule_group
DROP TABLE `_DB_PREFIX_cart_rule_zipcode_rule_group`;

# Table : cart_rule_zipcode_rule
DROP TABLE `_DB_PREFIX_cart_rule_zipcode_rule`;