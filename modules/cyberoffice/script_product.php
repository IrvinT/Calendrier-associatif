<?php
/**
 *	CyberOffice
 *
 *  @author    LVSinformatique <contact@lvsinformatique.com>
 *  @copyright 2014 LVSInformatique
 *	@license   NoLicence
 *  @version   1.2.32
 */

error_reporting(0);
require('../../config/config.inc.php');
require_once(dirname(__FILE__).'/nusoap/lib/nusoap.php');

$id_shop = Tools::getValue('shop');

$get_cyber_lang = version_compare(_PS_VERSION_, '1.5', '>') ? (int)Configuration::get('CYBEROFFICE_LANG', null, null, $id_shop) : (int)Configuration::get('CYBEROFFICE_LANG');
$get_cyber_key = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_key', null, null, $id_shop) : Configuration::get('CYBEROFFICE_key');
$get_cyber_path = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_path', null, null, $id_shop) : Configuration::get('CYBEROFFICE_path');
$get_cyber_login = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_login', null, null, $id_shop) : Configuration::get('CYBEROFFICE_login');
$get_cyber_pass = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_pass', null, null, $id_shop) : Configuration::get('CYBEROFFICE_pass');
$get_cyber_entity = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_entity', null, null, $id_shop) : Configuration::get('CYBEROFFICE_entity');
$get_cyber_warehouse = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_warehouse', null, null, $id_shop) : Configuration::get('CYBEROFFICE_warehouse');
$get_cyber_prefix = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_prefix', null, null, $id_shop) : Configuration::get('CYBEROFFICE_prefix');
$get_cyber_token = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_TOKEN', null, null, $id_shop) : Configuration::get('CYBEROFFICE_TOKEN');
$get_cyber_datefrom = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_dateFrom', null, null, $id_shop) : Configuration::get('CYBEROFFICE_dateFrom');
$get_cyber_dateto = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_dateTo', null, null, $id_shop) : Configuration::get('CYBEROFFICE_dateTo');
$get_cyber_protocole = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_protocole', null, null, $id_shop) : Configuration::get('CYBEROFFICE_protocole');
if (!$get_cyber_protocole) $get_cyber_protocole = 'http';


if (version_compare(_PS_VERSION_, '1.5', '>'))
		$token = Configuration::get('CYBEROFFICE_TOKEN', null, null, $id_shop)._COOKIE_KEY_;
else
		$token = Configuration::get('CYBEROFFICE_TOKEN')._COOKIE_KEY_;

if ((sha1($token)) != Tools::getValue('cyberoffice_token'))
	die('FATAL ERROR : INVALID TOKEN');

set_time_limit(3600);
ini_set('default_socket_timeout', 160);

$id_to   = Tools::getValue('idTo');
$id_from = Tools::getValue('idFrom');
$id_limit = $id_to - $id_from + 1;

if (version_compare(_PS_VERSION_, '1.5', '>'))
{
	/*$products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT p.*, product_shop.*, pl.* , m.name AS manufacturer_name, s.name AS supplier_name
		FROM '._DB_PREFIX_.'product p
		INNER JOIN '._DB_PREFIX_.'product_shop product_shop ON (product_shop.id_product = p.id_product AND product_shop.id_shop = '.$id_shop.')
		LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (p.id_product = pl.id_product AND pl.id_shop = '.$id_shop.' )
		LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
		LEFT JOIN '._DB_PREFIX_.'supplier s ON (s.id_supplier = p.id_supplier)
		WHERE pl.id_lang = '.(int)Configuration::get('CYBEROFFICE_LANG', null, null, $id_shop).' AND p.id_category_default IN (SELECT cs.id_category FROM '._DB_PREFIX_.'category_shop as cs WHERE cs.id_shop = '.$id_shop.') AND p.id_product between '.$id_from.' and '.$id_to.'
		ORDER BY p.id_product ASC ');*/
	$products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT DISTINCT p.*, product_shop.*, pl.* , m.name AS manufacturer_name, s.name AS supplier_name 
		FROM '._DB_PREFIX_.'product p 
		INNER JOIN '._DB_PREFIX_.'product_shop product_shop ON (product_shop.id_product = p.id_product AND product_shop.id_shop = '.$id_shop.') 
		LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (p.id_product = pl.id_product AND pl.id_shop = '.$id_shop.' ) 
		LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer) 
		LEFT JOIN '._DB_PREFIX_.'supplier s ON (s.id_supplier = p.id_supplier)
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = p.id_product) 
		WHERE pl.id_lang = '.(int)Configuration::get('CYBEROFFICE_LANG', null, null, $id_shop).' AND p.id_product between '.(int)$id_from.' and '.(int)$id_to.'
		AND cp.id_category IN (SELECT cs.id_category FROM '._DB_PREFIX_.'category_shop as cs WHERE cs.id_shop = '.(int)$id_shop.') 
		ORDER BY p.id_product ASC ');
}
else
	$products = Product::getProducts((int)Configuration::get('CYBEROFFICE_LANG'), $id_from, $id_limit, 'id_product', 'ASC');

