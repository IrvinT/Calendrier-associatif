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

set_time_limit(3600);
ini_set('default_socket_timeout', 160);

/*$orders = Order::getOrdersIdByDate(Configuration::get('CYBEROFFICE_dateFrom'),Configuration::get('CYBEROFFICE_dateTo'));*/

$date_from = $get_cyber_datefrom;
$date_to = $get_cyber_dateto;
if (version_compare(_PS_VERSION_, '1.5', '>'))
	$orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT o.*,a.*,o.date_add as o_date_add
			FROM `'._DB_PREFIX_.'orders` as o
			LEFT JOIN `'._DB_PREFIX_.'address` as a ON (a.id_address = o.id_address_invoice)
			WHERE DATE_ADD(o.date_add, INTERVAL -1 DAY) <= \''.pSQL($date_to).'\' AND o.date_add >= \''.pSQL($date_from).'\'
			'.(version_compare(_PS_VERSION_, '1.5', '>')?' AND o.id_shop ='.(int)$id_shop:'').'
			ORDER BY o.id_order ASC');
else
	$orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT o.*,a.*,o.date_add as o_date_add
			FROM `'._DB_PREFIX_.'orders` as o
			LEFT JOIN `'._DB_PREFIX_.'address` as a ON (a.id_address = o.id_address_invoice)
			WHERE DATE_ADD(o.invoice_date, INTERVAL -1 DAY) <= \''.pSQL($date_to).'\' AND o.invoice_date >= \''.pSQL($date_from).'\'
			'.(version_compare(_PS_VERSION_, '1.5', '>')?' AND o.id_shop ='.(int)$id_shop:'').'
			ORDER BY o.id_order ASC');

/*echo "<pre>".print_r($orders)."</pre>";die();*/

$cats = array();
$mylines = array();
$compte = 0;

if ($orders)
{
		foreach ($orders as $myorder)
		{
			$mylines = array();
			if (_PS_VERSION_ < '1.5')
				$lines = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT od.*, od.tax_rate AS rate
				FROM `'._DB_PREFIX_.'order_detail` AS od
				WHERE od.id_order='.(int)$myorder['id_order'].'
				ORDER BY od.id_order_detail ASC');
			else
				$lines = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT od.*, t.rate
				FROM `'._DB_PREFIX_.'order_detail` AS od
				LEFT JOIN `'._DB_PREFIX_.'order_detail_tax` AS odt ON odt.id_order_detail = od.id_order_detail
				LEFT JOIN `'._DB_PREFIX_.'tax` AS t ON odt.id_tax = t.id_tax
				WHERE od.id_order='.(int)$myorder['id_order'].'
				ORDER BY od.id_order_detail ASC');

			if ($lines)
			{
				$i = 0;
				$myline = array();
				/*echo "<pre>".print_r($lines)."</pre>";*/
				foreach ($lines as $line)
				{
					/*echo "<pre>".print_r($lines)."</pre>";die();*/
						$myline['desc']				= $line['product_name'];
						$myline['qty']				= $line['product_quantity'];
						$tax						= ($line['rate']?$line['rate']:0);/*(float)Tax::getProductTaxRate((int)$line['product_id']);*/
						$myline['tva_tx']			= $tax;/*$line['tax_rate'];*/
						/*$myline['fk_product']		= $line['product_id'];*/
						$myline['fk_product'] 		= $line['product_id'].((isset($line['product_attribute_id']) && $line['product_attribute_id'] > 0) ? '-'.$line['product_attribute_id'] : '');
						$myline['subprice']				= $line['original_product_price'];
						if ($line['original_product_price'] == $line['product_price'] && $line['original_product_price'] != 0)
							$myline['remise_percent']	= round((1 - ($line['unit_price_tax_excl'] / $line['original_product_price'])) * 100, 2);
						else $myline['remise_percent'] 	= 0;
						$myline['product_type']		= 0;
						$myline['label']			= $line['product_name'];
						$myline['reference']		= $line['product_reference'];
						/*$mylines[$i] = $myline;*/
						$mylines[] = $myline;
					$i++;
				}
			}
				$title_current = '';
				if (version_compare(_PS_VERSION_, '1.5', '>'))
				{
					$current_shop2 = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));
					$urls = $current_shop2->getUrls();
					foreach ($urls as $key_url => &$url)
						$title_current = $get_cyber_protocole.'://'.$url['domain'].'/'.$url['virtual_uri'];
				}
				else
					$title_current = $get_cyber_protocole.'://'.Tools::getShopDomain(false, true).__PS_BASE_URI__;

					$get_cyber_commande = Tools::jsonDecode(version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_commande', null, null, $id_shop) : Configuration::get('CYBEROFFICE_commande'));
					if (!is_array($get_cyber_commande)) $get_cyber_commande = array('5','4');
					$get_cyber_facture  = Tools::jsonDecode(version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_facture', null, null, $id_shop) : Configuration::get('CYBEROFFICE_facture'));
					if (!is_array($get_cyber_facture)) $get_cyber_facture = array('5','4');

					$cats[] = array(
								'id_order' 					=> $myorder['id_order'],
								'id_carrier' 				=> $myorder['id_carrier'],
								'id_customer' 				=> $myorder['id_customer'],
								'company' 					=> $myorder['company'],
								'lastname' 					=> $myorder['lastname'],
								'firstname' 				=> $myorder['firstname'],
								'address1'					=> $myorder['address1'].'<br/>'.$myorder['address2'],
								'postcode' 					=> $myorder['postcode'],
								'city' 						=> $myorder['city'],
								'id_country' 				=> $myorder['id_country'],
								'payment' 					=> $myorder['payment'],
								'module' 					=> $myorder['module'],
								'total_discounts' 			=> $myorder['total_discounts'],
								'total_paid' 				=> $myorder['total_paid'],
								'total_products' 			=> $myorder['total_products'],
								'total_products_wt' 		=> $myorder['total_products_wt'],
								'total_shipping' 			=> $myorder['total_shipping'],
								'invoice_number' 			=> $myorder['invoice_number'],
								'invoice_date' 				=> (Tools::substr($myorder['invoice_date'], 0, 1) > 0?$myorder['invoice_date']:$myorder['o_date_add']),
								'delivery_date' 			=> (Tools::substr($myorder['delivery_date'], 0, 1) > 0?$myorder['delivery_date']:$myorder['o_date_add']),
								'id_address_invoice'		=> $myorder['id_address_invoice'],
								'id_address_delivery'		=> $myorder['id_address_delivery'],
								'valid' 					=> $myorder['current_state'],
								'date_add' 					=> $myorder['o_date_add'],
								'warehouse'					=> $get_cyber_warehouse,
								'lines' 					=> $mylines,
								'match'						=> $get_cyber_prefix,
								'current_shop'				=> $title_current,
								'commOK'					=> (in_array($myorder['current_state'], $get_cyber_commande)?1:0),
								'factOK'					=> (in_array($myorder['current_state'], $get_cyber_facture)?1:0)
							);
		$compte++;
		/*if ($compte > 0) break;*/
		}
}
/*echo "<pre>".print $compte."</pre>";die();
echo "<pre>".print_r($cats)."</pre>";die();*/

