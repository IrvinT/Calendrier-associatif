<?php
/**
 *	CyberOffice
 *
 *  @author    LVSinformatique <contact@lvsinformatique.com>
 *  @copyright 2014 LVSInformatique
 *	@license   NoLicence
 *  @version   1.2.32
 */

require_once(dirname(__FILE__).'/nusoap/lib/nusoap.php');		/* Include SOAP*/
require('../../config/config.inc.php');
/*
if (!defined('_PS_VERSION_'))
	exit;

$token = Tools::getToken(false);
print $token.'<br/>';
$token = Tools::getToken();
print $token;
print '<pre>';
print_r(Context::getContext());
print '</pre>';
if( Context::getContext()->customer->isLogged() ) print 'ok';
if( Context::getContext()->cookie->isLoggedBack()) print 'ok2';
$cookie = Context::getContext()->cookie;
print '****';
print_r( $cookie);
*/
$id_shop = Tools::getValue('shop');
if (version_compare(_PS_VERSION_, '1.5', '>'))
		$token = Configuration::get('CYBEROFFICE_TOKEN', null, null, $id_shop)._COOKIE_KEY_;
else
		$token = Configuration::get('CYBEROFFICE_TOKEN')._COOKIE_KEY_;
$ws_dol_url = Configuration::get('CYBEROFFICE_path').'cyberoffice/server_thirdparty.php';
$ws_method  = 'getListOfThirdParties';/*'getCategory';*/
$ns = Configuration::get('CYBEROFFICE_path').'ns/';
/* Set the WebService URL*/
$soapclient = new nusoap_client($ws_dol_url);
if ($soapclient)
	$soapclient->soap_defencoding = 'UTF-8';
$soapclient2 = new nusoap_client($ws_dol_url);
if ($soapclient2)
	$soapclient2->soap_defencoding = 'UTF-8';
/* Call the WebService method and store its result in $result.*/
$authentication = array(
	'dolibarrkey'=>htmlentities(Configuration::get('CYBEROFFICE_key'), ENT_COMPAT, 'UTF-8'),
	'sourceapplication'=>'LVSInformatique',
	'login'=>Configuration::get('CYBEROFFICE_login'),
	'password'=>Configuration::get('CYBEROFFICE_pass'),
	'entity'=>'');
$filterthirdparty = array(
	'client'=>'1'
	);
$parameters = array('authentication'=>$authentication, $filterthirdparty);
$result = $soapclient->call($ws_method, $parameters, $ns, '');
if ($soapclient->fault)
{
			echo '<h2>Erreur SOAP </h2>';
			print_r($result);
}
else
{
			$err_msg = $soapclient->getError();
			if ($err_msg)
			{
				echo '<h2>Erreur SOAP </h2>'.$err_msg;
				print '<br/>'.$soapclient->error_str;
				echo '<pre>'.htmlspecialchars($soapclient->response, ENT_QUOTES).'</pre>';
				echo '<pre>';
				var_dump($soapclient);
				echo '</pre>';
			}
			else
			{
/*
if (! $result)
{
	var_dump($soapclient);
	print '<h2>Erreur SOAP 1</h2>'.$soapclient->error_str;
}
*/
/*
 * View
 */
				print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'."\n";
				echo '<html>'."\n";
				echo '<head>';
				echo '<title>WebService Test: '.$ws_method.'</title>';
				echo '</head>'."\n";

				echo '<body>'."\n";
				echo '<h2>Request 1:</h2>';
				echo '<h4>Function</h4>';
				echo $ws_method;
				echo '<h4>SOAP Message</h4>';
				echo '<pre>'.htmlspecialchars($soapclient->request, ENT_QUOTES).'</pre>';
				echo '<hr>';

				echo '<h2>Response:</h2>';
				echo '<h4>Result</h4>';
				echo '<pre>';
				print_r($result);
				echo '</pre>';

				echo '<h4>SOAP Message</h4>';
				echo '<pre>'.htmlspecialchars($soapclient->response, ENT_QUOTES).'</pre>';

				echo '<h2>Debug</h2><pre>'.htmlspecialchars($soapclient->debug_str, ENT_QUOTES).'</pre>';
				echo '</body>'."\n";
				echo '</html>'."\n";
			}
		}
?>