/*echo "<pre>".print_r($products)."</pre>";die();*/

$cats = array();
$cats_id = array();
$pictures = array();
$compte = 0;
$lang = $get_cyber_lang;
if ($products)
{
		foreach ($products as $product)
		{
				/*echo "<pre>".print_r($product)."</pre>";die();*/
				$link = new Link();
				$prefix = $get_cyber_protocole.'://';
				/*$productI = new Product((int)(Configuration::get('CYBEROFFICE_LANG')));
				$productI->id=$product['id_product'];*/
				$product_i = new Product((int)$product['id_product']);
				/*echo _PS_BASE_URL_."<pre>".print_r($productI)."</pre>";die();*/
				/*****Load Variations*****/
				/*$variations = array();
				$variationsList = array();
				$combinations = $productI->getAttributeCombinaisons((int)(Configuration::get('CYBEROFFICE_LANG')));
				$combinations2 = $productI->getCombinations((int)$product['id_product'], (int)(Configuration::get('CYBEROFFICE_LANG')));*/
				/*echo "<pre>".print_r($combinations2)."</pre>";die();*/
				/*$attributes_groups = $product_i->getAttributeCombinations($get_cyber_lang);*/
				if (version_compare(_PS_VERSION_, '1.5', '>'))
					$attributes_groups = $product_i->getAttributeCombinations($get_cyber_lang);
				else
					$attributes_groups = $product_i->getAttributeCombinaisons($get_cyber_lang);

				/*echo "<pre>".print_r($attributesGroups )."</pre>";die();*/
				$combinations = array();
				if ($attributes_groups && is_array($attributes_groups))
				{
					$combination_images = $product_i->getCombinationImages($get_cyber_lang);
					foreach ($attributes_groups as $k => $row)
					{
						$combinations[$row['id_product_attribute']]['id_combination'] = $row['id_product_attribute'];
						$combinations[$row['id_product_attribute']]['attributes'][$row['id_attribute_group']] = array('name'=>$row['attribute_name'], 'group_name'=>$row['group_name'], 'id_attribute'=>(int)$row['id_attribute']);
						$combinations[$row['id_product_attribute']]['price'] = (float)$row['price'];
						$combinations[$row['id_product_attribute']]['ecotax'] = (float)$row['ecotax'];
						$combinations[$row['id_product_attribute']]['weight'] = (float)$row['weight'];
						$combinations[$row['id_product_attribute']]['quantity'] = (int)$row['quantity'];
						$combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
						$combinations[$row['id_product_attribute']]['ean13'] = $row['ean13'];
						$combinations[$row['id_product_attribute']]['upc'] = $row['upc'];
						if (isset($row['unit_price_impact']))
							$combinations[$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
						$combinations[$row['id_product_attribute']]['id_image'] = isset($combination_images[$row['id_product_attribute']][0]['id_image']) ? $combination_images[$row['id_product_attribute']][0]['id_image'] : 0;
						$combinations[$row['id_product_attribute']]['wholesale_price'] = $row['wholesale_price'];
					}
				}
				/*echo "<pre>".print_r($combinations)."</pre>";die();*/
				$myproduct = array();
				$nb_combinations = 0;
				foreach ($combinations as $combination)
				{
					$nb_combinations = 1;
					$str_features = array();
					$model = array();
					$lang = $get_cyber_lang;
					if (isset($combination['attributes']))
					{
						foreach ($combination['attributes'] as $attribut)
						{
							$str_features[] = $attribut['group_name'].' : '.$attribut['name'];
							$model[] = $attribut['name'];
						}
					}
					$myproduct['product_url'] = $link->getProductLink((int)$product_i->id);/*, $productI->link_rewrite[$lang], $productI->ean13, $lang);*/
					$myproduct['designation'] = Tools::htmlentitiesUTF8($product_i->name[$lang].' '.implode(' ', $model));
					$myproduct['manufacturer'] = Manufacturer::getNameById($product_i->id_manufacturer);
					$myproduct['id_manufacturer'] = $product_i->id_manufacturer;
					/*$price = $productI->getPrice(true, (isset($combination['id_combination']) ? $combination['id_combination'] : NULL), 2);
					$price = Product::getPriceStatic((int)$productI->id, false, (int)$combination['id_combination']) + Product::getPriceStatic($productI->id, false, (int)$combination['id_combination'], 6, NULL, true);	*/
					$myproduct['price'] = $product_i->price + $combination['price'];
					$category = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
					SELECT cp.`id_category` as id
					FROM `'._DB_PREFIX_.'category_product` cp
					WHERE cp.`id_product` = '.(int)$product['id_product'].'
					ORDER BY `position` ASC');
					$categories = array();
					if (isset($category))
						foreach ($category as $categorie)
							$categories[] = $categorie['id'];
					$myproduct['category'] = implode('-', $categories);
					$id_image = (isset($combination['id_image']) && $combination['id_image'] > 0) ? $combination['id_image'] : 0;
					if ($id_image === 0)
					{
						$image = $product_i->getCover((int)$product_i->id);
						$id_image = $image['id_image'];
					}
					/*$image = $productI->getCover((int)$productI->id);
					if ($productI->id==102) {echo "<pre>".print_r( $image )."</pre>";die();}*/
					$myproduct['image_url'] = $link->getImageLink($product_i->link_rewrite[$lang], $product_i->id.'-'.$id_image);
					$myproduct['description_short'] = is_array($product_i->description_short) ? strip_tags($product_i->description_short[$lang]) : strip_tags($product_i->description_short);
					$myproduct['description_short'] = trim(strip_tags(implode(', ', $str_features)).'<br />'.$myproduct['description_short']);
					$myproduct['description'] = is_array($product_i->description) ? strip_tags($product_i->description[$lang]) : strip_tags($product_i->description);
					$myproduct['product_id'] = $product_i->id;
					$quantity = Product::getQuantity($product_i->id, (isset($combination['id_combination']) ? $combination['id_combination'] : null));
					$myproduct['quantity'] = $quantity;
					$myproduct['ean13'] = Tools::strlen((string)$combination['ean13']) == 13 ? $combination['ean13'] : '';
					$myproduct['upc'] = $combination['upc'];
					$myproduct['eco_tax'] = $product_i->ecotax;
					$myproduct['width'] = $product_i->width;
					$weight = null;
					if (isset($combination['weight']) && (float)$combination['weight'] != 0)
						$weight = $product_i->weight + (float)$combination['weight'];
					else if ($product_i->weight != 0)
						$weight = $product_i->weight;
					$myproduct['weight'] = $weight;
					$myproduct['active'] = $product_i->active;
					$tax = (float)Tax::getProductTaxRate((int)$product_i->id);
					$myproduct['tax_rate'] = $tax;
					if ($get_cyber_prefix == '{ref}')
						$myproduct['reference'] = $combination['reference'];
					else
						$myproduct['reference'] = (isset($combination['reference']) ? $combination['reference'] : $product_i->reference).'-'.$combination['id_combination'];
				/*}
				echo "<pre>".print_r($combination)."</pre>";die();
				*/
				$cats[] = array('id_product' 				=> $myproduct['product_id'].((isset($combination['id_combination']) && $combination['id_combination'] > 0) ? '-'.$combination['id_combination'] : ''),
								'ean13' 					=> $combination['ean13'],
								'upc' 						=> $combination['upc'],
								'price' 					=> $myproduct['price'],
								'width' 					=> $myproduct['width'],
								'weight' 					=> $myproduct['weight'],
								'description' 				=> $myproduct['description'],
								'description_short' 		=> $myproduct['description_short'],
								'name' 						=> $myproduct['designation'],
								'tax_rate' 					=> $myproduct['tax_rate'],
								'reference'					=> ($get_cyber_prefix == '{ref}'?$myproduct['reference']:($get_cyber_prefix.$myproduct['product_id'].((isset($combination['id_combination']) && $combination['id_combination'] > 0) ? '-'.$combination['id_combination'] : ''))),
								'active'					=> $myproduct['active'],
								'quantity'					=> $myproduct['quantity'],
								'warehouse'					=> $get_cyber_warehouse,
								'image'						=> Tools::substr($myproduct['image_url'], 0, 4) == 'http'?$myproduct['image_url']:$get_cyber_protocole.'://'.$myproduct['image_url'],
								'category'					=> $myproduct['category'],
								'product_url'				=> $myproduct['product_url'],
								'manufacturer'				=> $myproduct['manufacturer'],
								'id_manufacturer'			=> $myproduct['id_manufacturer'],
								'eco_tax'					=> $myproduct['eco_tax'],
								'match'						=> $get_cyber_prefix,
								'wholesale_price'			=> ($combination['wholesale_price'] == 0?$product_i->wholesale_price:$combination['wholesale_price'])
								);
				}
				if ($nb_combinations == 0)
				{
					$myproduct['product_url'] = $link->getProductLink((int)$product_i->id);/*, $productI->link_rewrite[$lang], $productI->ean13, $lang);*/
					$myproduct['designation'] = Tools::htmlentitiesUTF8($product_i->name[$lang]);
					$myproduct['manufacturer'] = Manufacturer::getNameById($product_i->id_manufacturer);
					$myproduct['id_manufacturer'] = $product_i->id_manufacturer;
					/*$price = Product::getPriceStatic((int)$productI->id, false);	*/
					$myproduct['price'] = $product_i->price;
					$category = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
					SELECT cp.`id_category` as id
					FROM `'._DB_PREFIX_.'category_product` cp
					WHERE cp.`id_product` = '.(int)$product['id_product'].'
					ORDER BY `position` ASC');
					$categories = array();
					if (isset($category))
						foreach ($category as $categorie)
							$categories[] = $categorie['id'];
					$myproduct['category'] = implode('-', $categories);
					$image = $product_i->getCover((int)$product_i->id);
					$id_image = $image['id_image'];
					$myproduct['image_url'] = $link->getImageLink($product_i->link_rewrite[$lang], $product_i->id.'-'.$id_image);
					$myproduct['description_short'] = is_array($product_i->description_short) ? strip_tags($product_i->description_short[$lang]) : strip_tags($product_i->description_short);
					$myproduct['description_short'] = trim($myproduct['description_short']);
					$myproduct['description'] = is_array($product_i->description) ? strip_tags($product_i->description[$lang]) : strip_tags($product_i->description);
					$myproduct['description'] = trim($myproduct['description']);
					$myproduct['product_id'] = $product_i->id;
					$quantity = Product::getQuantity($product_i->id, null);
					$myproduct['quantity'] = $quantity;
					$myproduct['ean13'] = Tools::strlen((string)$product_i->ean13) == 13 ? $product_i->ean13 : '';
					$myproduct['upc'] = $product_i->upc;
					$myproduct['eco_tax'] = $product_i->ecotax;
					$myproduct['width'] = $product_i->width;
					$weight = $product_i->weight;
					$myproduct['weight'] = $weight;
					$myproduct['active'] = $product_i->active;
					$tax = (float)Tax::getProductTaxRate((int)$product_i->id);
					$myproduct['tax_rate'] = $tax;
					$myproduct['reference'] = $product_i->reference;/*(isset($productI->reference)?$productI->reference.'-':'').(int)$productI->id;*/
					$cats[] = array(
								'id_product' 				=> $myproduct['product_id'],
								'ean13' 					=> $myproduct['ean13'],
								'upc' 						=> $myproduct['upc'],
								'price' 					=> $myproduct['price'],
								'width' 					=> $myproduct['width'],
								'weight' 					=> $myproduct['weight'],
								'description' 				=> $myproduct['description'],
								'description_short' 		=> $myproduct['description_short'],
								'name' 						=> $myproduct['designation'],
								'tax_rate' 					=> $myproduct['tax_rate'],
								'reference'					=> ($get_cyber_prefix == '{ref}'?$myproduct['reference']:$get_cyber_prefix.$myproduct['product_id']),
								'active'					=> $myproduct['active'],
								'quantity'					=> $myproduct['quantity'],
								'warehouse'					=> $get_cyber_warehouse,
								'image'						=> Tools::substr($myproduct['image_url'], 0, 4) == 'http'?$myproduct['image_url']:$get_cyber_protocole.'://'.$myproduct['image_url'],
								'category'					=> $myproduct['category'],
								'product_url'				=> $myproduct['product_url'],
								'manufacturer'				=> $myproduct['manufacturer'],
								'id_manufacturer'			=> $myproduct['id_manufacturer'],
								'eco_tax'					=> $myproduct['eco_tax'],
								'match'						=> $get_cyber_prefix,
								'wholesale_price'			=> $product_i->wholesale_price
								);
				}
		$compte++;
		/*if ($compte > 0) break;*/
		}
}
		else echo '==> aucun produit à transférer';

/*echo "<pre>".print $compte."</pre>";die();*/


/* Set the WebService URL*/
$ws_dol_url = $get_cyber_path.'cyberoffice/server_product.php';
$ws_method  = 'Create';
$ns = $get_cyber_path.'ns/';
/*$client = new SoapClient($wsdl, array("connection_timeout"=>15));
$soapclient = new nusoap_client($ws_dol_url);*/
$soapclient = new nusoap_client($ws_dol_url);
if ($soapclient)
	$soapclient->soap_defencoding = 'UTF-8';
/*
$soapclient2 = new nusoap_client($ws_dol_url);//new nusoap_client($ws_dol_url, array("connection_timeout"=>15));
if ($soapclient2)
{
	$soapclient2->soap_defencoding='UTF-8';
}
*/

/* Call the WebService method and store its result in $result.*/
$title = '';
		if (version_compare(_PS_VERSION_, '1.5', '>'))
		{
			$current_shop = new Shop($id_shop);
			$urls = $current_shop->getUrls();

			foreach ($urls as $key_url => &$url)
				$title = $get_cyber_protocole.'://'.$url['domain'].'/'.$url['virtual_uri'];
		}
		else
			$title = $get_cyber_protocole.'://'.Tools::getShopDomain(false, true).__PS_BASE_URI__;

$authentication = array(
	'dolibarrkey'		=>	htmlentities($get_cyber_key, ENT_COMPAT, 'UTF-8'),
	'sourceapplication'	=>	'LVSInformatique',
	'login'				=>	$get_cyber_login,
	'password'			=>	$get_cyber_pass,
	'entity'			=>	$get_cyber_entity,
	'myurl'				=>	$title
	);
/*echo "<pre>".print_r($param)."</pre>";die();
print_r(array_chunk($input_array, 2, true));*/
$cats100 = array_chunk($cats, 100, true);
$i = 0;
header('Content-type: text/html; charset=utf8');
foreach ($cats100 as $cat100)
{
	$parameters = array('authentication'=>$authentication, $cat100);
	$i++;
	$result = $soapclient->call($ws_method, $parameters, $ns, '');
	if ($soapclient->fault)
	{
			echo '<h2>Erreur SOAP1 </h2>';
			print_r($result);
	}
	else
	{
			$err_msg = $soapclient->getError();
			if ($err_msg)
			{
				echo '<h2>Erreur SOAP2 </h2>'.$err_msg;
				print '<br/>'.$soapclient->error_str;
				echo '<h2>response </h2>';
				echo '<pre>'.htmlspecialchars($soapclient->response, ENT_QUOTES).'</pre>';
				echo '<h2>request </h2>';
				echo '<pre>'.htmlspecialchars($soapclient->request, ENT_QUOTES).'</pre>';
				var_dump($soapclient);
			}
			else
			{
				print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'."\n";
				echo '<html>'."\n";
				echo '<head>';
				echo '<title>WebService : '.$ws_method.'</title>';
				/*echo '<pre>' . htmlspecialchars($soapclient->response, ENT_QUOTES) . '</pre>';*/
				echo '</head>'."\n";
				echo '<body>'."\n";
				echo '<h2>Response batch'.$i.' : sent</h2>';
				echo '<h4>Result</h4>';
				echo '<pre>';
				print_r($result);
				echo '</pre>';
				echo '</body>'."\n";
				echo '</html>'."\n";
			}
	}
}
?>