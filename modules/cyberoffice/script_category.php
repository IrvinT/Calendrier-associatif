<?php
/**
 *	CyberOffice
 *
 *  @author    LVSinformatique <contact@lvsinformatique.com>
 *  @copyright 2014 LVSInformatique
 *	@license
 *  @version   1.2.32
 */

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

if (version_compare(_PS_VERSION_, '1.5', '>'))
{
	$categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'category c 
		INNER JOIN '._DB_PREFIX_.'category_shop category_shop ON (category_shop.id_category = c.id_category AND category_shop.id_shop = '.$id_shop.') 
		LEFT JOIN '._DB_PREFIX_.'category_lang cl ON c.id_category = cl.id_category AND cl.id_shop = '.$id_shop.' 
		WHERE 1 AND id_lang = '.(int)Configuration::get('CYBEROFFICE_LANG', null, null, $id_shop).' 
		ORDER BY c.id_parent ASC, c.id_category ASC');
}
else
	$categories = Category::getCategories((int)Configuration::get('CYBEROFFICE_LANG'), false, false);/*false,false tout , true,false actif*/

/*echo "<pre>".print_r($categories )."</pre>";die();*/

$cats = array();
$cats_id = array();

if ($categories)
{
		foreach ($categories as $category)
		{
				$link = new Link;
				$image0 = $get_cyber_protocole.'://'.Tools::getShopDomain(false).$link->getCatImageLink($category['link_rewrite'], $category['id_category'], 'category');
				$ext = preg_match('/(\.gif|\.jpg|\.jpeg|\.png|\.bmp)$/i', $image0, $reg);
				$image = $get_cyber_protocole.'://'.Tools::getShopDomain(false)._PS_IMG_.'c/'.(int)$category['id_category'].$reg[1];

				/*echo "<pre>".print_r($category)."</pre>";die();*/
				$cats[] = array(	'id'			=>	$category['id_category'],
									'id_mere'		=>	$category['id_parent'],
									'label'			=>	Tools::htmlentitiesUTF8($category['name']),
									'description'	=>	strip_tags($category['description']),
									'image'			=>  $image
									);
		}
}

/*echo "<pre>".print_r($cats)."</pre>";die();*/

/* Set the WebService URL*/
$ws_dol_url = $get_cyber_path.'cyberoffice/server_categorie.php';
$ws_method  = 'Create';
$ns = $get_cyber_path.'ns/';

$soapclient = new nusoap_client($ws_dol_url);
if ($soapclient)
	$soapclient->soap_defencoding = 'UTF-8';

$soapclient2 = new nusoap_client($ws_dol_url);
if ($soapclient2)
	$soapclient2->soap_defencoding = 'UTF-8';

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

/*echo "<pre>".print_r($authentication)."</pre>";*/

$cats100 = array_chunk($cats, 50, true);
$i = 0;
header('Content-type: text/html; charset=utf8');
foreach ($cats100 as $cat100)
{
	$parameters = array('authentication'=>$authentication, $cat100);
	$i++;
	$result = $soapclient->call($ws_method, $parameters, $ns, '');
	if ($soapclient->fault)
	{
			echo '<h2>Erreur SOAP on batch'.$i.'</h2>';
			print_r($result);
	}
	else
	{
			$err_msg = $soapclient->getError();
			if ($err_msg)
			{
				echo '<h2>Erreur SOAP on batch'.$i.'</h2> '.$err_msg;
				print '<br/>'.$soapclient->error_str;
				echo '<pre>'.htmlspecialchars($soapclient->response, ENT_QUOTES).'</pre>';
				var_dump($soapclient);
			}
			else
			{
				print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'."\n";
				echo '<html>'."\n";
				echo '<head>';
				echo '<title>WebService : '.$ws_method.'</title>';
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
	/*
	if (! $result)
	{
		var_dump($soapclient);
		print '<h2>Erreur SOAP </h2>'.$soapclient->error_str;
		exit;
	}

	print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'."\n";
	echo '<html>'."\n";
	echo '<head>';
	echo '<title>WebService Test: '.$ws_method.'</title>';
	echo '</head>'."\n";
	echo '<h2>Response batch'.$i.' : sent</h2>';
	echo '<h4>Result</h4>';
	echo '<pre>';
	print_r($result);
	echo '</pre>';
	echo '</body>'."\n";
	echo '</html>'."\n";
	*/
}
?>