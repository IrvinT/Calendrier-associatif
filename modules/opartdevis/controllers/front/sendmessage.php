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

class OpartDevisSendmessageModuleFrontController extends ModuleFrontController {

	public function init()
	{
		$this->display_column_left = false;
		parent::init();
	}

	public function l($string)
	{
		return Translate::getModuleTranslation('opartdevis', $string, 'sendmessage');
	}

	public function initContent()
	{
		parent::initContent();

		$customer = $this->context->customer;

		//Tools::redirect('index.php?controller=order&step=1');
		if (Validate::isLoadedObject($customer))
		{
			$customer_id = $customer->id;
			$addresses = $customer->getAddresses($this->context->language->id);
		}
		else
		{
			$customer_id = 0;
			$addresses = array();
		}
		$this->context->smarty->assign(array(
			'customer_id' => $customer_id,
			'addresses' => $addresses,
		));

		if (Tools::isSubmit('submitMessage'))
		{
			$opart_quotation = new OpartQuotation();

			$customer = $this->context->customer;

			//Tools::redirect('index.php?controller=order&step=1');
			if (!Validate::isLoadedObject($customer))
			{
				if (!Tools::getValue('customer_firstname'))
					$this->errors[] = Tools::displayError($this->l('You have to specify your firstname'));

				if (!Tools::getValue('customer_lastname'))
					$this->errors[] = Tools::displayError($this->l('You have to specify your lastname'));

				if (!Tools::getValue('customer_email'))
					$this->errors[] = Tools::displayError($this->l('You have to specify your email'));

				else if (!Validate::isEmail(Tools::getValue('customer_email')))
					$this->errors[] = Tools::displayError($this->l($this->l('Please specify a valid email')));

				if (!$this->errors)
				{
					$customer = array();
					$customer['firstname'] = Tools::getValue('customer_firstname');
					$customer['lastname'] = Tools::getValue('customer_lastname');
					$customer['email'] = Tools::getValue('customer_email');
				}
			}
			$invoice_address = (!Tools::getValue('invoice_address')) ? Tools::getValue('invoice_address_text') : Tools::getValue('invoice_address');
			$delivery_address = (!Tools::getValue('delivery_address')) ? Tools::getValue('delivery_address_text') : Tools::getValue('delivery_address');

			$phone = Tools::getValue('customer_phone');
			$message = Tools::getValue('quotation_message');

			if (empty($message))
				$this->errors[] = Tools::displayError($this->l('Please explain us your request'));

			if (!$this->errors)
			{
				if ($opart_quotation->sendQuotationRequest($customer, $invoice_address, $delivery_address, $message, $phone, $this->context))
					$this->context->smarty->assign('confirmation', 1);
				else
					$this->errors[] = Tools::displayError($this->l('An error occured during the send of your request'));
			}
		}

		$this->setTemplate('sendmessage.tpl');
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/opartdevis.css');
		if (version_compare(_PS_VERSION_, '1.6.0', '<'))
			$this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/opartdevis_15.css');
	}

}
