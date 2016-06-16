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

$date_from = $get_cyber_datefrom;
$date_to = $get_cyber_dateto;

/*$customers = Customer::getCustomers();*/

$customers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT cu.id_customer as cu_id_customer, cu.email as cu_email, cu.firstname as cu_firstname, cu.lastname as cu_lastname,cu.id_gender as cu_id_gender,cu.birthday as cu_birthday,
		a.*, s.name AS state, s.iso_code AS state_iso,c.iso_code as c_iso_code
		FROM '._DB_PREFIX_.'customer cu
		LEFT JOIN '._DB_PREFIX_.'address a ON (a.id_customer = cu.id_customer AND a.deleted = 0)
		LEFT JOIN '._DB_PREFIX_.'country c ON (a.id_country = c.id_country)
		LEFT JOIN '._DB_PREFIX_.'state s ON (s.id_state = a.id_state)
		WHERE DATE_ADD(cu.date_add, INTERVAL -1 DAY) <= \''.pSQL($date_to).'\' AND cu.date_add >= \''.pSQL($date_from).'\'
		ORDER BY cu.id_customer ASC');

/*echo "<pre>".print_r($customers)."</pre>";die();*/

$cats = array();
/*$catsId = array();*/
$compte = 0;

if ($customers)
{
		foreach ($customers as $customer)
		{
				/*echo "<pre>".print_r($customer)."</pre>";die();*/
				$cats[] = array('id_customer'	=> $customer['cu_id_customer'],
								'birthday' 		=> $customer['cu_birthday'],
								'email' 		=> $customer['cu_email'],
								'id_gender' 	=> $customer['cu_id_gender'],  /*1 homme, 2 femme 9 inconnu*/
								'firstname' 	=> $customer['cu_firstname'],
								'lastname' 		=> $customer['cu_lastname'],
								'id_address' 	=> $customer['id_address'],
								'company' 		=> $customer['company'],
								'address'		=> $customer['address1']."\r\n".$customer['address2'],
								'postcode'		=> $customer['postcode'],
								'city'			=> $customer['city'],
								'phone'			=> $customer['phone'],
								'phone_mobile'	=> $customer['phone_mobile'],
								'vat_number'	=> $customer['vat_number'],
								'state_iso'		=> $customer['state_iso'],
								'c_iso_code'	=> $customer['c_iso_code']
							);
		$compte++;
		/*if ($compte > 5) break;*/
		}
}
/*echo "<pre>".print_r($cats)."</pre>";die();*/

/* Set the WebService URL*/
$ws_dol_url = $get_cyber_path.'cyberoffice/server_customer.php';
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
			$current_shop = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));/*new Shop($id_shop);*/
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
	'myurl'				=>  $title
	);

/*echo "<pre>".print_r($param)."</pre>";die();*/
$cats100 = array_chunk($cats, 100, true);
$i = 0;
header('Content-type: text/html; charset=utf8');
foreach ($cats100 as $cat100)
{
	$parameters = array('authentication'=>$authentication, $cat100);
	$i++;
	$result = $soapclient->call($ws_method, $parameters, $ns, '');
	if (! $result)
	{
		var_dump($soapclient);
		print '<h2 Erreur SOAP </h2>'.$soapclient->error_str;
		exit;
	}


	print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'."\n";
	echo '<html>'."\n";
	echo '<head>';
	echo '<title>WebService : '.$ws_method.'</title>';
	echo '</head>'."\n";
	echo '<body>'."\n";
	/*
	echo "<h2>Request 1:</h2>";
	echo '<h4>Function</h4>';
	echo $ws_method;
	echo '<h4>SOAP Message</h4>';
	echo '<pre>' . htmlspecialchars($soapclient->request, ENT_QUOTES) . '</pre>';
	*/
	echo '<hr>';
	echo '<h2>Response:</h2>';
	echo '<h4>Result</h4>';
	echo '<pre>';
	print_r($result);
	echo '</pre>';
	echo '<h4>SOAP Message</h4>';
	echo '<pre>'.htmlspecialchars($soapclient->response, ENT_QUOTES).'</pre>';
	echo '</body>'."\n";
	echo '</html>'."\n";
}
?>