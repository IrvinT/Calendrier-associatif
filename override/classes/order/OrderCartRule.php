<?php
/**
 *  Order Fees
 *
 *  @author    motionSeed <ecommerce@motionseed.com>
 *  @copyright 2016 motionSeed. All rights reserved.
 *  @license   https://www.motionseed.com/license
 */
class OrderCartRule extends OrderCartRuleCore
{
    /*
    * module: orderfees
    * date: 2016-06-14 11:46:28
    * version: 1.7.1
    */
    public function __construct($id = null)
    {
        parent::__construct($id);
        if ($this->isFee()) {
            $this->value *= -1;
            $this->value_tax_excl *= -1;
        }
    }
    /*
    * module: orderfees
    * date: 2016-06-14 11:46:28
    * version: 1.7.1
    */
    public function isFee()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT cr.is_fee
		FROM `' . _DB_PREFIX_ . 'order_cart_rule` ocr
		LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule` cr ON cr.`id_cart_rule` = ocr.`id_cart_rule`
		WHERE ocr.`id_order_cart_rule` = ' . (int) $this->id_order_cart_rule);
    }
}
