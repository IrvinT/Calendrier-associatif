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

require_once _PS_MODULE_DIR_ . 'opartdevis/models/OpartQuotation.php';

class AdminOpartdevisController extends ModuleAdminController {

	public function __construct() {
		$this->bootstrap = true;
		$this->table = 'opartdevis';
		$this->name = 'opartdevis';
		$this->className = 'OpartQuotation';
		$this->lang = false;
		$this->deleted = false;
		$this->colorOnBackground = false;
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected items'),
				'confirm' => $this->l('Delete selected items?')));
		$this->context = Context::getContext();

		$this->_select = '
		a.*, a.date_add expire_date, a.id_cart company_name,
		CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`';

		$this->_join = '
		LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)';

		$this->_orderBy = 'a.date_add';
		$this->_orderWay = 'DESC';
		$this->context->smarty->assign(array(
			'module_name' => $this->name,
			'moduledir' => _MODULE_DIR_ . $this->name . '/',
			'ps_base_url' => _PS_BASE_URL_SSL_
		));
		if (!(int) Configuration::get('PS_SHOP_ENABLE'))
			$this->errors[] = ($this->l('Your shop is not enable: Carrier and customer list will not be loaded'));
		
                    $this->fields_list = array(
			'id_opartdevis' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 'auto'
			),
			'customer' => array(
				'title' => $this->l('Customer'),
				'width' => 'auto',
                                'orderby' => false,
                                'search' => false,
			),
			'id_customer_thread' => array(
				'title' => $this->l('Message'),
				'width' => 'auto',
				'callback' => 'showMessageLink',
                                'orderby' => false,
                                'search' => false,
			),
			'date_add' => array(
				'title' => $this->l('Date'),
				'width' => 'auto',
                                'filter_key' => 'a!date_add'
			),
			'expire_date' => array(
				'title' => $this->l('Expire date'),
				'width' => 'auto',
                                'callback' => 'displayExpireDate',
                                'orderby' => false,
                                'search' => false,
			),
			'id_cart' => array(
				'title' => $this->l('Total'),
				'width' => 'auto',
                                'callback' => 'getOrderTotalUsingTaxCalculationMethod',
                                'orderby' => false,
                                'search' => false,
			),
			'company_name' => array(
				'title' => $this->l('Company'),
				'width' => 'auto',
                                'callback' => 'getCompanyName',
                                'orderby' => false,
                                'search' => false,
			),
			'statut' => array(
				'title' => $this->l('Statut'),
				'width' => 'auto',
                                'callback' => 'getStatutName',
                                'orderby' => false,
                                'search' => false,
			),
			'id_order' => array(
				'title' => $this->l('Order'),
				'width' => 'auto',
                                'callback' => 'showOrderLink',
                                'orderby' => false,
                                'search' => false,
			)
                    
		);
                
                parent::__construct();
	}

	public function setMedia() {
		$this->addCSS(__PS_BASE_URI__ . 'modules/opartdevis/views/css/opartdevis_admin.css');
		return parent::setMedia();
	}

        public function getStatutName($val) {
            $nameArray[0] = $this->l('Validation needed');
            $nameArray[1] = $this->l('Validated');
            $nameArray[2] = $this->l('Ordered');
            $nameArray[3] = $this->l('Expired');
            
            return $nameArray[$val];
        }
        
	public function renderList() {
            /* delete quote without cart */
                OpartQuotation::deleteQuoteWithoutCart();
                OpartQuotation::checkValidityAllquote();
            
		$this->addRowAction('view');
		$this->addRowAction('edit');
		$this->addRowAction('viewcustomer');
		$this->addRowAction('createorder');
		$this->addRowAction('sendbymail');
		$this->addRowAction('sendbymailtoadmin');
		$this->addRowAction('validate');
		$this->addRowAction('delete');

		$this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected items'),
				'confirm' => $this->l('Delete selected items?')
			)
		);
		

		$this->initToolbar();
		$lists = parent::renderList();
		//parent::initToolbar();
		//$html=$this->display(_PS_MODULE_DIR_.'/opartdevis', 'views/templates/admin/header.tpl');
		$html = $this->context->smarty->fetch(_PS_MODULE_DIR_ . '/opartdevis/views/templates/admin/header.tpl');
		$html .= $lists;
		$html .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . '/opartdevis/views/templates/admin/help.tpl');
		return $html;
	}

        public function displayExpireDate($val) {
           return OpartQuotation::calc_expire_date($val);
        }
        
        public static function getOrderTotalUsingTaxCalculationMethod($id_cart)
        {
            //die('afficher msg erreur si cart existe plus');
            $context = Context::getContext();
            $context->cart = new Cart($id_cart);
            if(!$context->cart->id)
                return 'error';
            $context->currency = new Currency((int)$context->cart->id_currency);
            $context->customer = new Customer((int)$context->cart->id_customer);
            return Cart::getTotalCart($id_cart, true, Cart::BOTH);
        }
        
        public static function getCompanyName($id_cart)
        {
            //$context = Context::getContext();
            $cart = new Cart($id_cart);
            $address_invoice = new Address($cart->id_address_invoice);
            return $address_invoice->company;
        }
        /*
        public function displayTotal($val) {
            return '50';
           return OpartQuotation::calc_expire_date($val);
        }
        */
	public function showMessageLink($val) {
		if ($val != 0) {
			$token = Tools::getAdminToken('AdminCustomerThreads' .
					(int) Tab::getIdFromClassName('AdminCustomerThreads') .
					(int) $this->context->cookie->id_employee);
			$href = 'index.php?controller=AdminCustomerThreads&id_customer_thread=' . $val . '&viewcustomer_thread&token=' . $token;
			return '<a href="' . $href . '">' . $this->l('read') . '</a>';
		} else
			return '-';
	}

        public function showOrderLink($val) {
		if ($val != 0) {
			$token = Tools::getAdminToken('AdminOrders' .
					(int) Tab::getIdFromClassName('AdminOrders') .
					(int) $this->context->cookie->id_employee);
			$href = 'index.php?controller=AdminOrders&id_order=' . $val . '&vieworder&token=' . $token;
			return '<a href="' . $href . '">' . $val . '</a>';
		} else
			return '-';
	}
        
	public function displayViewcustomerLink($token = null, $id) {
		if (!array_key_exists('viewcustomer', self::$cache_lang))
			self::$cache_lang['viewcustomer'] = $this->l('View customer');
		$token = Tools::getAdminToken('AdminCustomers' . (int) Tab::getIdFromClassName('AdminCustomers') . (int) $this->context->cookie->id_employee);

		$new_quotation = new OpartQuotation($id);
		$this->context->smarty->assign(array(
			'href' => 'index.php?controller=AdminCustomers&id_customer=' . $new_quotation->id_customer . '&viewcustomer&token=' . $token,
			'action' => self::$cache_lang['viewcustomer'],
		));
		//return $this->context->smarty->fetch('helpers/list/list_action_supply_order_change_state.tpl');
                
		return $this->context->smarty->fetch('helpers/list/list_action_default.tpl');
	}

	public function displayCreateorderLink($token = null, $id) {
		if (!array_key_exists('createorder', self::$cache_lang))
			self::$cache_lang['createorder'] = $this->l('Create order');
		$token = Tools::getAdminToken('AdminOrders' . (int) Tab::getIdFromClassName('AdminOrders') . (int) $this->context->cookie->id_employee);

		$new_quotation = new OpartQuotation($id);
		$this->context->smarty->assign(array(
			'href' => 'index.php?controller=AdminOrders&id_cart=' . $new_quotation->id_cart . '&addorder&token=' . $token,
			'action' => self::$cache_lang['createorder'],
		));
		return $this->context->smarty->fetch('helpers/list/list_action_default.tpl');
	}

        public function displayValidateLink($token = null, $id) {
		if (!array_key_exists('validate', self::$cache_lang))
			self::$cache_lang['validate'] = $this->l('Validate');
                
		//$token = Tools::getAdminToken('AdminOrders' . (int) Tab::getIdFromClassName('AdminOrders') . (int) $this->context->cookie->id_employee);

                $quote = new OpartQuotation($id);
                if($quote->statut != 0)
                    return '';
                
		$this->context->smarty->assign(array(
			'href' => 'index.php?controller=AdminOpartdevis&id_opartdevis=' . $id . '&validate&token=' . ($token != null ? $token : $this->token),
			'action' => self::$cache_lang['validate'],
		));
		//return $this->context->smarty->fetch('helpers/list/list_action_addstock.tpl');
		return $this->context->smarty->fetch('helpers/list/list_action_default.tpl');
	}
        
	public function displaySendbymailLink($token = null, $id) {
		if (!array_key_exists('sendbymail', self::$cache_lang))
			self::$cache_lang['sendbymail'] = $this->l('Send by email to customer');

		$this->context->smarty->assign(array(
			'href' => 'index.php?controller=AdminOpartdevis&id_opartdevis=' . $id . '&sendbymail&token=' . ($token != null ? $token : $this->token),
			'action' => self::$cache_lang['sendbymail'],
		));
		//return $this->context->smarty->fetch('helpers/list/list_action_addstock.tpl');
		return $this->context->smarty->fetch('helpers/list/list_action_default.tpl');
	}

	public function displaySendbymailtoadminLink($token = null, $id) {
		$this->context->smarty->assign(array(
			'href' => self::$currentIndex . '&' . $this->identifier . '=' . $id . '&sendbymailtoadmin&token=' . ($token != null ? $token : $this->token),
			'confirm' => $this->l('Are you sure you want to send this quotation to customer?'),
			'action' => $this->l('Send mail to admin'),
			'id' => $id,
		));
		//return $this->context->smarty->fetch('helpers/list/list_action_addstock.tpl');
		return $this->context->smarty->fetch('helpers/list/list_action_default.tpl');
	}

	public function renderForm() {
		if (!($obj = $this->loadObject(true)))
			return;

		if (isset($obj->id_customer) && is_numeric($obj->id_customer))
			$customer = new Customer($obj->id_customer);

		//p($obj);

		if (isset($obj->id_cart) && is_numeric($obj->id_cart)) {
			$cart = new Cart($obj->id_cart);
			$products = $cart->getProducts();
			Context::getContext()->cart = $cart;
		}
		if (isset($products) && count($products) > 0) {
			foreach ($products as &$prod) {
                                //get specifique price
				$sql = 'SELECT price,from_quantity FROM ' . _DB_PREFIX_ . 'specific_price WHERE id_cart=' . (int) $obj->id_cart
					. ' AND id_product=' . (int) $prod['id_product'] . ' AND id_product_attribute=' . (int) $prod['id_product_attribute'];
				$row = db::getInstance()->getRow($sql);
				$prod['specific_price'] = $row['price'];
				$prod['specific_qty'] = $row['from_quantity'];
                                
                                //get catalog price
                                $prod['catalogue_price'] = Product::getPriceStatic($prod['id_product'], false, $prod['id_product_attribute'], 6, null, false, true, $prod['cart_quantity'],false, null, null, null, $specific_price_output, true, true, null, false);
                                
			}
		}
		//p($cart);
		//$accessories=Product::getProducts($this->context->language->id, 0, 1000, 'name', 'desc',false,true,$this->context);
		$accessories = array();
		//p($cart->getSummaryDetails());

		$this->context->smarty->assign(array(
			'obj' => $obj,
			'customer' => (isset($customer)) ? $customer : null,
			'cart' => (isset($cart)) ? $cart : null,
			'summary' => (isset($cart)) ? $cart->getSummaryDetails() : null,
			'products' => (isset($products)) ? $products : null,
			'accessories' => $accessories,
			'flag' => false,
			'view_flag' => _MODULE_DIR_,
			'dir_flag' => Tools::getValue('id_opartdevis'),
			'pathuploadfiles' => _PS_MODULE_DIR_ . 'opartdevis/uploadfiles/'.Tools::getValue('id_opartdevis'),
			'cart_rules' => $this->getAllCartRules(),
			'id_lang_default' => $this->context->language->id,
			'opart_module_dir' => _MODULE_DIR_ . $this->name,
			'href' => self::$currentIndex . '&AdminOpartdevis&addopartdevis&token=' . $this->token,
			'hrefCancel' => self::$currentIndex . '&token=' . $this->token,
			'opart_token' => $this->token
		));

		$this->addJqueryPlugin(array('autocomplete'));
		$this->addJS(_MODULE_DIR_ . $this->name . '/views/js/admin.js');
		$this->addJS(_MODULE_DIR_ . $this->name . '/views/js/front.js');
		$html = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/admin/header.tpl');
		$html .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/admin/form_quotation.tpl');
		$html .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/admin/help.tpl');
		if (version_compare(_PS_VERSION_, '1.6.0', '<'))
			$this->addCSS(_MODULE_DIR_ . $this->name . '/views/css/admin_15.css');

		return $html;
	}

	private function getAllCartRules() {
		$sql = 'SELECT c.id_cart_rule, c.code, c.description, cl.name FROM ' . _DB_PREFIX_ . 'cart_rule c LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_lang';
		$sql .= ' cl ON (c.id_cart_rule=cl.id_cart_rule) WHERE c.active=1 GROUP BY c.id_cart_rule ORDER BY c.id_cart_rule';

		$rules = db::getInstance()->executeS($sql);
		return $rules;
	}

	public function postProcess() {
		if (Tools::getIsset('ajax_carrier_list')) {
			$quoteObj = new OpartQuotation();
			$json = $quoteObj->getCarriersList();
			echo $json;
			die();
		}

		if (Tools::getIsset('ajax_customer_list')) {
			$query = Tools::getValue('q', false);
			$context = Context::getContext();

			$sql = 'SELECT c.`id_customer`, c.`firstname`, c.`lastname` 
			FROM `' . _DB_PREFIX_ . 'customer` c 
			WHERE (c.firstname LIKE \'%' . pSQL($query) . '%\' OR c.lastname LIKE \'%' . pSQL($query) . '%\') GROUP BY c.id_customer';

			$customer_list = Db::getInstance()->executeS($sql);

			die(Tools::jsonEncode($customer_list));
		}

		if (Tools::getIsset('ajax_product_list')) {
			$query = Tools::getValue('q', false);
			$context = Context::getContext();

			$sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, p.`price`, pl.`name`
			FROM `' . _DB_PREFIX_ . 'product` p
			LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int) Context::getContext()->language->id . ')
			WHERE (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\') GROUP BY p.id_product';

			$prod_list = Db::getInstance()->executeS($sql);

			foreach ($prod_list as $prod)
				echo trim($prod['id_product']) . '|' . trim($prod['name']) . '|' . trim($prod['price']) . "\n";
			die();
		}

		if (Tools::getIsset('ajax_load_cart_rule')) {
			$id_obj = Tools::getValue('id_cart_rule');
			$context = Context::getContext();
			$obj = new CartRule($id_obj);
			echo Tools::jsonEncode($obj);
			die();
		}

		if (Tools::getIsset('ajax_load_declinaisons')) {
			$id_prod = Tools::getValue('id_prod');
			$context = Context::getContext();

			$prod = new Product($id_prod);
			$declinaisons = $prod->getAttributesResume($context->language->id);

			if (empty($declinaisons))
				die();

			$result = array();
			foreach ($declinaisons as $dec)
				$result[$dec['id_product_attribute']] = $dec;

			echo Tools::jsonEncode($result);
			die();
		}

		if (Tools::getIsset('ajax_get_total_cart')) {
                        
                        $id_cart = (int) Tools::getValue('id_cart');
			$cart = OpartQuotation::createCart($id_cart);
                        
			//$cart = OpartQuotation::createCart();
			$summary = $cart->getSummaryDetails(null, true);
                        
			echo tools::jsonEncode($summary);
			die();
		}

		if (Tools::getIsset('ajax_delete_upload_file')) {
			$dossier = _PS_MODULE_DIR_ . 'opartdevis/uploadfiles/'.Tools::getValue('upload_id');
			$file = Tools::getValue('upload_name');
			unlink($dossier.'/'.$file);
			die();
		}

		if (Tools::getIsset('ajax_address_list')) {
			$id_customer = Tools::getValue('id_customer', false);
			$context = Context::getContext();

			$sql = 'SELECT  a.`alias`, a.`id_address`, a.`lastname`, a.`firstname`, a.`lastname`, a.`company`, 
			a.`address1`, a.`address2`, a.`postcode`, a.`city`,cl.`name` as `country_name`
			FROM `' . _DB_PREFIX_ . 'address` a 
			LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (a.`id_country`=cl.`id_country` AND cl.id_lang = ' . (int) $context->language->id . ')
			WHERE a.id_customer=' . (int) $id_customer;

			$result = array();
			$address_list = Db::getInstance()->executeS($sql);
			if (count($address_list) > 0)
				foreach ($address_list as $address)
					$result[$address['id_address']] = $address;
			else
				$result['erreur'] = 'no address founded';
			echo Tools::jsonEncode($result);
			die();
		}

		if (Tools::getIsset('ajax_get_reduced_price')) {
			$who_is_list = Tools::getValue('whoIs');
                        
                        $id_cart = (int) Tools::getValue('id_cart');
			$cart = OpartQuotation::createCart($id_cart);
			//$cart = OpartQuotation::createCart();
			$prod_list = $cart->getProducts(true, Tools::getValue('product_id'));
			$array = Array();
			foreach ($prod_list as $prod) {
                            $price = Product::getPriceStatic($prod['id_product'], false, $prod['id_product_attribute'], 6, null, false, true, $prod['quantity'], false, null, null, null, $specific_price_output, true, true, null, false);
                            //$array[$prod['id_product']] = $prod['price'];
                            $array[$prod['id_product']] = $price;
                        }
			$result = array();
			$i = 0;
			foreach ($who_is_list as $key => $prod_id) {
				$result[$i]['random_id'] = $key;
				$result[$i]['product_id'] = $prod_id;
				$result[$i]['real_price'] = $array[$prod_id];
				$i++;
			}
			echo tools::jsonEncode($result);
			die();
		}

		if (Tools::getIsset('transformThisCartId')) {
			$cart = new Cart(Tools::getValue('transformThisCartId'));
			$customer = new Customer($cart->id_customer);
			$new_quotation = OpartQuotation::createQuotation($cart, $customer);
			Tools::redirectAdmin(self::$currentIndex . '&id_opartdevis=' . $new_quotation->id . '&updateopartdevis&token=' . $this->token);
		}

		if (Tools::isSubmit('submitAddOpartDevis')) {
                    if (Tools::getIsset('change_carrier_cart'))
                        return false;
                    
			$id_customer = (int) Tools::getValue('opart_devis_customer_id');
			if ($id_customer == '')
				$this->errors[] = Tools::displayError($this->l('You have to choose a customer'));
			if (count($this->errors) > 0)
				return;

			//create quotation
                        $id_cart = (int) Tools::getValue('id_cart');
                        $cart = OpartQuotation::createCart($id_cart);
                        
                        //p($cart);
                        
			$customer = new Customer($id_customer);
			$id_opart_devis = Tools::getValue('id_opartdevis');

                        
			$new_quotation = OpartQuotation::createQuotation(
					$cart, $customer, $id_opart_devis, Tools::getValue('quotation_name'), Tools::getValue('message_visible'), null, false
			);
                        
			if (isset($_FILES['fileopartdevis']) && ($_FILES['fileopartdevis']['name'][0] !== ''))
			{
				$count = count($_FILES['fileopartdevis']['name']);
				$dossier = _PS_MODULE_DIR_ . 'opartdevis/uploadfiles';
				if (!is_dir($dossier))
					$dr1 = mkdir($dossier, 0777);
				if (!is_dir($dossier . '/' . $new_quotation->id))
					$dr2 = mkdir($dossier . '/'. $new_quotation->id, 0777);
				$taille_maxi = 5242880;
				$extensions = array('.png', '.gif', '.jpg', '.jpeg', '.pdf',
					'.doc', '.docx', '.txt', '.ppt', '.xls');
				$filesave = array();
				for ($i = 0; $i < $count; $i++)
				{
					$fichier = $_FILES['fileopartdevis']['name'][$i];
					$taille = filesize($_FILES['fileopartdevis']['tmp_name'][$i]);
					$extension = strrchr($_FILES['fileopartdevis']['name'][$i], '.');
					//Si l'extension n'est pas dans le tableau
					if (!in_array($extension, $extensions))
					{
						$this->bulk_actions = array(
							'extention' => array(
								'text' => $this->l('You must upload a file type image, pdf, rtf, txt or doc.'),
								'confirm' => $this->l('You must upload a file type image, pdf, rtf, txt or doc.')
							)
						);
					}
					else{
						if ($taille > $taille_maxi) {
							$this->bulk_actions = array(
								'extention' => array(
									'text' => $this->l('The file is too big...'),
									'confirm' => $this->l('The file is too big...')
								)
							);
						}
						else{
							if (!isset($erreur) && isset($_FILES['fileopartdevis']['error'][$i]))
							{
								move_uploaded_file($_FILES['fileopartdevis']['tmp_name'][$i], $dossier . '/' . $new_quotation->id . '/' . $fichier);
							}
							else
							{
								$this->bulk_actions = array(
									'extention' => array(
										'text' => $this->$erreur,
										'confirm' => $this->$erreur
									)
								);
							}
						}
					}
				}
			}
			Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
		}
		if (Tools::isSubmit('sendbymail')) {
			$id_opartdevis = Tools::getValue('id_opartdevis');
			$link = new Link;
			$redirect_link = $link->getModuleLink('opartdevis', 'showpdf', array('id_opartdevis' => $id_opartdevis, 'admin_key' => Configuration::get('PS_OPART_DEVIS_SECURE_KEY'),
				'sendMailToCustomer' => true));
			Tools::redirect($redirect_link);
		}
                
		if (Tools::isSubmit('sendbymailtoadmin')) {
			$id_opartdevis = Tools::getValue('id_opartdevis');
			$link = new Link;
			$redirect_link = $link->getModuleLink('opartdevis', 'showpdf', array('id_opartdevis' => $id_opartdevis, 'admin_key' => Configuration::get('PS_OPART_DEVIS_SECURE_KEY'),
				'sendMailToAdmin' => true));
			Tools::redirect($redirect_link);
		}

		if (Tools::isSubmit('view' . $this->table)) {
			$id_opartdevis = Tools::getValue('id_opartdevis');
			$link = new Link;
			$redirect_link = $link->getModuleLink('opartdevis', 'showpdf', array('id_opartdevis' => $id_opartdevis, 'admin_key' => Configuration::get('PS_OPART_DEVIS_SECURE_KEY')));
			Tools::redirect($redirect_link);
		}

		if (Tools::isSubmit('validate')) {
			$id_opartdevis = Tools::getValue('id_opartdevis');                        
			$quote = new OpartQuotation($id_opartdevis);
                        //p($quote);
                        $quote->validate();
			/*$link = new Link;
			$redirect_link = $link->getModuleLink('opartdevis', 'showpdf', array('id_opartdevis' => $id_opartdevis, 'admin_key' => Configuration::get('PS_OPART_DEVIS_SECURE_KEY')));
			Tools::redirect($redirect_link);*/
		}

		return parent::postProcess();
	}

	public function renderView() {
		die('render view please');
	}

}
