<?php
/**
 *	CyberOffice
 *
 *  @author    LVSinformatique <contact@lvsinformatique.com>
 *  @copyright 2014 LVSInformatique
 *	@license   NoLicence
 *  @version   1.2.32
 */

if (!defined('_CAN_LOAD_FILES_')) exit;
if (!defined( '_PS_VERSION_')) exit;

class Cyberoffice extends Module
{
	private $html = '';
	private $post_errors = array();
	public $errors = array();
	public $my_id = null;

	public function __construct()
	{
		$this->name = 'cyberoffice';
		$this->tab = 'migration_tools';
		$this->version = '1.2.32';
		$this->author = 'LVSInformatique';
		$this->need_instance = 0;
		$this->module_key = '180a01abf377ec5c8417fd86d67d2530';

		parent::__construct();

		$this->displayName = $this->l('CyberOffice');
		$this->description = $this->l('Synchronization from Dolibarr to Prestashop');
		$this->confirmUninstall = $this->l('Êtes-vous certain de vouloir supprimer les informations de ce module ?');
	}

	public function install()
	{
		$id_shop = version_compare(_PS_VERSION_, '1.5', '>') ? Shop::getContextShopID() : 1;
		Configuration::updateValue('CYBEROFFICE_TOKEN', Tools::passwdGen());
		$get_cyber_commande = Tools::jsonDecode(version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_commande', null, null, $id_shop) : Configuration::get('CYBEROFFICE_commande'));
		if (!is_array($get_cyber_commande)) $get_cyber_commande = array('5','4');
		$get_cyber_facture  = Tools::jsonDecode(version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_facture', null, null, $id_shop) : Configuration::get('CYBEROFFICE_facture'));
		if (!is_array($get_cyber_facture)) $get_cyber_facture = array('5','4');
		//$myarray=array('5','4');
		Configuration::updateValue('CYBEROFFICE_commande', Tools::jsonEncode($get_cyber_commande));
		Configuration::updateValue('CYBEROFFICE_facture', Tools::jsonEncode($get_cyber_facture));
		$loaded_curl = (extension_loaded('curl') ? 'ok':'ko');
		if ($loaded_curl == 'ko') return false;
		if (!parent::install()
				|| !$this->registerHook('backOfficeFooter')//displayBackOfficeFooter
				|| !$this->registerHook('backOfficeHeader'))
			return false;
		$hook_create_account = version_compare(_PS_VERSION_, '1.5', '>') ? 'actionCustomerAccountAdd' : 'createAccount';
		if (!$this->registerHook($hook_create_account))
			return false;
		$hook_update_order_status = version_compare(_PS_VERSION_, '1.5', '>') ? 'actionOrderStatusPostUpdate' : 'postUpdateOrderStatus';
		if (!$this->registerHook($hook_update_order_status))
			return false;

		return true;
	}

	public function uninstall()
	{
		$this->context->cookie->CyberCat = '';
		$this->context->cookie->CyberProd = '';
		$this->context->cookie->CyberAddr = '';

		return parent::uninstall();
	}

	public function getContent()
	{
		//error_reporting(E_ALL);
		$this->html = '';
		if (_PS_VERSION_ < '1.5')
			includeDatepicker(array('datepickerFrom', 'datepickerTo'));
		else
		{
			$this->context->controller->addJqueryUI('ui.datepicker');
			$this->html .= $this->ModuleDatepicker('datepicker', false);
		}
		if (Tools::isSubmit('submitCross'))
		{
			//print_r(Tools::getValue('Cybershop'));die();
			if (Tools::getValue('CYBEROFFICE_key') && Tools::strlen(Tools::getValue('CYBEROFFICE_key')) < 32)
				$this->html .= '<div class="alert error">'.$this->l('Key length must be 32 character long').'</div>';
			else
			{
				Configuration::updateValue('CYBEROFFICE_protocole', Tools::getValue('CYBEROFFICE_protocole'));
				Configuration::updateValue('CYBEROFFICE_key', Tools::getValue('CYBEROFFICE_key'));
				Configuration::updateValue('CYBEROFFICE_LANG', Tools::getValue('CYBEROFFICE_LANG'));
				Configuration::updateValue('CYBEROFFICE_path', Tools::getValue('CYBEROFFICE_path'));
				Configuration::updateValue('CYBEROFFICE_login', Tools::getValue('CYBEROFFICE_login'));
				Configuration::updateValue('CYBEROFFICE_pass', Tools::getValue('CYBEROFFICE_pass'));
				Configuration::updateValue('CYBEROFFICE_entity', (Tools::getValue('CYBEROFFICE_entity')?Tools::getValue('CYBEROFFICE_entity'):1));
				Configuration::updateValue('CYBEROFFICE_warehouse', Tools::getValue('CYBEROFFICE_warehouse'));
				Configuration::updateValue('CYBEROFFICE_prefix', (Tools::getValue('CYBEROFFICE_prefix')?Tools::getValue('CYBEROFFICE_prefix'):'Presta'));
				Configuration::updateValue('CYBEROFFICE_commande', Tools::jsonEncode(Tools::getValue('CYBEROFFICE_commande')));
				Configuration::updateValue('CYBEROFFICE_facture', Tools::jsonEncode(Tools::getValue('CYBEROFFICE_facture')));
				Configuration::updateValue('CYBEROFFICE_client', Tools::getValue('CYBEROFFICE_client'));
				$this->html .= $this->displayConfirmation($this->l('Settings updated successfully'));
			}
		}
		if (Tools::isSubmit('datepicker'))
		{
			//$stats_date_from = Tools::getValue('datepickerFrom');
			//$stats_date_to = Tools::getValue('datepickerTo');
			Configuration::updateValue('CYBEROFFICE_dateTo', Tools::getValue('datepickerTo'));
			Configuration::updateValue('CYBEROFFICE_dateFrom', Tools::getValue('datepickerFrom'));
		}

		$id_shop = version_compare(_PS_VERSION_, '1.5', '>') ? Shop::getContextShopID() : 1;
		/*
		if (!$id_shop)
			if (Configuration::get('PS_SHOP_DEFAULT'))
				$id_shop = Configuration::get('PS_SHOP_DEFAULT');
			else
				$id_shop = 0;
		*/
		if (!$id_shop)
		{
			$shop_name = '<span style="color:red">'.$this->l('ERREUR!! Vous devez choisir une boutique : ').'</span>';
			$id_shop = 0;
		}
		elseif (version_compare(_PS_VERSION_, '1.5', '>'))
		{
			$shop_info = Shop::getShop($id_shop);
			$shop_name = $shop_info['name'].' : ';
		}
		else
			$shop_name = '';
		/*
		if (!$id_shop_test)
			$shop_name = '<span style="color:red">ERREUR!! Vous devez choisir une boutique : </span>';
		else {
			$shop_info = Shop::getShop($id_shop);
			$shop_name = $shop_info['name']. ' : ';
		}
		*/
		$langagues = Db::getInstance()->ExecuteS('SELECT * FROM  '._DB_PREFIX_.'lang WHERE ACTIVE = 1');
		if (version_compare(_PS_VERSION_, '1.5', '>'))
		{
			$categories = Db::getInstance()->getRow('SELECT COUNT(*) as countcategory FROM  '._DB_PREFIX_.'category AS c
				INNER JOIN '._DB_PREFIX_.'category_shop AS cs ON c.id_category = cs.id_category AND cs.id_shop = '.$id_shop);
			$customers = Db::getInstance()->getRow('SELECT COUNT(*) as countcustomer FROM  '._DB_PREFIX_.'customer
				WHERE DATE_ADD(date_add, INTERVAL -1 DAY) <= \''.pSQL(Configuration::get('CYBEROFFICE_dateTo', null, null, $id_shop)).'\' AND date_add >= \''.pSQL(Configuration::get('CYBEROFFICE_dateFrom', null, null, $id_shop)).'\'');
			$products = Db::getInstance()->getRow('SELECT COUNT(*) AS countproduct FROM '._DB_PREFIX_.'product AS p
				INNER JOIN  '._DB_PREFIX_.'product_shop AS ps ON p.id_product = ps.id_product AND ps.id_shop = '.$id_shop.'
				LEFT JOIN '._DB_PREFIX_.'product_attribute as a ON a.id_product=p.id_product
				WHERE p.id_category_default IN (SELECT cs.id_category FROM '._DB_PREFIX_.'category_shop as cs WHERE cs.id_shop = '.(int)$id_shop.')');
			/*$products = Db::getInstance()->getRow('SELECT COUNT(*) as countproduct FROM  '._DB_PREFIX_.'product as p
				LEFT JOIN '._DB_PREFIX_.'category_product as cp ON cp.id_product=p.id_product
				LEFT JOIN '._DB_PREFIX_.'product_attribute as a ON a.id_product=p.id_product
				INNER JOIN '._DB_PREFIX_.'category_shop AS cs ON c.id_category = cs.id_category AND cs.id_shop = '.$id_shop);*/
			$orders = Db::getInstance()->getRow('SELECT COUNT(*) as countorder FROM  '._DB_PREFIX_.'orders 
				WHERE id_shop ='.(int)$id_shop.' AND DATE_ADD(date_add, INTERVAL -1 DAY) <= \''.pSQL(Configuration::get('CYBEROFFICE_dateTo', null, null, $id_shop)).'\' AND date_add >= \''.pSQL(Configuration::get('CYBEROFFICE_dateFrom', null, null, $id_shop)).'\'');
		}
		else
		{
			$categories = Db::getInstance()->getRow('SELECT COUNT(*) as countcategory FROM  '._DB_PREFIX_.'category');
			$customers = Db::getInstance()->getRow('SELECT COUNT(*) as countcustomer FROM  '._DB_PREFIX_.'customer
				WHERE DATE_ADD(date_add, INTERVAL -1 DAY) <= \''.pSQL(Configuration::get('CYBEROFFICE_dateTo')).'\' AND date_add >= \''.pSQL(Configuration::get('CYBEROFFICE_dateFrom')).'\'');
			$products = Db::getInstance()->getRow('SELECT COUNT(*) as countproduct FROM  '._DB_PREFIX_.'product as p
		 		LEFT JOIN '._DB_PREFIX_.'product_attribute as a ON a.id_product=p.id_product');
			$orders = Db::getInstance()->getRow('SELECT COUNT(*) as countorder FROM  '._DB_PREFIX_.'orders 
				WHERE DATE_ADD(invoice_date, INTERVAL -1 DAY) <= \''.pSQL(Configuration::get('CYBEROFFICE_dateTo')).'\' AND invoice_date >= \''.pSQL(Configuration::get('CYBEROFFICE_dateFrom')).'\'');
		}

		$test = $this->testConfig();

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
		$get_cyber_commande = Tools::jsonDecode(version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_commande', null, null, $id_shop) : Configuration::get('CYBEROFFICE_commande'));
		if (!is_array($get_cyber_commande)) $get_cyber_commande = array();
		$get_cyber_facture  = Tools::jsonDecode(version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_facture', null, null, $id_shop) : Configuration::get('CYBEROFFICE_facture'));
		if (!is_array($get_cyber_facture)) $get_cyber_facture = array();
		$get_cyber_client = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_client', null, null, $id_shop) : Configuration::get('CYBEROFFICE_client');
		if (!$get_cyber_protocole) $get_cyber_protocole = 'http';
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

		$this->html .= '
		<fieldset><legend><img src="../img/admin/info.png" />'.$shop_name.$this->l('Test Connexion').'</legend>
		'.$this->l('Configuration du répertoire ').' '.$test['repertoire'].'cyberoffice '.($test['repertoireTF'] == 'KO'?'<img src="../img/admin/disabled.gif" />':'<img src="../img/admin/enabled.gif" />').'<br/>
		'.((!$test['indice'] || $test['indice'] == -1)?'0':$test['indice']).' '.$this->l(' boutique(s) connectée(s) à Dolibarr').'<img src="../img/admin/enabled.gif" /><br/>
		'.$this->l('Dolibarr Webservices : ').$test['webservice'].((!$test['indice'] || $test['indice'] == -1)?'<img src="../img/admin/disabled.gif" />':'<img src="../img/admin/enabled.gif" />').'<br/>
		'.$this->l('Dolibarr CyberOffice : ').$test['dolicyber'].($test['dolicyber'] == 'OK'?'<img src="../img/admin/enabled.gif" />':'<img src="../img/admin/disabled.gif" />').'<br/>
		'.$this->l('Default language').': '.($get_cyber_lang?'<img src="../img/admin/enabled.gif" />':'<img src="../img/admin/disabled.gif" />').'<br/>
		'.$this->l('Licence unique n°').' <strong>'.sha1($get_cyber_protocole.'://'.Tools::getShopDomain(false, true).__PS_BASE_URI__).'</strong> '.$this->l('accordé à').' <strong>'.$get_cyber_protocole.'://'.Tools::getShopDomain(false, true).__PS_BASE_URI__.'</strong><img src="../img/admin/enabled.gif" /><br/>';
		if (_PS_VERSION_ < '1.5')
			$hook_right = (int)Hook::get('postUpdateOrderStatus');
		else
			$hook_right = (int)Hook::getIdByName('actionOrderStatusPostUpdate');
		$module_instance = Module::getInstanceByName('cyberoffice');
		$test_module_info = 0;
		if (_PS_VERSION_ < '1.5')
		{
			$module_info = Hook::getModuleFromHook($hook_right, $module_instance->id);
			if (isset($module_info['active']) && (int)$module_info['active'] == 1) $test_module_info = 1;
		}
		else
		{
			$module_info = Hook::getModulesFromHook($hook_right, $module_instance->id);
			if (isset($module_info[0]['active']) && (int)$module_info[0]['active'] == 1) $test_module_info = 1;
		}
		if ($test_module_info == 1)
			$this->html .= '';/*$this->l('orderHookOK').'<img src="../img/admin/enabled.gif" /><br/>';*/
		else
			$this->html .= '<span style="color:red;font-weight:bold">'.$this->l('orderHook::KO, only products and categories will be synchronized').'</span><br/>';
		/*if (isset(Shop::getContextShopID())*/
		$this->html .= '</fieldset>';

		$this->html .= '
		<fieldset><legend><img src="../img/admin/info.png" />'.$shop_name.$this->l('CyberOffice Infos').'</legend>
		1) '.$this->l('Installer le module sous Prestashop').'<br/>
		2) '.$this->l('Télécharger le module').' <a href="'.$get_cyber_protocole.'://'.Tools::getShopDomain(false, true).__PS_BASE_URI__.'modules/cyberoffice/module_cyberoffice.zip"><strong> module_cyberoffice.zip</strong></a> '.$this->l('et l\'installer sous Dolibarr, sous le répertoire htdocs/').'<br/>
		3) '.$this->l('Sous Dolibarr :').' <br/>
			'.$this->l('-activer le webservice et générer la clé. (accueil-configuration-modules-modules interfaces-WebServices)').'<br/>
			'.$this->l('-activer le module cyberoffice').'<br/>
		4) '.$this->l('Sous Prestashop : paramétrer le module. Tous les champs doivent être renseignés.').'<br/>
		5) '.$this->l('Tout est coché vert ? votre synchro est opérationnelle').'<br/>
		6) '.$this->l('Initialiser la base "Categorie" et la base "Produit"').'<br/>
		7) '.$this->l('Optionnel : Vous pouvez synchroniser vos commandes et clients existants').'
		</fieldset>';

