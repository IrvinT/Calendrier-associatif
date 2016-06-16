<?php

$sql = array();

$sql[] = 'INSERT INTO `'._DB_PREFIX_.'homcatplus` (
					`id_shop`, `nbr`, `nbr_sub`,  `nbr_size`,  `nbr_size_h`,  `nbr_size_p`, `nbr_size_l`, `nbr_marg_p`, `nbr_mw`, `product`, `product_odr`, `product_btn`, `product_price`, `product_b`, `product_bc`, `cat`, `cat_img`,`subcat_img`, `blockb`, `blockbc`, `sub1`,  `sub2`,  `sub3`, `sub4`, `sub5`, `sub6`, `sub7`, `sub8`, `sub9`, `sub10`, `sub11`, `sub12`, `sub13`, `sub14`, `sub15`, `sub16`, `sub17`, `sub18`, `sub19`, `sub20`, `sub21`, `sub22`, `sub23`, `sub24`, `sub25`
			) VALUES (
				\''.$id_shop.'\',
				\''.$nbr.'\',
				\''.$nbr_sub.'\',
				\''.$nbr_size.'\',
				\''.$nbr_size_h.'\',
				\''.$nbr_size_p.'\',
				\''.$nbr_size_l.'\',
				\''.$nbr_marg_p.'\',
				\''.$nbr_mw.'\',
				\''.$product.'\',
				\''.$product_odr.'\',
				\''.$product_btn.'\',
				\''.$product_price.'\',
				\''.$product_b.'\',
				\''.$product_bc.'\',
				\''.$cat.'\',
				\''.$cat_img.'\',
				\''.$subcat_img.'\',
				\''.$blockb.'\',
				\''.$blockbc.'\',
				\''.$sub1.'\',
				\''.$sub2.'\',
				\''.$sub3.'\',
				\''.$sub4.'\',
				\''.$sub5.'\',
				\''.$sub6.'\',
				\''.$sub7.'\',
				\''.$sub8.'\',
				\''.$sub9.'\',
				\''.$sub10.'\',
				\''.$sub11.'\',
				\''.$sub12.'\',
				\''.$sub13.'\',
				\''.$sub14.'\',
				\''.$sub15.'\',
				\''.$sub16.'\',
				\''.$sub17.'\',
				\''.$sub18.'\',
				\''.$sub19.'\',
				\''.$sub20.'\',
				\''.$sub21.'\',
				\''.$sub22.'\',
				\''.$sub23.'\',
				\''.$sub24.'\',
				\''.$sub25.'\')';

foreach ($sql as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;
