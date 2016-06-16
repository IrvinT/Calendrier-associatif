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

class HTMLTemplateQuotationPdf extends HTMLTemplate {

	public $cart_object;
	public $opart_quotation;

	public function __construct($opart_quotation, $smarty)
	{
		$this->opart_quotation = $opart_quotation;
		$this->cart_object = new Cart($opart_quotation->id_cart);
		$this->message_visible = $opart_quotation->message_visible;
		$this->smarty = $smarty;
		//header
		//$id_lang = Context::getContext()->language->id;
		//$this->title = $this->l('Quotation');
		//footer
		$this->shop = new Shop(Context::getContext()->shop->id);
	}

	public function getContent()
	{
		$max_prod_page = ((int)Configuration::get('OPARTDEVIS_MAXPRODPAGE') == 0) ? 13 : (int)Configuration::get('OPARTDEVIS_MAXPRODPAGE');
                $max_prod_first_page = ((int)Configuration::get('OPARTDEVIS_MAXPRODFIRSTPAGE') == 0) ? 8 : (int)Configuration::get('OPARTDEVIS_MAXPRODFIRSTPAGE');

		$this->smarty->assign(array(
			'cart_obj' => $this->cart_object,
			'message_visible' => nl2br($this->message_visible),
			'expire_time' => (int)Configuration::get('OPARTDEVIS_EXPIRETIME'),
			'priceDisplay' => Product::getTaxCalculationMethod((int)$this->cart_object->id_customer),
			'use_taxes' => (int)Configuration::get('PS_TAX'),
			'validationText' => $this->opart_quotation->getQuotationText(1, Context::getContext()->language->id),
			'freeText' => $this->opart_quotation->getQuotationText(0, Context::getContext()->language->id),
			'goodforagrementText' => $this->opart_quotation->getQuotationText(2, Context::getContext()->language->id),
			'maxProdFirstPage' => $max_prod_first_page,
			'maxProdPage' => $max_prod_page,
			'mod_tpl_dir' => _PS_MODULE_DIR_.'opartdevis/views/templates/front/pdf',
			'tax_details' => $this->opart_quotation->getDetailsTax($this->cart_object),
			'quotation_name' => $this->opart_quotation->name,
                        'quote_object' => $this->opart_quotation
		));
		return $this->smarty->fetch(_PS_MODULE_DIR_.'opartdevis/views/templates/front/pdf/quotation.tpl');
	}

	public function getHeader()
	{
		$shop_name = Configuration::get('PS_SHOP_NAME', null, null, (int)$this->cart_object->id_shop);
		$path_logo = $this->getLogo();
		$width = 0;
		$height = 0;
		if (!empty($path_logo))
			list($width, $height) = getimagesize($path_logo);

                //Limit the height of the logo for the PDF render
                $maximum_height = 100;
                if ($height > $maximum_height) {
                    $ratio = $maximum_height / $height;
                    $height *= $ratio;
                    $width *= $ratio;
                }
                
		$this->smarty->assign(array(
			'logo_path' => $path_logo,
			'img_ps_dir' => 'http://'.Tools::getMediaServer(_PS_IMG_)._PS_IMG_,
			'img_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),
			'title' => $this->l('Quotation number').':'.$this->opart_quotation->id,
			'date' => Tools::displayDate($this->cart_object->date_upd),
			'shop_name' => $shop_name,
			'width_logo' => $width,
			'height_logo' => $height
		));
		return $this->smarty->fetch($this->getTemplate('header'));
	}

	protected static function l($string)
	{
		return Translate::getModuleTranslation('opartdevis', $string, 'htmltemplatequotationpdf');
	}

	public function getFooter()
	{
		$shop_address = $this->getShopAddress();
		$this->smarty->assign(array(
			'available_in_your_account' => $this->available_in_your_account,
			'shop_address' => $shop_address,
			'shop_fax' => Configuration::get('PS_SHOP_FAX', null, null, (int)$this->cart_object->id_shop),
			'shop_phone' => Configuration::get('PS_SHOP_PHONE', null, null, (int)$this->cart_object->id_shop),
			'shop_details' => Configuration::get('PS_SHOP_DETAILS', null, null, (int)$this->cart_object->id_shop),
			'free_text' => Configuration::get('PS_INVOICE_FREE_TEXT', (int)Context::getContext()->language->id, null, (int)$this->cart_object->id_shop)
		));
		return $this->smarty->fetch(_PS_MODULE_DIR_.'opartdevis/views/templates/front/pdf/footer.tpl');
	}

	/*
	 * Returns the template filename
	 * @return string filename
	 */

	public function getFilename()
	{
		return $this->l('Quotation').'_'.$this->opart_quotation->id.'.pdf';
	}

	/*
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */

	public function getBulkFilename()
	{
		return $this->l('quotation').'.pdf';
	}

	protected function getLogo()
	{
		$logo = '';
		if (Configuration::get('PS_LOGO_INVOICE', null, null, (int)$this->cart_object->id_shop) != false &&
			file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, (int)$this->cart_object->id_shop)))
			$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, (int)$this->cart_object->id_shop);
		elseif (Configuration::get('PS_LOGO', null, null, (int)$this->cart_object->id_shop) != false &&
			file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, (int)$this->cart_object->id_shop)))
			$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, (int)$this->cart_object->id_shop);
		return $logo;
	}
        
    /** since 1.6.1.5 **/
    public function getPagination()    {
        return false;
        //return $this->smarty->fetch($this->getTemplate('pagination'));
    }

}
