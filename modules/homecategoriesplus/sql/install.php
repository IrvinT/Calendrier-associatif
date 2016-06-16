<?php

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'homcatplus` (
					`id_item` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`id_shop` int(10) unsigned NOT NULL,
					`nbr` VARCHAR(100),
					`nbr_sub` VARCHAR(100),
					`nbr_size` VARCHAR(100),
					`nbr_size_h` VARCHAR(100),
					`nbr_size_p` VARCHAR(100),
					`nbr_size_l` VARCHAR(100),
					`nbr_marg_p` VARCHAR(100),
					`nbr_mw` VARCHAR(100),
					`product` VARCHAR(100),
					`product_odr` VARCHAR(100),
					`product_btn` VARCHAR(100),
					`product_price` VARCHAR(100),
					`product_b` VARCHAR(100),
					`product_bc` VARCHAR(100),
					`cat` VARCHAR(100),
					`cat_img` VARCHAR(100),
					`subcat_img` VARCHAR(100),
					`blockb` VARCHAR(100),
					`blockbc` VARCHAR(100),
					`sub1` VARCHAR(100),
					`sub2` VARCHAR(100),
					`sub3` VARCHAR(100),
					`sub4` VARCHAR(100),
					`sub5` VARCHAR(100),
					`sub6` VARCHAR(100),
					`sub7` VARCHAR(100),
					`sub8` VARCHAR(100),
					`sub9` VARCHAR(100),
					`sub10` VARCHAR(100),
					`sub11` VARCHAR(100),
					`sub12` VARCHAR(100),
					`sub13` VARCHAR(100),
					`sub14` VARCHAR(100),
					`sub15` VARCHAR(100),
					`sub16` VARCHAR(100),
					`sub17` VARCHAR(100),
					`sub18` VARCHAR(100),
					`sub19` VARCHAR(100),
					`sub20` VARCHAR(100),
					`sub21` VARCHAR(100),
					`sub22` VARCHAR(100),
					`sub23` VARCHAR(100),
					`sub24` VARCHAR(100),
					`sub25` VARCHAR(100),
					PRIMARY KEY (`id_item`)
			) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;';

foreach ($sql as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;
