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

require_once(_PS_MODULE_DIR_.'opartdevis/models/OpartQuotation.php');

if (!defined('_PS_VERSION_'))
	exit;

class Opartdevis extends PaymentModule {
	public function __construct()
	{
		$this->name = 'opartdevis';
		$this->tab = 'payments_gateways';
		$this->version = '3.0.7';
		$this->author = 'Op\'art - Olivier CLEMENCE';
		$this->module_key = '5165c4489bcc64253b1c1cd98926a8a4';
		$this->need_instance = 0;
		$this->erreurs = array();
		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Op\'art devis');
		$this->description = $this->l('This module allows your customers to create a quotation.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete these details?');

		$this->context->smarty->assign(array(
			'module_name' => $this->name,
			'moduledir' => _MODULE_DIR_.$this->name.'/'
		));
		if (!extension_loaded('curl'))
			$this->warning = $this->l(' To properly display PDF, Php Curl extensions have to be loaded.');
	}

	public function install()
	{
		if (version_compare(_PS_VERSION_, '1.5.0', '<'))
			return false;
		$sql = array();
		include(dirname(__FILE__).'/sql/install.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->execute($s))
				return false;

                //1.6.1.0 specific price bug fixe
                if(version_compare(_PS_VERSION_, '1.6.1.0', '=')) {
                    $sqlUpdateIndex[]="ALTER TABLE "._DB_PREFIX_."specific_price DROP INDEX id_product_2";
                    $sqlUpdateIndex[]="ALTER TABLE "._DB_PREFIX_."specific_price ADD INDEX id_product_2 (id_product,id_shop,id_shop_group,id_currency,id_country,id_group,id_customer,id_product_attribute,from_quantity,id_specific_price_rule,id_cart,`from`,`to`)";
                    foreach ($sqlUpdateIndex as $sql)
                        if (!Db::getInstance()->execute($sql))
                            return false;                    
                }
                        
		// Install Tabs
		$this->installQuotationModuleTab();

		//Init
		$rand_key = Tools::substr(md5(rand(0, 1000000)), 0, 7);
		Configuration::updateValue('PS_OPART_DEVIS_SECURE_KEY', $rand_key);
		Configuration::updateValue('OPARTDEVIS_EXPIRETIME', 0);
		Configuration::updateValue('OPARTDEVIS_MAXPRODFIRSTPAGE', 7);
		Configuration::updateValue('OPARTDEVIS_MAXPRODPAGE', 10);
		Configuration::updateValue('OPARTDEVIS_SHOWFREEFORM', 1);
		Configuration::updateValue('OPARTDEVIS_SHOWACCOUNTBTN', 1);
                
		if (!parent::install() ||
			!$this->registerHook('payment') ||
			!$this->registerHook('displayLeftColumn') ||
			!$this->registerHook('displayShoppingCart') ||
			!$this->registerHook('displayCustomerAccount') ||
			!$this->registerHook('header') ||
			!$this->registerHook('displayAdminView') ||
			!$this->registerHook('actionObjectCartUpdateBefore') ||
			!$this->registerHook('displayBeforeShoppingCartBlock') ||
			!$this->registerHook('actionOrderStatusUpdate'))
			return false;
		return true;
	}

	public function uninstall()
	{
		Configuration::deleteByName('PS_OPART_DEVIS_SECURE_KEY');
		Configuration::deleteByName('OPARTDEVIS_SENDMAILTOCUSTOMER');
		Configuration::deleteByName('OPARTDEVIS_SENDMAILTOADMIN');
		Configuration::deleteByName('OPARTDEVIS_ADMINCONTACTID');
		Configuration::deleteByName('OPARTDEVIS_MAXPRODFIRSTPAGE');
		Configuration::deleteByName('OPARTDEVIS_MAXPRODPAGE');
		Configuration::deleteByName('OPARTDEVIS_EXPIRETIME');
		Configuration::updateValue('OPARTDEVIS_SHOWFREEFORM');
		Configuration::updateValue('OPARTDEVIS_SHOWACCOUNTBTN');

		$sql = array();
		include(dirname(__FILE__).'/sql/uninstall.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->execute($s))
				return false;

		/* Uninstall Tabs */
		$tab = new Tab((int)Tab::getIdFromClassName('AdminOpartdevis'));
		$tab->delete();

		if (!parent::uninstall())
			return false;
		return true;
	}

	private function installQuotationModuleTab()
	{
		$tab = new Tab();
		$tab->class_name = 'AdminOpartdevis';
		$tab->module = 'opartdevis';
		$tab->id_parent = (int)Tab::getIdFromClassName('AdminOrders');
		$tab->position = Tab::getNewLastPosition($tab->id_parent);
		foreach (Language::getLanguages(false) as $lang)
			$tab->name[(int)$lang['id_lang']] = 'Quotations';
		$tab->save();
	}

        public function isFreezCart($cart = null) {
            if($cart == null && !isset($this->context->cart))
                return false;
            $cart=($cart != null)?$cart:$this->context->cart;
            $quote = OpartQuotation::getByIdCart($cart->id);
            if(!(is_object($quote) && $quote->statut != 0)) 
                return false;
            
            return $quote;
        }
        
        public function hookdisplayBeforeShoppingCartBlock() {
            if(!isset($this->context->cart))
                return false;
            $quote = OpartQuotation::getByIdCart($this->context->cart->id);
            if(is_object($quote)) {
                $this->smarty->assign(array(
                    'quote' => $quote,
                ));
                return $this->display(__FILE__, 'views/templates/hook/displayBeforeShoppingCartBlock.tpl');
            }
        }
        
	public function hookPayment()
	{
            if($this->isFreezCart())
                return false;
            
            $this->smarty->assign(array(
		'this_path' => $this->_path,
		'this_path_opartdevis' => $this->_path,
            ));
            if (version_compare(_PS_VERSION_, '1.6.0', '<'))
		return $this->display(__FILE__, 'views/templates/hook/payment_15.tpl');
            else
		return $this->display(__FILE__, 'views/templates/hook/payment.tpl');
	}

	public function hookHeader()
	{
		$this->context->controller->addCSS(_MODULE_DIR_.'opartdevis/views/css/opartdevis_1.css');
                if($this->isFreezCart()) {
                    Context::getContext()->cookie->check_cgv = true;
                    if(isset($this->context->controller->step) 
                        && get_class($this->context->controller)=="OrderController" 
                        && ($this->context->controller->step == 2 || $this->context->controller->step == 1) ) {
                        //$this->context->controller->errors[] = $this->l('You are not allowed to modify this cart'); 
                        Tools::redirect('index.php?controller=order&step=3');
                    }
                }            
	}

        public function hookActionObjectCartUpdateBefore($vars) {
            // test if cart is linked to validated quote 
            if($quote = $this->isFreezCart($vars['cart'])) {
                if (Tools::getIsset('add') || Tools::getIsset('update') || Tools::getIsset('delete') || Tools::getIsset('changeAddressDelivery'))
                    $this->context->controller->errors[] = sprintf($this->l('You are not allowed to modify this cart because is linked to the quotation number: %s. Go to your cart for more information'),$quote->id_opartdevis); 
            }
        }
        
        public function hookActionOrderStatusUpdate($vars) {
            $orderObj = new Order($vars['id_order']);
            $quote = OpartQuotation::getQuotationByCartId($orderObj->id_cart);
            if(is_object($quote) && $quote->statut == 1) {
                $quote->statut = 2;
                $quote->id_order = $vars['id_order'];
                $quote->save();
                
                $message = sprintf($this->l('Order created from quotation number: %s'),$quote->id_opartdevis);
                //add msg to order
                $msg = new Message();
                $msg->message = $message;
                $msg->id_cart = (int)$orderObj->id_cart;
                $msg->id_customer = (int)($orderObj->id_customer);
                $msg->id_order = (int)$orderObj->id;
                $msg->private = 1;
                $msg->add();
            }       
            
        }
        
	public function hookDisplayShoppingCart()
	{            
            $cartId = $this->context->cart->id;
            $quotationObj = OpartQuotation::getQuotationByCartId($cartId);
            $this->smarty->assign(array(
                'quote' => $quotationObj
            ));
            if($this->isFreezCart())
                $html = '';
            else 
                $html = $this->display(__FILE__, 'views/templates/hook/displayButtonCart.tpl');
            
            if (version_compare(_PS_VERSION_, '1.6.0', '<')) 
                $html .= $this->display(__FILE__, 'views/templates/hook/displayBeforeShoppingCartBlock.tpl');
            
            return $html;
	}

	public function hookDisplayLeftColumn()
	{
		/* $this->smarty->assign('idCart',$this->context->cart->id); */
		$html = $this->display(__FILE__, 'views/templates/hook/displayButton.tpl');
		if (Configuration::get('OPARTDEVIS_SHOWFREEFORM') == 1)
			$html .= $this->display(__FILE__, 'views/templates/hook/displayButton2.tpl');
		return $html;
	}

	public function hookDisplayRightColumn()
	{
		return $this->hookDisplayLeftColumn();
	}

	public function hookDisplayFooter()
	{
		return $this->hookDisplayLeftColumn();
	}

	public function hookDisplayTop()
	{
		$this->smarty->assign('this_path', dirname(__FILE__));
		return $this->display(__FILE__, 'views/templates/hook/displayTop.tpl');
	}

	public function hookDisplayCustomerAccount()
	{    
            if(Configuration::get('OPARTDEVIS_SHOWACCOUNTBTN') == 0) {
                $id_customer = $this->context->customer->id;
                $sql = 'SELECT * FROM `'._DB_PREFIX_.'opartdevis` WHERE id_customer='.(int)$id_customer;
                $quotations = Db::getInstance()->executeS($sql);
                if(count($quotations) == 0)
                    return false;
            }
            if (version_compare(_PS_VERSION_, '1.6.0', '<'))
		return $this->display(__FILE__, 'views/templates/front/myaccount_15.tpl');
            else
		return $this->display(__FILE__, 'views/templates/front/myaccount.tpl');
	}

	public function hookDisplayAdminView()
	{
		$controller_name = Tools::getValue('controller');

		if ($controller_name == 'AdminCarts')
		{
			$token = Tools::getAdminToken('AdminOpartdevis'.(int)Tab::getIdFromClassName('AdminOpartdevis').(int)Context::getContext()->employee->id);
			$id_cart = Tools::getValue('id_cart');
			$href = 'index.php?controller=AdminOpartdevis&transformThisCartId='.$id_cart.'&token='.$token;
			return '<a class="btn btn-default" href="'.$href.'"><i class="icon-shopping-cart"></i> '.$this->l('Create a quotation from this cart').'</a>';
		}
	}
        
	public function getContent()
	{
		$fields_value = array();
		$this->postProcess();
		$fields_value['sendMailtoCustomer'] = Configuration::get('OPARTDEVIS_SENDMAILTOCUSTOMER');
		$fields_value['sendMailtoAdmin'] = Configuration::get('OPARTDEVIS_SENDMAILTOADMIN');
		$fields_value['adminContactId'] = Configuration::get('OPARTDEVIS_ADMINCONTACTID');
		$fields_value['freeText'] = OpartQuotation::getQuotationText(0);
		$fields_value['validationText'] = OpartQuotation::getQuotationText(1);
		$fields_value['goodforagrementText'] = OpartQuotation::getQuotationText(2);
		$fields_value['maxProdFirstPage'] = Configuration::get('OPARTDEVIS_MAXPRODFIRSTPAGE');
		$fields_value['maxProdPage'] = Configuration::get('OPARTDEVIS_MAXPRODPAGE');
		$fields_value['expireTime'] = Configuration::get('OPARTDEVIS_EXPIRETIME');
		$fields_value['showFreeForm'] = Configuration::get('OPARTDEVIS_SHOWFREEFORM');
		$fields_value['showAccountBtn'] = Configuration::get('OPARTDEVIS_SHOWACCOUNTBTN');
                if (isset($fields_value))
			$this->context->smarty->assign('fieldsValue', $fields_value);

		/* 1.5 compatibility */
		$languages = Language::getLanguages();
		foreach (Language::getLanguages() as $key => $lang)
			$languages[$key]['is_default'] = ($lang['id_lang'] == Context::getContext()->language->id) ? 1 : 0;

		$this->context->smarty->assign(array(
			'adminModuleUrl' => 'index.php?controller=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
			'erreurs' => $this->erreurs,
			'languages' => $languages,
			'defaultLangId' => $this->context->language->id,
			'contacts' => Contact::getContacts($this->context->language->id)
		));

		$html = $this->display(__FILE__, 'views/templates/admin/header.tpl');

		if (version_compare(_PS_VERSION_, '1.6.0', '<'))
		{
			$html .= $this->display(__FILE__, 'views/templates/admin/form_15.tpl');
			$this->context->controller->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
			$this->context->controller->addJS(_PS_JS_DIR_.'tinymce.inc.js');
		}
		else
			$html .= $this->display(__FILE__, 'views/templates/admin/form.tpl');

		$html .= $this->display(__FILE__, 'views/templates/admin/help.tpl');
		return $html;
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitSettings'))
		{
			Configuration::updateValue('OPARTDEVIS_SENDMAILTOCUSTOMER', (Tools::getValue('sendMailtoCustomer') == 1) ? 1 : 0);
			Configuration::updateValue('OPARTDEVIS_SENDMAILTOADMIN', (Tools::getValue('sendMailtoAdmin') == 1) ? 1 : 0);

			Configuration::updateValue('OPARTDEVIS_ADMINCONTACTID', Tools::getValue('adminContactId'));

			$max_prod_first_page = Tools::getValue('maxProdFirstPage');
			if (is_numeric($max_prod_first_page))
				Configuration::updateValue('OPARTDEVIS_MAXPRODFIRSTPAGE', $max_prod_first_page);
			else
				$this->erreurs[] = Tools::displayError($this->l('max product on first page have to be a number'));

			$max_prod_page = Tools::getValue('maxProdPage');
			if (is_numeric($max_prod_page))
				Configuration::updateValue('OPARTDEVIS_MAXPRODPAGE', $max_prod_page);
			else
				$this->erreurs[] = Tools::displayError($this->l('max product on pages have to be a number'));

			$expire_time = Tools::getValue('expireTime');
			if (is_numeric($max_prod_page))
				Configuration::updateValue('OPARTDEVIS_EXPIRETIME', $expire_time);
			else
				$this->erreurs[] = Tools::displayError($this->l('Expiration time have to be a number'));

			Configuration::updateValue('OPARTDEVIS_SHOWFREEFORM', (Tools::getValue('showFreeForm') == 1) ? 1 : 0);

			Configuration::updateValue('OPARTDEVIS_SHOWACCOUNTBTN', (Tools::getValue('showAccountBtn') == 1) ? 1 : 0);

			/* delete all text */
			$sql = 'DELETE FROM '._DB_PREFIX_.'opartdevis_text';
			db::getInstance()->execute($sql);
			$insert = '';
			foreach (Language::getLanguages() as $lang)
			{
				//freetext
				$values = '"'.pSQL(Tools::getValue('freeText_'.$lang['id_lang']), true).'",0,'.(int)$lang['id_lang'];
				$insert .= ($insert == '') ? '('.$values.')' : ',('.$values.')';
				//validationText
				$values = "'".pSQL(Tools::getValue('validationText_'.$lang['id_lang']), true)."',1,".(int)$lang['id_lang'];
				$insert .= ($insert == '') ? '('.$values.')' : ',('.$values.')';
				//goodforagrementText
				$values = "'".pSQL(Tools::getValue('goodforagrementText_'.$lang['id_lang']), true)."',2,".(int)$lang['id_lang'];
				$insert .= ($insert == '') ? '('.$values.')' : ',('.$values.')';
			}
			$sql = 'INSERT INTO '._DB_PREFIX_.'opartdevis_text (text_value,text_type,id_lang) VALUES '.$insert;

			db::getInstance()->execute($sql);
		}
	}

}

?>