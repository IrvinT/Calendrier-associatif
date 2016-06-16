<?php
/**
 * Module opartdevis
 *
 * @category Prestashop
 * @category Module
 * @author    Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright Op'art
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 */

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_15_05_04($module)
{
	if (Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_.'opartdevis` LIKE \'name\'') == false)
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.$module->name.'` ADD `name` varchar(128) AFTER `id_customer`');

	if (Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_.'opartdevis` LIKE \'message_visible\'') == false)
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.$module->name.'` ADD `message_visible` TEXT AFTER `name`');

	if (Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_.'opartdevis` LIKE \'id_customer_thread\'') == false)
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.$module->name.'` ADD `id_customer_thread` int(10) AFTER `message_visible`');

	$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'opartdevis_text` (
		  `id_opartdevis_text` int(10) NOT NULL AUTO_INCREMENT,
		  `text_value` TEXT NOT NULL,
		  `text_type` int(10) NOT NULL,
                  `id_lang` int(10) NOT NULL,
  		PRIMARY KEY (`id_opartdevis_text`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

	Db::getInstance()->Execute($sql);
	return $module;
}
