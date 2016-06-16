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

$sql = array();
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'opartdevis` (
		  `id_opartdevis` int(10) NOT NULL AUTO_INCREMENT,
		  `id_cart` int(10) NOT NULL,
		  `id_customer` int(10) NOT NULL,
                  `name` varchar(128),
                  `message_visible` TEXT,
                  `id_customer_thread` int(10),
		  `date_add` DATETIME NOT NULL,
                  `statut` int(2) DEFAULT 0,
		  `id_order` int(10) NULL,
  		PRIMARY KEY (`id_opartdevis`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'opartdevis_text` (
		  `id_opartdevis_text` int(10) NOT NULL AUTO_INCREMENT,
		  `text_value` TEXT NOT NULL,
		  `text_type` int(10) NOT NULL,
                  `id_lang` int(10) NOT NULL,
  		PRIMARY KEY (`id_opartdevis_text`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
