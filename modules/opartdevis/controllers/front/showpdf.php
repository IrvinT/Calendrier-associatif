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

class OpartDevisShowPdfModuleFrontController extends ModuleFrontController {

	public function init()
	{
		$this->display_column_left = false;
		parent::init();
	}

	public function initContent()
	{
		if (Tools::getValue('sendMailToCustomer') && Tools::getValue('sendMailToCustomer') == true)
		{
			$new_quotation = new OpartQuotation(Tools::getValue('id_opartdevis'));
			if ($new_quotation->sendMailToCustommer($this->context) == true)
				$text = '<span style="color:green">Mail to customer has been sent successfully.</a>';
			else
				$text = '<span style="color:red">An error occurred during sending mail.</a>';

			$text .= '<br /><a href="#" onClick="history.back(-1); return false;">back to quotation list</a>';
			echo $text;
			die();
		}

		if (Tools::getValue('sendMailToAdmin') && Tools::getValue('sendMailToAdmin') == true)
		{
			$new_quotation = new OpartQuotation(Tools::getValue('id_opartdevis'));
                        
			if ($new_quotation->sendMailToAdmin($this->context) === true)
                            $text = '<span style="color:green">Mail to admin has been sent successfully.</a>';
			else if('noadmincontact')
                            $text = '<span style="color:red">You have to configure your module before use this feature.</a>';
                        else 
                            $text = '<span style="color:red">An error occurred during sending mail.</a>';
                        
			$text .= '<br /><a href="#" onClick="history.back(-1); return false;">back to quotation list</a>';
			echo $text;
			die();
		}

		if (Tools::getValue('idCart'))
			$opart_quotation = OpartQuotation::getByIdCart((int)Tools::getValue('idCart'));

		if (Tools::getValue('id_opartdevis'))
			$opart_quotation = new OpartQuotation((int)Tools::getValue('id_opartdevis'));

		if ($opart_quotation == false)
			die('no quotation found');
		$opart_quotation->renderPdf(Context::getContext()->smarty, true, Context::getContext());
	}

}
