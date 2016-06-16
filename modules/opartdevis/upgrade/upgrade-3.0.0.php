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

function upgrade_module_3_0_0($module)
{
	if (Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_.'opartdevis` LIKE \'statut\'') == false)
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.$module->name.'` ADD `statut` int(2) DEFAULT 0 AFTER `date_add`');

	if (Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_.'opartdevis` LIKE \'id_order\'') == false)
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.$module->name.'` ADD `id_order` int(10) NULL AFTER `statut`');
	return $module;
}