/* Set the WebService URL*/
$ws_dol_url = $get_cyber_path.'cyberoffice/server_order.php';
$ws_method  = 'Create';
$ns = $get_cyber_path.'ns/';
/*$client = new SoapClient($wsdl, array("connection_timeout"=>15));*/
$soapclient = new nusoap_client($ws_dol_url);
if ($soapclient)
	$soapclient->soap_defencoding = 'UTF-8';

$soapclient2 = new nusoap_client($ws_dol_url);/*new nusoap_client($ws_dol_url, array("connection_timeout"=>15));*/
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

$title_current = '';
		if (version_compare(_PS_VERSION_, '1.5', '>'))
		{
			$current_shop2 = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));
			$urls = $current_shop2->getUrls();

			foreach ($urls as $key_url => &$url)
				$title_current = $get_cyber_protocole.'://'.$url['domain'].'/'.$url['virtual_uri'];
		}
		else
			$title_current = $get_cyber_protocole.'://'.Tools::getShopDomain(false, true).__PS_BASE_URI__;


$authentication = array(
	'dolibarrkey'		=>	htmlentities($get_cyber_key, ENT_COMPAT, 'UTF-8'),
	'sourceapplication'	=>	'LVSInformatique',
	'login'				=>	$get_cyber_login,
	'password'			=>	$get_cyber_pass,
	'entity'			=>	$get_cyber_entity,
	'myurl'				=>  $title
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
	if (! $result)
	{
		var_dump($soapclient);
		print '<h2>Erreur SOAP </h2>'.$soapclient->error_str;
		exit;
	}


	print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'."\n";
	echo '<html>'."\n";
	echo '<head>';
	echo '<title>WebService : '.$ws_method.'</title>';
	echo '</head>'."\n";
	echo '<body>'."\n";
	echo '<h2>Response '.$i.' :</h2>';
	echo '<h4>Result</h4>';
	echo '<pre>';
	print_r($result);
	echo '</pre>';
	echo '</body>'."\n";
	echo '</html>'."\n";
}
?>