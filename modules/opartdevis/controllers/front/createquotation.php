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

require_once _PS_MODULE_DIR_.'opartdevis/models/OpartQuotation.php';

class OpartDevisCreateQuotationModuleFrontController extends ModuleFrontController {

	public function init()
	{
		$this->display_column_left = false;
		parent::init();
	}

	public function l($string)
	{
		return Translate::getModuleTranslation('opartdevis', $string, 'createquotation');
	}
/*
	public function getCarriersList()
	{
		if (!Tools::getValue('idCart'))
		{
			$cart = new Cart();
			$id_customer = Tools::getValue('opart_devis_customer_id');
			$context = Context::getContext();
			if ($id_customer == '')
				return array();

			$cart->id_customer = $id_customer;
			$customer_obj = new Customer($id_customer);
			$context->customer = $customer_obj;

			$cart->id_address_delivery = Tools::getValue('invoice_address');
			$cart->id_address_invoice = Tools::getValue('delivery_address');
			$cart->id_currency = $context->currency->id;
			$cart->id_lang = $context->language->id;
			$cart->add();

			$add_prod_list = Tools::getValue('add_prod');
			$add_attribute_list = Tools::getValue('add_attribute');
			$who_is_list = Tools::getValue('whoIs');

			if (empty($who_is_list))
				die();
			$list_prod = array();
			foreach ($who_is_list as $random_id => $prod_id)
			{
				$list_prod[$random_id]['id'] = $prod_id;
				$list_prod[$random_id]['qty'] = $add_prod_list[$random_id];
				if (isset($add_attribute_list[$random_id]))
					$list_prod[$random_id]['id_attribute'] = $add_attribute_list[$random_id];
			}
			if (!empty($list_prod))
			{
				foreach ($list_prod as $prod)
				{
					if (isset($list_prod[$random_id]['id_attribute']))
						$cart->updateQty($prod['qty'], $prod['id'], $prod['id_attribute']);
					else
						$cart->updateQty($prod['qty'], $prod['id']);
				}
			}
		}
		else
		{
			$cart = new Cart((int)Tools::getValue('idCart'));
			$cart->updateAddressId($cart->id_address_invoice, (int)Tools::getValue('invoice_address'));
			$cart->updateAddressId($cart->id_address_delivery, (int)Tools::getValue('delivery_address'));
		}
		$option_list = $cart->getDeliveryOptionList();
		if (!count($option_list) > 0)
			return array();
		
		$price_display = Group::getPriceDisplayMethod(Group::getCurrent()->id);
		$with_tax = ($price_display == 0) ? true : false;

		$result = array();
		foreach ($option_list as $options)
		{
			foreach ($options as $option)
			{				
				if ($option['unique_carrier'] == 1)
				{
					foreach ($option['carrier_list'] as $key => $carrier_list)
					{
						
						$result[$key]['price'] = $cart->getPackageShippingCost($key, $with_tax);
						$result[$key]['name'] = $carrier_list['instance']->name;
						$result[$key]['taxOrnot'] = ($with_tax == true) ? $this->l('tax incl.') : $this->l('tax excl.');
					}
				}
			}
		}

		if (!Tools::getValue('idCart'))
			$cart->delete();

		echo Tools::jsonEncode($result);
		die();
	}
*/
	public function initContent()
	{
		if (Tools::getIsset('ajax_carrier_list')) {
                    $quoteObj = new OpartQuotation();
                    $json = $quoteObj->getCarriersList();
                    echo $json;
                    die();
                }
                if (Tools::getIsset('change_carrier_cart')) {
                    $cart = OpartQuotation::changeCarrierCart();
                    $summary = $cart->getSummaryDetails(null, true);
                    echo tools::jsonEncode($summary);
                    die();
		}
		parent::initContent();
		$show_form = true;
		$cart = $this->context->cart;
		$customer = $this->context->customer;

		if (!Validate::isLoadedObject($customer))
		{
			/*$back_url = $this->context->link->getModuleLink('opartdevis', 'createquotation', array('create' => true));*/
			$this->context->smarty->assign(array(
				'OPARTDEVIS_SHOWFREEFORM' => Configuration::get('OPARTDEVIS_SHOWFREEFORM'),
				'back' => ''
			));
			$this->setTemplate('pleaselog.tpl');
			return false;
		}
		if (Tools::getValue('create'))
		{
			//get customers addresses
			if (!Validate::isLoadedObject($customer))
				$addresses = array();
			else
				$addresses = $customer->getAddresses($this->context->language->id);

			if (count($addresses) == 0)
				$this->errors[] = Tools::displayError($this->l('You have to save at least one address, before creating your quotation'));

			if ($cart->nbProducts() == 0)
			{
				$show_form = false;
				$this->context->smarty->assign('cartEmpty', true);
			}

			$from = (Tools::getIsset('from')) ? Tools::getValue('from') : '';

                        //if(isset($useSSL) AND $useSSL AND Configuration::get('PS_SSL_ENABLED'))
                        /*
                        if(Configuration::get('PS_SSL_ENABLED'))
                            $ps_base_url = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
			else
                            $ps_base_url = _PS_BASE_URL_.__PS_BASE_URI__;
                        */
			if ($this->errors)
				$show_form = false;
                        
                        //search id by cart
                        $quotationObj = OpartQuotation::getQuotationByCartId($cart->id);
                        if(is_object($quotationObj)) {
                            $quotationId = $quotationObj->id_opartdevis;
                            $quotationName = $quotationObj->name;
                        }
                        else {
                            $quotationId = null;
                            $quotationName = '';
                        }
                        
			$this->context->smarty->assign(array(
				'addresses' => $addresses,
				'opart_module_dir' => _MODULE_DIR_.'opartdevis',
				//'ps_base_url' => $ps_base_url,
				'customerId' => $customer->id,
				'cart' => $cart,
				'summary' => $cart->getSummaryDetails(),
				'id_cart' => $cart->id,
				'showForm' => $show_form,
				'from' => $from,
                                'quotationId' => $quotationId,
                                'quotationName' => $quotationName,
			));                        
                        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/front.js');
			$this->setTemplate('create.tpl');
		}

		if (Tools::isSubmit('submitQuotation'))
		{
                    if (Tools::getIsset('change_carrier_cart'))
                        return false;
                    
			$cart->id_address_delivery = (int)Tools::getValue('delivery_address');
			$cart->id_address_invoice = (int)Tools::getValue('invoice_address');
			$cart->id_carrier = Tools::getValue('opart_devis_carrier_input');                   
                        $cart->update();
                        
                        //create specific price
                        $listProd=$cart->getProducts();
                        foreach($listProd as &$prod) {
                            $prod['specific_price'] = $prod['price'];
                            $prod['specific_qty'] = $prod['cart_quantity'];
                            $prod['id'] = $prod['id_product'];
                            $prod['id_attribute'] = $prod['id_product_attribute'];
                        }                        
                        OpartQuotation::addSpecificPrice($listProd,$cart,$customer->id);
                        
                        
                        $quotationId = Tools::getValue('quotationId');
                        
			$new_quotation = OpartQuotation::createQuotation($cart, $customer, $quotationId, Tools::getValue('quotation_name'), Tools::getValue('message_visible'),
					Tools::getValue('message_not_visible'), false);

                        $link = new Link;
                        $redirect_link = $link->getModuleLink('opartdevis','createquotation',array('confirm' => $new_quotation->id));
                        
                        //reset current panier customer
                        $this->context->cookie->__set('id_cart', $id_cart);
                        
                        Tools::redirect($redirect_link);                         
		}
                
                if(Tools::getValue('confirm')) {
                    $new_quotation = new OpartQuotation(Tools::getValue('confirm'));
                    $this->context->smarty->assign('id_cart', $new_quotation->id_cart);
                    if (version_compare(_PS_VERSION_, '1.6.0', '<'))
				$this->setTemplate('confirm_15.tpl');
			else
				$this->setTemplate('confirm.tpl');    
                              
                        if (Configuration::get('OPARTDEVIS_SENDMAILTOCUSTOMER') == 1) 
				$new_quotation->sendMailToCustommer($this->context);
                        
			if (Configuration::get('OPARTDEVIS_SENDMAILTOADMIN') == 1)
				$new_quotation->sendMailToAdmin($this->context);
                }
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/opartdevis.css');
		if (version_compare(_PS_VERSION_, '1.6.0', '<'))
			$this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/opartdevis_15.css');
	}

}