		$this->html .= '<br/>
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$shop_name.$this->l('Settings').'</legend>
		<label>'.$this->l('key').'</label>
		<div class="margin-form">
			<input type="text" size="40" name="CYBEROFFICE_key" id="code" value="'.htmlentities($get_cyber_key, ENT_COMPAT, 'UTF-8').'" />
			'.$this->l('Copier ici la clé générée dans le module Dolibarr/Webservices.').'
		</div>
		<label>'.$this->l('chemin d\'accès à Dolibarr').'</label>
		<div class="margin-form">
			<input type="text" size="40" name="CYBEROFFICE_path" value="'.$get_cyber_path.'" /> (http://www.yourdomain.com/htdocs/)
		</div>
		<label>'.$this->l('Authentification Dolibarr').'</label>
		<div class="margin-form">
			<input type="text" size="30" name="CYBEROFFICE_login" value="'.$get_cyber_login.'" />'.$this->l('Login').'
			<br/><br/>
			<input type="text" size="30" name="CYBEROFFICE_pass" value="'.$get_cyber_pass.'" />'.$this->l('PassWord').'
		</div>
		<label>'.$this->l('Entité').'</label>
		<div class="margin-form">
			<input type="text" size="10" name="CYBEROFFICE_entity" value="'.($get_cyber_entity?$get_cyber_entity:1).'" />'.$this->l('Entité Dolibarr dans le cas de l\'utilisation du multisociété. Mettre 1 par défaut').'
		</div>
		<label>'.$this->l('Entrepôt').'</label>
		<div class="margin-form">
			<input type="text" size="10" name="CYBEROFFICE_warehouse" value="'.($get_cyber_warehouse?$get_cyber_warehouse:1).'" />'.$this->l('Index de l\'entrepôt utilisé dans Dolibarr pour la gestion des stocks').'
		</div>
		<label>'.$this->l('Default language').'</label>
		<div class="margin-form">
			<select name="CYBEROFFICE_LANG"><option value="0">------</option>';//(int)(Configuration::get('CYBEROFFICE_LANG')
				foreach ($langagues as $lang)
					$this->html .= '<option value="'.$lang['id_lang'].'" '.($get_cyber_lang == $lang['id_lang']?'selected="selected"':'').'>'.$lang['name'].'</option>';
					$this->html .= '
			</select>
		</div>
		<label>'.$this->l('Statut(s) à synchroniser').'</label>
		<div class="margin-form">';
		$countarray = count(OrderState::getOrderStates((int)Context::getContext()->language->id));
		$this->html .= '<div >
			<div style="float:left;">'.$this->l('Commande').'<br/>
			<select multiple name="CYBEROFFICE_commande[]" size="'.$countarray.'">';
				foreach (OrderState::getOrderStates((int)Context::getContext()->language->id) as $orderstate)
					$this->html .= '<option value="'.$orderstate['id_order_state'].'" '.(in_array($orderstate['id_order_state'], $get_cyber_commande)? 'selected="selected"':'').'>'.$orderstate['name'].'</option>';

			$this->html .= '</select></div>
		
			<div>'.$this->l('Facture').'<br/>
				<select multiple name="CYBEROFFICE_facture[]" size="'.$countarray.'">';
				foreach (OrderState::getOrderStates((int)Context::getContext()->language->id) as $orderstate)
					$this->html .= '<option value="'.$orderstate['id_order_state'].'" '.(in_array($orderstate['id_order_state'], $get_cyber_facture)? 'selected="selected"':'').'>'.$orderstate['name'].'</option>';

			$this->html .= '</select></div>
		</div>
		</div>';
		$this->html .= '<label>'.$this->l('Synchroniser tous les clients').'</label>
		<div class="margin-form">
			<select name="CYBEROFFICE_client">
				<option value="0" '.($get_cyber_client == '0'?'selected="selected"':'').'>Non</option>
				<option value="1" '.($get_cyber_client == '1'?'selected="selected"':'').'>Oui</option>
				selected="selected"
			</select>
		</div>';

		$this->html .= '<label>'.$this->l('Shop Protocole').'</label>
		<div class="margin-form">
			<select name="CYBEROFFICE_protocole">
				<option value="http" '.($get_cyber_protocole == 'http'?'selected="selected"':'').'>http</option>
				<option value="https" '.($get_cyber_protocole == 'https'?'selected="selected"':'').'>https</option>
				selected="selected"
			</select>
		</div>';

		$this->html .= '<label>'.$this->l('Prefixe').'</label>
		<div class="margin-form">
			<input type="text" size="10" name="CYBEROFFICE_prefix" value="'.($get_cyber_prefix?$get_cyber_prefix:'Presta').'" />'.$this->l('Prefixe à utiliser pour les references produits').($get_cyber_prefix == '{ref}'?'<span style="color:red;font-weight:bold"> '.$this->l('ATTENTION ! Vos références doivent être uniques').'</span>':'').'
		</div>';

		/*$this->html .= '<label>'.$this->l('Boutique(s) à synchroniser').'</label>
		<div class="margin-form">
			'.$shop_html.'
		</div>';*/

		$this->html .= '<center><input type="submit" name="submitCross" value="'.$this->l('Save').'" class="button" /></center>
		</fieldset>
		</form>';

		$this->html .= '<br /><fieldset><legend><img src="../img/admin/tab-tools.gif" />'.$shop_name.$this->l('Initialisation').'</legend>
		<div class="margin-form">
			<a class="button" href="'.$get_cyber_protocole.'://'.Tools::getShopDomain(false, true).__PS_BASE_URI__.'modules/cyberoffice/script_category.php?cyberoffice_token='.sha1($get_cyber_token._COOKIE_KEY_).'&shop='.$id_shop.'" target="_blank"> '.$this->l('Synchronisation des catégories').'</a>'.$categories['countcategory'].' '.$this->l('records').'<br/><br/>
			<form action="'.$get_cyber_protocole.'://'.Tools::getShopDomain(false, true).__PS_BASE_URI__.'modules/cyberoffice/script_product.php?cyberoffice_token='.sha1($get_cyber_token._COOKIE_KEY_).'&shop='.$id_shop.'" method="post" target="_blank">
					'.$this->l('From Id').' <input type="text" name="idFrom" id="idFrom" value=0 size="5">
					'.$this->l('To Id').' <input type="text" name="idTo" id="idTo" value=500 size="5">
					<input type="submit" name="idFromTo" value="'.$this->l('Synchronisation des produits').'" class="button" />'.$products['countproduct'].' '.$this->l('records').'<br/>
			</form>

		</div>
		</fieldset>';

		$this->html .= '<br /><fieldset><legend><img src="../img/admin/tab-tools.gif" />'.$shop_name.$this->l('CyberOffice Tools').'</legend>
		<p><strong><u>'.$this->l('Synchronisation des Clients et des Commandes déjà existants vers Dolibarr').'</u></strong></p>
	<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<div class="margin-form">
			'.$this->l('From').'<input type="text" name="datepickerFrom" id="datepickerFrom" value="'.$get_cyber_datefrom.'" class="datepicker">
			'.$this->l('To').'<input type="text" name="datepickerTo" id="datepickerTo" value="'.$get_cyber_dateto.'" class="datepicker">
			<input type="submit" name="datepicker" value="'.$this->l('Save').'" class="button" />
		</div>
	</form>
		<div class="margin-form">			
			<a class="button" href="'.$get_cyber_protocole.'://'.Tools::getShopDomain(false, true).__PS_BASE_URI__.'modules/cyberoffice/script_customerALL.php?cyberoffice_token='.sha1($get_cyber_token._COOKIE_KEY_).'&shop='.$id_shop.'" target="_blank"> '.$this->l('Synchronisation des clients').'</a>'.$customers['countcustomer'].' '.$this->l('records').'<br/><br/>	
			<a class="button" href="'.$get_cyber_protocole.'://'.Tools::getShopDomain(false, true).__PS_BASE_URI__.'modules/cyberoffice/script_order.php?cyberoffice_token='.sha1($get_cyber_token._COOKIE_KEY_).'&shop='.$id_shop.'"    target="_blank"> '.$this->l('Synchronisation des commandes').'</a>'.$orders['countorder'].' '.$this->l('records').'<br/><br/>
		</div>
		</fieldset>';

		return $this->html;
	}

	private function installModuleTab($tab_class, $tab_name, $id_tab_parent)
	{
		Tools::copy(_PS_MODULE_DIR_.$this->name.'/logo.gif', _PS_IMG_DIR_.'t/'.$tab_class.'.gif');
		$tab = new Tab();
		$tab->name = $tab_name;
		$tab->class_name = $tab_class;
		$tab->module = $this->name;
		$tab->id_parent = $id_tab_parent;
		if (!$tab->save())
			return false;
		return true;
	}

	private function uninstallModuleTab($tab_class)
	{
		$id_tab = Tab::getIdFromClassName($tab_class);
		if ($id_tab != 0)
		{
			$tab = new Tab($id_tab);
			$tab->delete();
			return true;
		}
		return false;
	}

	private function ModuleDatepicker($class, $time)
	{
		$return = '';
		if ($time)
			$return = '
	        var dateObj = new Date();
	        var hours = dateObj.getHours();
	        var mins = dateObj.getMinutes();
	        var secs = dateObj.getSeconds();
	        if (hours < 10) { hours = "0" + hours; }
	        if (mins < 10) { mins = "0" + mins; }
	        if (secs < 10) { secs = "0" + secs; }
	        var time = " "+hours+":"+mins+":"+secs;';
		$return .= '
	    $(function() {
	        $(".'.Tools::htmlentitiesUTF8($class).'").datepicker({
	            prevText:"",
	            nextText:"",
	            dateFormat:"yy-mm-dd"'.($time ? '+time' : '').'});
	    });';
		return '<script type="text/javascript">'.$return.'</script>';
	}

	/* HOOK
	*******/
	public function hookBackOfficeHeader($params)
	{
		if (Tools::getValue('controller') == 'AdminProducts'
			|| Tools::getValue('controller') == 'AdminCategories'
			|| Tools::getValue('controller') == 'AdminAddresses'
			|| Tools::getValue('controller') == 'AdminCustomers')
		{
			if (Tools::getValue('id_category')) $this->context->cookie->CyberCat = Tools::getValue('id_category');
			if (Tools::getValue('id_product_attribute')) $this->context->cookie->CyberAttr = Tools::getValue('id_product_attribute');
			if (Tools::getValue('id_address')) $this->context->cookie->CyberAddr = Tools::getValue('id_address');
			if (Tools::getValue('id_product')) $this->context->cookie->CyberProd = Tools::getValue('id_product');
		}
	}

	public function hookBackOfficeFooter($params)
	{
		if (isset($this->context->cookie->CyberCat) && (int)$this->context->cookie->CyberCat > 0)
		{
			$lastcat = Db::getInstance()->getRow('SELECT id_category as id_category FROM '._DB_PREFIX_.'category ORDER by date_upd DESC');
			$this->my_id = $lastcat['id_category'];
			$this->hookcategoryAddition($params);
		}
		$id_shop = version_compare(_PS_VERSION_, '1.5', '>') ? Shop::getContextShopID() : 1;
		$get_cyber_lang = version_compare(_PS_VERSION_, '1.5', '>') ? (int)Configuration::get('CYBEROFFICE_LANG', null, null, $id_shop) : (int)Configuration::get('CYBEROFFICE_LANG');
		if (Tools::getValue('conf') && isset($this->context->cookie->CyberProd) && (int)$this->context->cookie->CyberProd > 0)
				$this->_scriptproduct($this->context->cookie->CyberProd, $this->context->cookie->CyberAttr);
		if (Tools::getValue('conf') && isset($this->context->cookie->CyberAddr) && (int)$this->context->cookie->CyberAddr > 0)
				$this->_scriptCustomer($this->context->cookie->CyberAddr);
		$this->context->cookie->CyberCat = '';
		$this->context->cookie->CyberProd = '';
		$this->context->cookie->CyberAttr = '';
		$this->context->cookie->CyberAddr = '';
	}
	public function hookdisplayBackOfficeFooter($params)
	{
		$this->hookBackOfficeFooter($params);
	}
	public function hookdisplayBackOfficeHeader($params)
	{
		$this->hookBackOfficeHeader($params);
	}
	public function hookwatermark($params)
	{
		require_once(dirname(__FILE__).'/nusoap/lib/nusoap.php');
		//echo "<pre>".print_r($params)."</pre>";die();
	}
	public function hookaddproduct($params)
	{
		require_once(dirname(__FILE__).'/nusoap/lib/nusoap.php');
	}
	public function hookActionOrderStatusUpdate($params)
	{
		$id_shop = version_compare(_PS_VERSION_, '1.5', '>') ? Shop::getContextShopID() : 1;
		$get_cyber_commande = Tools::jsonDecode(version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_commande', null, null, $id_shop) : Configuration::get('CYBEROFFICE_commande'));
		if (!is_array($get_cyber_commande)) $get_cyber_commande = array();
		$get_cyber_facture  = Tools::jsonDecode(version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_facture', null, null, $id_shop) : Configuration::get('CYBEROFFICE_facture'));
		if (!is_array($get_cyber_facture)) $get_cyber_facture = array();
		require_once(dirname(__FILE__).'/nusoap/lib/nusoap.php');
		$new_order_status = $params['newOrderStatus'];
		$id_order_state = $new_order_status->id;
		$orders = array();
		//if ($params['newOrderStatus']->invoice == 1 || $id_order_state == 5 || $id_order_state == 4)//delivery
		if (in_array($id_order_state, $get_cyber_commande) || in_array($id_order_state, $get_cyber_facture))
			$orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT o.*,a.*,o.date_add as o_date_add
			FROM `'._DB_PREFIX_.'orders` as o
			LEFT JOIN `'._DB_PREFIX_.'address` as a ON (a.id_address = o.id_address_invoice)
			WHERE o.id_order = '.(int)$params['id_order'].'
			ORDER BY o.id_order ASC');
		/*echo "<pre>".print_r($orders)."</pre>";die();*/
		$cats = array();
		$mylines = array();
		//$compte=0;
		if (is_array($orders) && count($orders) > 0)
		{
			foreach ($orders as $myorder)
			{
				/* CREATION DU CLIENT
				*********************/
				$customers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
					SELECT cu.id_customer as cu_id_customer, cu.email as cu_email, cu.firstname as cu_firstname, cu.lastname as cu_lastname,cu.id_gender as cu_id_gender,cu.birthday as cu_birthday,
					a.*, s.name AS state, s.iso_code AS state_iso,c.iso_code as c_iso_code
					FROM '._DB_PREFIX_.'customer cu
					LEFT JOIN '._DB_PREFIX_.'address a ON (a.id_customer = cu.id_customer AND a.deleted = 0)
					LEFT JOIN '._DB_PREFIX_.'country c ON (a.id_country = c.id_country)
					LEFT JOIN '._DB_PREFIX_.'state s ON (s.id_state = a.id_state)
					WHERE cu.id_customer = '.(int)$myorder['id_customer']
					);
				$cats = array();
				//$catsId = array();
				//$compte=0;
				if ($customers)
				{
						foreach ($customers as $customer)
						{
								//echo "<pre>".print_r($category)."</pre>";die();
								$cats[] = array('id_customer'	=> $customer['cu_id_customer'],
												'birthday' 		=> $customer['cu_birthday'],
												'email' 		=> $customer['cu_email'],
												'id_gender' 	=> $customer['cu_id_gender'],  //1 homme, 2 femme 9 inconnu
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
						}
				}
				//$id_shop = version_compare(_PS_VERSION_, '1.5', '>') ? Shop::getContextShopID() : 1;
				$id_shop = version_compare(_PS_VERSION_, '1.5', '>') ? $myorder['id_shop'] : 1;
				$get_cyber_path = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_path', null, null, $id_shop) : Configuration::get('CYBEROFFICE_path');
				$get_cyber_key = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_key', null, null, $id_shop) : Configuration::get('CYBEROFFICE_key');
				$get_cyber_login = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_login', null, null, $id_shop) : Configuration::get('CYBEROFFICE_login');
			$get_cyber_pass = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_pass', null, null, $id_shop) : Configuration::get('CYBEROFFICE_pass');
			$get_cyber_entity = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_entity', null, null, $id_shop) : Configuration::get('CYBEROFFICE_entity');
			$get_cyber_protocole = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_protocole', null, null, $id_shop) : Configuration::get('CYBEROFFICE_protocole');
			if (!$get_cyber_protocole) $get_cyber_protocole = 'http';

				/*echo "<pre>".print_r($cats)."</pre>";die();*/
				$ws_dol_url = $get_cyber_path.'cyberoffice/server_customer.php';
				$ws_method  = 'Create';
				$ns = $get_cyber_path.'ns/';
				$soapclient = new nusoap_client($ws_dol_url);
				if ($soapclient)
					$soapclient->soap_defencoding = 'UTF-8';
				$soapclient2 = new nusoap_client($ws_dol_url);
				if ($soapclient2)
					$soapclient2->soap_defencoding = 'UTF-8';
				// Call the WebService method and store its result in $result.
				if (version_compare(_PS_VERSION_, '1.5', '>'))
				{
					$current_shop = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));/*new Shop($id_shop);*/
					$urls = $current_shop->getUrls();

					foreach ($urls as $key_url => &$url)
						$title = $get_cyber_protocole.'://'.$url['domain'].'/'.$url['virtual_uri'];
				}
				else
					$title = $get_cyber_protocole.'://'.Tools::getShopDomain(false, true).__PS_BASE_URI__;
				if (!$this->context->cookie->id_employee) $title = $get_cyber_protocole.'://'.Tools::getShopDomain(false, true).__PS_BASE_URI__;
				$authentication = array(
					'dolibarrkey'		=>	htmlentities($get_cyber_key, ENT_COMPAT, 'UTF-8'),
					'sourceapplication'	=>	'LVSInformatique',
					'login'				=>	$get_cyber_login,
					'password'			=>	$get_cyber_pass,
					'entity'			=>	$get_cyber_entity,
					'myurl'				=>  $title
				);
				$parameters = array('authentication'=>$authentication, $cats);
				$result = $soapclient->call($ws_method, $parameters, $ns, '');

				/* TRAITEMENT ORDER
				*******************/
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
					$cats = array();
					$myline = array();
					//echo "<pre>".print_r($lines)."</pre>";die();
					foreach ($lines as $line)
					{
						//echo "<pre>".print_r($lines)."</pre>";die();
						$myline['desc']				= $line['product_name'];
						$myline['qty']				= $line['product_quantity'];
						$myline['tva_tx']			= ($line['rate']?$line['rate']:0);//(float)Tax::getProductTaxRate((int)$line['product_id']);//$line['tax_rate'];
						// $myline['fk_product']		= $line['product_id'];
						$myline['fk_product'] 		= $line['product_id'].((isset($line['product_attribute_id']) && $line['product_attribute_id'] > 0) ? '-'.$line['product_attribute_id'] : '');
						$myline['subprice']				= $line['original_product_price'];
						if ($line['original_product_price'] == $line['product_price'] && $line['original_product_price'] != 0)
							$myline['remise_percent']	= round((1 - ($line['unit_price_tax_excl'] / $line['original_product_price'])) * 100, 2);
						else $myline['remise_percent'] 	= 0;

						$myline['product_type']		= 0;
						$myline['label']			= $line['product_name'];
						$myline['reference']		= $line['product_reference'];
						$mylines[] = $myline;
						$i++;
					}
				}
				$get_cyber_warehouse = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_warehouse', null, null, $id_shop) : Configuration::get('CYBEROFFICE_warehouse');
				$get_cyber_prefix = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_prefix', null, null, $id_shop) : Configuration::get('CYBEROFFICE_prefix');
				$get_cyber_protocole = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_protocole', null, null, $id_shop) : Configuration::get('CYBEROFFICE_protocole');
				if (!$get_cyber_protocole) $get_cyber_protocole = 'http';

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

				$cats[] = array(
					'id_order' 					=> $myorder['id_order'],
					'id_carrier' 				=> $myorder['id_carrier'],
					'id_customer' 				=> $myorder['id_customer'],
					'company' 					=> $myorder['company'],
					'lastname' 					=> $myorder['lastname'],
					'firstname' 				=> $myorder['firstname'],
					'address1'					=> $myorder['address1']."\r\n".$myorder['address2'],
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
					'valid' 					=> $id_order_state,//OrderHistory::getLastOrderState($myorder['id_order'])->id,//$myorder['valid'],
					'date_add' 					=> $myorder['o_date_add'],
					'warehouse'					=> $get_cyber_warehouse,
					'lines' 					=> $mylines,
					'match'						=> $get_cyber_prefix,
					'current_shop'				=> $title_current,
					'commOK'					=> (in_array($id_order_state, $get_cyber_commande)?1:0),
					'factOK'					=> (in_array($id_order_state, $get_cyber_facture)?1:0)
				);
			}
			/*echo "<pre>".print_r($cats)."</pre>";die();*/

			/* Set the WebService URL
			*************************/
			$ws_dol_url = $get_cyber_path.'cyberoffice/server_order.php';
			$ws_method  = 'Create';
			$ns = $get_cyber_path.'ns/';
			$soapclient = new nusoap_client($ws_dol_url);
			if ($soapclient)
				$soapclient->soap_defencoding = 'UTF-8';

			$soapclient2 = new nusoap_client($ws_dol_url);//new nusoap_client($ws_dol_url, array("connection_timeout"=>15));
			if ($soapclient2)
				$soapclient2->soap_defencoding = 'UTF-8';

			// Call the WebService method and store its result in $result.
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
			if (!$this->context->cookie->id_employee) $title = $get_cyber_protocole.'://'.Tools::getShopDomain(false, true).__PS_BASE_URI__;
			$authentication = array(
				'dolibarrkey'		=>	htmlentities($get_cyber_key, ENT_COMPAT, 'UTF-8'),
				'sourceapplication'	=>	'LVSInformatique',
				'login'				=>	$get_cyber_login,
				'password'			=>	$get_cyber_pass,
				'entity'			=>	$get_cyber_entity,
				'myurl'				=>  $title
			);
			$cats100 = array_chunk($cats, 100, true);
			$i = 0;
			foreach ($cats100 as $cat100)
			{
				$parameters = array('authentication'=>$authentication, $cat100);
				$i++;
				$result = $soapclient->call($ws_method, $parameters, $ns, '');
				/*if (! $result)
				{
					//var_dump($soapclient);
					//echo '<pre>' . htmlspecialchars($soapclient->request, ENT_QUOTES) . '</pre>';
					//print '<h2>Erreur SOAP </h2>'.$soapclient->error_str;
					//print_r($result);
					echo '<pre>'.htmlspecialchars($soapclient->response, ENT_QUOTES).'</pre>';
					exit;
				}*/
			}
		} /*fin if(array)*/
	}

	public function hookActionCustomerAccountAdd($params)
	{
		/*echo "<pre>".print_r($params)."</pre>";die();*/
		$this->hookCreateAccount($params);
	}
	public function hookCreateAccount($params)
	{
		$id_shop = version_compare(_PS_VERSION_, '1.5', '>') ? Shop::getContextShopID() : 1;
		$get_cyber_client = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_client', null, null, $id_shop) : Configuration::get('CYBEROFFICE_client');
		//echo "<pre>".print_r($params)."</pre>";die();
	}

	public function hookUpdateOrderStatus($params)
	{
		/*echo "<pre>".print_r($params)."</pre>";die();*/
		$this->hookActionOrderStatusUpdate($params);
	}
	public function hookActionOrderStatusPostUpdate($params)
	{
		/*echo "<pre>".print_r($params)."</pre>";die();*/
		$this->hookActionOrderStatusUpdate($params);
	}
	public function hookPostUpdateOrderStatus($params)
	{
		/*echo "<pre>".print_r($params)."</pre>";die();*/
		$this->hookActionOrderStatusUpdate($params);
	}
	public function hookupdateproduct($params)
	{
		$this->hookaddproduct($params);
	}
	public function hookupdateProductAttribute($params)
	{
		$this->hookaddproduct($params);
	}
	public function hookdeleteproduct($params)
	{
		$this->hookaddproduct($params);
	}
	public function hookcategoryUpdate($params)
	{
	}/*$this->hookcategoryAddition($params);*/

	private function hookcategoryAddition($params)
	{
		//id_category
		require_once(dirname(__FILE__).'/nusoap/lib/nusoap.php');
		//echo "<pre>".print_r($params)."</pre>";die();
		$id_shop = version_compare(_PS_VERSION_, '1.5', '>') ? Shop::getContextShopID() : 1;
		$get_cyber_lang = version_compare(_PS_VERSION_, '1.5', '>') ? (int)Configuration::get('CYBEROFFICE_LANG', null, null, $id_shop) : (int)Configuration::get('CYBEROFFICE_LANG');
		$get_cyber_key = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_key', null, null, $id_shop) : Configuration::get('CYBEROFFICE_key');
		$get_cyber_path = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_path', null, null, $id_shop) : Configuration::get('CYBEROFFICE_path');
		$get_cyber_login = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_login', null, null, $id_shop) : Configuration::get('CYBEROFFICE_login');
		$get_cyber_pass = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_pass', null, null, $id_shop) : Configuration::get('CYBEROFFICE_pass');
		$get_cyber_entity = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_entity', null, null, $id_shop) : Configuration::get('CYBEROFFICE_entity');
		$get_cyber_protocole = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_protocole', null, null, $id_shop) : Configuration::get('CYBEROFFICE_protocole');
		if (!$get_cyber_protocole) $get_cyber_protocole = 'http';
		if ($this->my_id == null) $this->my_id = $params['category']->id_category;
		$categories = Category::getCategories($get_cyber_lang, false, false, ' AND c.id_category = '.$this->my_id);//false,false tout , true,false actif
		//echo "<pre>".print_r($categories )."</pre>";die();
		$cats = array();
		//$catsId = array();

		if ($categories)
		{
				foreach ($categories as $category)
				{
						$link = new Link;
						//print $link->getCatImageLink($category['link_rewrite'], $category['id_category'], 'category');
						//echo "<pre>".print_r($category)."</pre>";die();
						//$id_image = (file_exists(_PS_CAT_IMG_DIR_.$category['id_category'].'.jpg')) ? (int)($category['id_category']) : Language::getIsoById((int)(Configuration::get('CYBEROFFICE_LANG'))).'-default';
						// _PS_CAT_IMG_DIR_=/home/legreniei/www/img/c/
						//<img src="http://www.legrenierdulin.fr/img/tmp/category_70.jpghttp://www.legrenierdulin.fr/img/tmp/category_70.jpg">
						$image0 = $get_cyber_protocole.'://'.Tools::getShopDomain(false).$link->getCatImageLink($category['link_rewrite'], $category['id_category'], 'category');
						$ext = preg_match('/(\.gif|\.jpg|\.jpeg|\.png|\.bmp)$/i', $image0, $reg);
						$image = $get_cyber_protocole.'://'.Tools::getShopDomain(false)._PS_IMG_.'c/'.(int)$category['id_category'].$reg[1];

						$cats[] = array(	'id'			=>	$category['id_category'],
											'id_mere'		=>	$category['id_parent'],
											'label'			=>	Tools::htmlentitiesUTF8($category['name']),
											'description'	=>	strip_tags($category['description']),
											'image'			=>  $image
											);
				}
		}
			//echo "<pre>".print_r($cats)."</pre>";die();

			// Set the WebService URL
			$ws_dol_url = $get_cyber_path.'cyberoffice/server_categorie.php';
			$ws_method  = 'Create';
			$ns = $get_cyber_path.'ns/';

			$soapclient = new nusoap_client($ws_dol_url);
			if ($soapclient)
				$soapclient->soap_defencoding = 'UTF-8';

			$soapclient2 = new nusoap_client($ws_dol_url);
			if ($soapclient2)
				$soapclient2->soap_defencoding = 'UTF-8';

			// Call the WebService method and store its result in $result.
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
				'myurl'				=>  $title
			);

			//echo "<pre>".print_r($authentication)."</pre>";die();

			$parameters = array('authentication'=>$authentication, $cats);

			$result = $soapclient->call($ws_method, $parameters, $ns, '');
			/*
			if (! $result)
			{
				//var_dump($soapclient);
				//echo '<pre>' . htmlspecialchars($soapclient->request, ENT_QUOTES) . '</pre>';
				//print '<h2>Erreur SOAP </h2>'.$soapclient->error_str;
				//print_r($result);
				echo '<pre>'.htmlspecialchars($soapclient->response, ENT_QUOTES).'</pre>';
				exit;
			}
			*/
	}

	private function _scriptCustomer($addr)
	{
		require_once(dirname(__FILE__).'/nusoap/lib/nusoap.php');
		$customers = array();
		$customers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
					SELECT cu.id_customer as cu_id_customer, cu.email as cu_email, cu.firstname as cu_firstname, cu.lastname as cu_lastname,cu.id_gender as cu_id_gender,cu.birthday as cu_birthday,
					a.*, s.name AS state, s.iso_code AS state_iso,c.iso_code as c_iso_code
					FROM '._DB_PREFIX_.'customer cu
					LEFT JOIN '._DB_PREFIX_.'address a ON (a.id_customer = cu.id_customer AND a.deleted = 0)
					LEFT JOIN '._DB_PREFIX_.'country c ON (a.id_country = c.id_country)
					LEFT JOIN '._DB_PREFIX_.'state s ON (s.id_state = a.id_state)
					WHERE a.id_address = '.(int)$addr
					);
		if (is_array($customers) && count($customers) > 0)
		{
			foreach ($customers as $customer)
			{
				$cats = array();
				$cats[] = array('id_customer'	=> $customer['cu_id_customer'],
								'birthday' 		=> $customer['cu_birthday'],
								'email' 		=> $customer['cu_email'],
								'id_gender' 	=> $customer['cu_id_gender'],  //1 homme, 2 femme 9 inconnu
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
								'c_iso_code'	=> $customer['c_iso_code'],
								'client'		=> '2'
				);
			}
				$id_shop = version_compare(_PS_VERSION_, '1.5', '>') ? Shop::getContextShopID() : 1;
				$get_cyber_path = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_path', null, null, $id_shop) : Configuration::get('CYBEROFFICE_path');
				$get_cyber_key = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_key', null, null, $id_shop) : Configuration::get('CYBEROFFICE_key');
				$get_cyber_login = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_login', null, null, $id_shop) : Configuration::get('CYBEROFFICE_login');
			$get_cyber_pass = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_pass', null, null, $id_shop) : Configuration::get('CYBEROFFICE_pass');
			$get_cyber_entity = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_entity', null, null, $id_shop) : Configuration::get('CYBEROFFICE_entity');
			$get_cyber_protocole = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_protocole', null, null, $id_shop) : Configuration::get('CYBEROFFICE_protocole');
			if (!$get_cyber_protocole) $get_cyber_protocole = 'http';

				/*echo "<pre>".print_r($cats)."</pre>";die();*/
				$ws_dol_url = $get_cyber_path.'cyberoffice/server_customer.php';
				$ws_method  = 'Create';
				$ns = $get_cyber_path.'ns/';
				$soapclient = new nusoap_client($ws_dol_url);
				if ($soapclient)
					$soapclient->soap_defencoding = 'UTF-8';
				$soapclient2 = new nusoap_client($ws_dol_url);
				if ($soapclient2)
					$soapclient2->soap_defencoding = 'UTF-8';
				// Call the WebService method and store its result in $result.
				if (version_compare(_PS_VERSION_, '1.5', '>'))
				{
					$current_shop = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));/*new Shop($id_shop);*/
					$urls = $current_shop->getUrls();

					foreach ($urls as $key_url => &$url)
						$title = $get_cyber_protocole.'://'.$url['domain'].'/'.$url['virtual_uri'];
				}
				else
					$title = $get_cyber_protocole.'://'.Tools::getShopDomain(false, true).__PS_BASE_URI__;
				if (!$this->context->cookie->id_employee) $title = $get_cyber_protocole.'://'.Tools::getShopDomain(false, true).__PS_BASE_URI__;
				$authentication = array(
					'dolibarrkey'		=>	htmlentities($get_cyber_key, ENT_COMPAT, 'UTF-8'),
					'sourceapplication'	=>	'LVSInformatique',
					'login'				=>	$get_cyber_login,
					'password'			=>	$get_cyber_pass,
					'entity'			=>	$get_cyber_entity,
					'myurl'				=>  $title
				);
				$parameters = array('authentication'=>$authentication, $cats);
				$result = $soapclient->call($ws_method, $parameters, $ns, '');
		}
	}
	private function _scriptproduct($products, $attribute)
	{
		require_once(dirname(__FILE__).'/nusoap/lib/nusoap.php');
		$cats = array();
		//$catsId = array();
		//$pictures = array();
		$compte = 0;
		$id_shop = version_compare(_PS_VERSION_, '1.5', '>') ? Shop::getContextShopID() : 1;
		$lang = version_compare(_PS_VERSION_, '1.5', '>') ? (int)Configuration::get('CYBEROFFICE_LANG', null, null, $id_shop) : (int)Configuration::get('CYBEROFFICE_LANG');
		$get_cyber_prefix = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_prefix', null, null, $id_shop) : Configuration::get('CYBEROFFICE_prefix');
		$get_cyber_warehouse = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_warehouse', null, null, $id_shop) : Configuration::get('CYBEROFFICE_warehouse');
		$get_cyber_prefix = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_prefix', null, null, $id_shop) : Configuration::get('CYBEROFFICE_prefix');
		$get_cyber_protocole = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_protocole', null, null, $id_shop) : Configuration::get('CYBEROFFICE_protocole');
		if (!$get_cyber_protocole) $get_cyber_protocole = 'http';
		//echo "<pre>".print_r($products)."</pre>";die();
				$lang = version_compare(_PS_VERSION_, '1.5', '>') ? (int)Configuration::get('CYBEROFFICE_LANG', null, null, $id_shop) : (int)Configuration::get('CYBEROFFICE_LANG');

				$link = new Link;
				//$prefix = 'http://';
				$product_i = new Product((int)$products);
				if (!$product_i->id) return -1;
				//echo "<pre>".print_r($product_i)."</pre>";die();
				/*****Load Variations
				*********************/
				//echo "<pre>".print_r($combinations2)."</pre>";die();

				//$attributes_groups = $product_i->getAttributesGroups($lang);
				//echo "<pre>".print_r($attributes_groups)."</pre>";die();
				if ($attribute && $attribute > 0)
					$attributes_groups = $product_i->getAttributeCombinationsById($attribute, $lang);
				else
				{
					if (version_compare(_PS_VERSION_, '1.5', '>'))
						$attributes_groups = $product_i->getAttributeCombinations($lang);
					else
						$attributes_groups = $product_i->getAttributeCombinaisons($lang);
				}

				//echo "<pre>".print_r($attributes_groups)."</pre><br/>";

				$combinations = array();
				if ($attributes_groups && is_array($attributes_groups))
				{
					$combination_images = $product_i->getCombinationImages($lang);
					foreach ($attributes_groups as $row)//($attributes_groups AS $k => $row)
					{
						//echo "<pre>".print_r($row)."</pre>";die();
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
					}
				}
				//echo "<pre>".print_r($combinations)."</pre><br/>";
				$myproduct = array();
				$nb_combinations = 0;
				foreach ($combinations as $combination)
				{
					//echo 'je rentre';die();
					$nb_combinations = 1;
					$str_features = array();
					$model = array();
					$lang = version_compare(_PS_VERSION_, '1.5', '>') ? (int)Configuration::get('CYBEROFFICE_LANG', null, null, $id_shop) : (int)Configuration::get('CYBEROFFICE_LANG');
					$get_cyber_prefix = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_prefix', null, null, $id_shop) : Configuration::get('CYBEROFFICE_prefix');
					$get_cyber_warehouse = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_warehouse', null, null, $id_shop) : Configuration::get('CYBEROFFICE_warehouse');
					$get_cyber_prefix = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_prefix', null, null, $id_shop) : Configuration::get('CYBEROFFICE_prefix');
					$get_cyber_protocole = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_protocole', null, null, $id_shop) : Configuration::get('CYBEROFFICE_protocole');
					if (!$get_cyber_protocole) $get_cyber_protocole = 'http';

					if (isset($combination['attributes']))
					{
						foreach ($combination['attributes'] as $attribut)
						{
							$str_features[] = $attribut['group_name'].' : '.$attribut['name'];
							$model[] = $attribut['name'];
						}
					}
					$myproduct['product_url'] = $link->getProductLink((int)$product_i->id, $product_i->link_rewrite[$lang], $product_i->ean13, $lang);
					$myproduct['designation'] = Tools::htmlentitiesUTF8($product_i->name[$lang].' '.implode(' ', $model));
					$myproduct['manufacturer'] = Manufacturer::getNameById($product_i->id_manufacturer);
					$myproduct['id_manufacturer'] = $product_i->id_manufacturer;
					//$price = Product::getPriceStatic((int)$product_i->id, false, (int)$combination['id_combination']) + Product::getPriceStatic($product_i->id, false, (int)$combination['id_combination'], 6, NULL, true);
					$myproduct['price'] = $product_i->price + $combination['price'];
					$category = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
					SELECT cp.`id_category` as id
					FROM `'._DB_PREFIX_.'category_product` cp
					WHERE cp.`id_product` = '.(int)$products.'
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
					//$image = $product_i->getCover((int)$product_i->id);
					//if ($product_i->id==102) {echo "<pre>".print_r( $image )."</pre>";die();}
					$myproduct['image_url'] = $link->getImageLink($product_i->link_rewrite[$lang], $product_i->id.'-'.$id_image);
					$myproduct['description_short'] = is_array($product_i->description_short) ? strip_tags($product_i->description_short[$lang]) : strip_tags($product_i->description_short);
					$myproduct['description_short'] = trim(strip_tags(implode(', ', $str_features)).'<br />'.$myproduct['description_short']);
					$myproduct['description'] = is_array($product_i->description) ? strip_tags($product_i->description[$lang]) : strip_tags($product_i->description);
					//$myproduct['description'] = $myproduct['description'];//Tools::htmlentitiesUTF8($myproduct['description']);
					$myproduct['product_id'] = $product_i->id;
					$quantity = Product::getQuantity($product_i->id, (isset($combination['id_combination']) ? $combination['id_combination'] : null));
					$myproduct['quantity'] = $quantity;
					$myproduct['ean13'] = Tools::strlen((string)$combination['ean13']) == 13 ? $combination['ean13'] : '';
					$myproduct['upc'] = $combination['upc'];
					$myproduct['eco_tax'] = $product_i->ecotax;
					$myproduct['width'] = $product_i->width;
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
					//$myproduct['reference'] = isset($combination['reference']) ? $combination['reference'] : $product_i->reference;
				//}
				//echo "<pre>".print_r($combination)."</pre>";die();
				/////////////////////////////////////////////////
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
								'match'						=> $get_cyber_prefix
								);
				}

				if ($nb_combinations == 0)
				{
					$myproduct['product_url'] = $link->getProductLink((int)$product_i->id, $product_i->link_rewrite[$lang], $product_i->ean13, $lang);
					$myproduct['designation'] = Tools::htmlentitiesUTF8($product_i->name[$lang]);
					$myproduct['manufacturer'] = Manufacturer::getNameById($product_i->id_manufacturer);
					$myproduct['id_manufacturer'] = $product_i->id_manufacturer;
					//$price = Product::getPriceStatic((int)$product_i->id, false);
					$myproduct['price'] = $product_i->price;
					$category = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
					SELECT cp.`id_category` as id
					FROM `'._DB_PREFIX_.'category_product` cp
					WHERE cp.`id_product` = '.(int)$products.'
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
					$myproduct['reference'] = $product_i->reference;
					/*
					$images = $product->getImages($this->id_lang);
				foreach ($images as $image)
				{
					$pictures[] = $prefix.$link->getImageLink('', $product->id.'-'.$image['id_image'], NULL);
					*/
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
								'match'						=> $get_cyber_prefix
								);
				}
		$compte++;

		//$this->errors[] = 'Product cyber';
		//$this->context->controller->errors[];
		//echo "<pre>".print $compte."</pre>";die();
		//echo "<pre>".print_r($cats)."</pre>";die();
		// Set the WebService URL
		$get_cyber_key = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_key', null, null, $id_shop) : Configuration::get('CYBEROFFICE_key');
		$get_cyber_path = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_path', null, null, $id_shop) : Configuration::get('CYBEROFFICE_path');
		$get_cyber_login = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_login', null, null, $id_shop) : Configuration::get('CYBEROFFICE_login');
		$get_cyber_pass = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_pass', null, null, $id_shop) : Configuration::get('CYBEROFFICE_pass');
		$get_cyber_entity = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_entity', null, null, $id_shop) : Configuration::get('CYBEROFFICE_entity');
		$get_cyber_protocole = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_protocole', null, null, $id_shop) : Configuration::get('CYBEROFFICE_protocole');
		if (!$get_cyber_protocole) $get_cyber_protocole = 'http';

		$ws_dol_url = $get_cyber_path.'cyberoffice/server_product.php';
		$ws_method  = 'Create';
		$ns = $get_cyber_path.'ns/';
		//$client = new SoapClient($wsdl, array("connection_timeout"=>15));
		$soapclient = new nusoap_client($ws_dol_url);
		if ($soapclient)
			$soapclient->soap_defencoding = 'UTF-8';

		$soapclient2 = new nusoap_client($ws_dol_url);//new nusoap_client($ws_dol_url, array("connection_timeout"=>15));
		if ($soapclient2)
			$soapclient2->soap_defencoding = 'UTF-8';

		// Call the WebService method and store its result in $result.
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
			'myurl'				=>  $title
		);

		/*echo "<pre>".print_r($authentication )."</pre>";die();*/
		//var_dump($cats);die();
		$cats100 = array_chunk($cats, 100, true);
		$i = 0;
		foreach ($cats100 as $cat100)
		{
			$parameters = array('authentication'=>$authentication, $cat100);
			$i++;

			$result = $soapclient->call($ws_method, $parameters, $ns, '');
			/*
			if (! $result)
			{
				//var_dump($soapclient);
				//echo '<pre>' . htmlspecialchars($soapclient->request, ENT_QUOTES) . '</pre>';
				//print '<h2>Erreur SOAP </h2>'.$soapclient->error_str;
				//print_r($result);
				echo '<pre>'.htmlspecialchars($soapclient->response, ENT_QUOTES).'</pre>';
				exit;
			}
			*/
		}

	}

	public function testConfig()
	{
		require_once(dirname(__FILE__).'/nusoap/lib/nusoap.php');		// Include SOAP

		$id_shop = version_compare(_PS_VERSION_, '1.5', '>') ? Shop::getContextShopID() : 1;

		$get_cyber_protocole = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_protocole', null, null, $id_shop) : Configuration::get('CYBEROFFICE_protocole');
		if (!$get_cyber_protocole) $get_cyber_protocole = 'http';

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

		$myurl = $title;

		$get_cyber_key = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_key', null, null, $id_shop) : Configuration::get('CYBEROFFICE_key');
		$get_cyber_path = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_path', null, null, $id_shop) : Configuration::get('CYBEROFFICE_path');
		$get_cyber_login = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_login', null, null, $id_shop) : Configuration::get('CYBEROFFICE_login');
		$get_cyber_pass = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_pass', null, null, $id_shop) : Configuration::get('CYBEROFFICE_pass');
		$get_cyber_entity = version_compare(_PS_VERSION_, '1.5', '>') ? Configuration::get('CYBEROFFICE_entity', null, null, $id_shop) : Configuration::get('CYBEROFFICE_entity');

		$ws_dol_url = $get_cyber_path.'cyberoffice/server_config.php';

		$ws_method  = 'getConfig';
		$ns = $get_cyber_path.'ns/';

		// Set the WebService URL

		$soapclient = new nusoap_client($ws_dol_url);
		if ($soapclient)
			$soapclient->soap_defencoding = 'UTF-8';

		$soapclient2 = new nusoap_client($ws_dol_url);
		if ($soapclient2)
			$soapclient2->soap_defencoding = 'UTF-8';

		// Call the WebService method and store its result in $result.
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
//echo $ws_dol_url.'*'.$title.'*'.$get_cyber_path;exit;
		$authentication = array(
			'dolibarrkey'=>htmlentities($get_cyber_key, ENT_COMPAT, 'UTF-8'),
			'sourceapplication'	=> 'LVSInformatique',
			'login'				=> $get_cyber_login,
			'password'			=> $get_cyber_pass,
			'entity'			=> $get_cyber_entity,
			'myurl'				=> $title
		);
		$myparam = array(
			'repertoire'=>$get_cyber_path,
			'supplier' 	=> 1,
			'category' 	=> 2,
			'myurl'		=> $myurl
		);
		$parameters = array('authentication'=>$authentication, $myparam);

		$result = $soapclient->call($ws_method, $parameters, $ns, '');
		//print_r($result);
		/*if ($soapclient->fault)
			echo "Error <br/>".$soapclient->getError();*/
		/*var_dump($soapclient);
			echo '<pre>' . htmlspecialchars($soapclient->request, ENT_QUOTES) . '</pre>';
			print '<h2>Erreur SOAP </h2>'.$soapclient->error_str;
			print_r($result);
			echo '<pre>'.htmlspecialchars($soapclient->response, ENT_QUOTES).'</pre>';*/
		if ($soapclient->fault)
		{
			$result = array(
				'result'=>array('result_code' => 'KO', 'result_label' => 'KO'),
				'repertoire' => $get_cyber_path,
				'repertoireTF' => 'KO',
				'webservice' => '<b>'.$soapclient->getError().'</b>',
				'dolicyber' => 'KO',
				'indice' => -1
			);
		}
		else
		{
			$err_msg = $soapclient->getError();
			if ($err_msg)
			{
				$result = array(
				'result'=>array('result_code' => 'KO', 'result_label' => 'KO'),
				'repertoire' => $get_cyber_path,
				'repertoireTF' => 'KO',
				'webservice' => '<b>'.$err_msg.'</b>',
				'dolicyber' => 'KO',
				'indice' => -1
				);
			}
		}
		//print '<pre>'.print_r($authentication).print_r($myparam).print_r($result).'</pre>';die();
		return $result;

		//return $result['description']['repertoire'];
	}
}
?>