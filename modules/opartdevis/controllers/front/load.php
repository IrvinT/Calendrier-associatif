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

class OpartdevisloadModuleFrontController extends ModuleFrontController {

	public function initContent()
	{
		parent::initContent();
		$id_customer = $this->context->customer->id;
		$id_opartdevis = Tools::getValue('opartquotationId');
		if (is_numeric($id_opartdevis))
		{
			$id_opartdevis = (int)Tools::getValue('opartquotationId');
			$sql = 'SELECT * FROM `'._DB_PREFIX_.'opartdevis` WHERE id_customer='.(int)$id_customer.' AND id_opartdevis='.(int)$id_opartdevis;
			$result = Db::getInstance()->getRow($sql);
			if (is_array($result))
			{
				$obj = new OpartQuotation();
				if ($obj->statut == 3 || $obj->statut == 2)
					die($this->l('This quotation is no more valid'));

				$cart_obj = new Cart($result['id_cart']);
				$id_cart = $cart_obj->id;
				$this->context->cookie->__set('id_cart', $id_cart);
                                if(Tools::getValue('proceedCheckout') == true) 
                                    Tools::redirect('index.php?controller=order&step=3');
                                else
                                    Tools::redirect('index.php?controller=order');
			}
		}
		else
                    Tools::redirect('index.php?controller=my-account');
	}

	public function l($string)
	{
		return Translate::getModuleTranslation('opartdevis', $string, 'createquotation');
	}

}

?>