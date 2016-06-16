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

class OpartdevislistModuleFrontController extends ModuleFrontController {

    public function init() {
        $this->display_column_left = false;
        parent::init();
    }

    public function initContent() {
        parent::initContent();
        
        if(Tools::getIsset('newcart') && Tools::getValue('newcart')==true) {
            //reset current panier customer
            $this->context->cookie->__set('id_cart', $id_cart);
            Tools::redirect('index.php?controller=order');
        }
            
        $id_customer = $this->context->customer->id;

        if (Tools::getValue('action') == 'delete') {
            $id_opartdevis = (int) Tools::getValue('opartquotationId');
            if (Db::getInstance()->delete('opartdevis', 'id_customer =' . (int) $id_customer . ' AND id_opartdevis=' . (int) $id_opartdevis))
                $this->context->smarty->assign('deleted', 'success');
        }

        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'opartdevis` WHERE id_customer=' . (int) $id_customer;
        $quotations = Db::getInstance()->executeS($sql);
        
        $expiretime = (int) Configuration::get('OPARTDEVIS_EXPIRETIME', 0);

        foreach ($quotations as &$quotation) {
            //$quotation['is_valid'] = $obj->isValid($quotation['date_add']);
            //update statut for quote nore more valid
            $obj = new OpartQuotation($quotation['id_opartdevis']);
            $quotation['statut'] = $obj->checkValidity($quotation['date_add']);
            $quotation['expire_date'] = OpartQuotation::calc_expire_date($quotation['date_add']);
        }

        $this->context->smarty->assign('quotations', $quotations);
        $this->context->smarty->assign('expiretime', $expiretime);
        $this->setTemplate('list.tpl');
    }

}
?>