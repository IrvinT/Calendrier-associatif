<?php
/**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code
*
*  @author    Geodis
*  @copyright 2010-2015 Geodis
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

ob_clean();
if (!defined('_PS_ADMIN_DIR_')) {
    require_once(realpath(dirname(__FILE__) . '/../../config/config.inc.php'));
    require_once(realpath(dirname(__FILE__) . '/../../init.php'));
    //utilisé avec Modman
    //require_once('/var/www/html/prestashop16/config/config.inc.php');
    //require_once('/var/www/html/prestashop16/init.php');
}

switch (Tools::getValue('method')) {
    case 'SetShippingGeodisFranceExpressInformation':
        SetShippingGeodisFranceExpressInformation(Tools::getValue('params'));
        break;
    default:
        exit;
}

//save shipping information 
function SetShippingGeodisFranceExpressInformation($params)
{
    $email = Tools::substr((isset($params['email']) ? Tools::jsonEncode($params['email']) : ''), 1, -1);
    $fixe = Tools::substr((isset($params['fixe']) ? Tools::jsonEncode($params['fixe']) : ''), 1, -1);
    $mobile = Tools::substr((isset($params['mobile']) ? Tools::jsonEncode($params['mobile']) : ''), 1, -1);
    $id_shop = (int) Context::getContext()->shop->id;
    $context = Context::getContext();
    $id_cart = $context->cart->id;
    $data = array(
        'cart_id' => (int) $id_cart,
        'store' => (int) $id_shop,
        'email' => $email,
        'phone' => $fixe,
        'mobile' => $mobile,
    );

    $table_exist = 'SELECT count(*) FROM '._DB_PREFIX_.'geodis_shipping';
    // @todo: remove line if ok
    // $row_returned = Db::getInstance()->getRow($table_exist, $use_cache = 1);
    $row_returned = Db::getInstance()->getRow($table_exist);

    if ($row_returned) {
        Db::getInstance()->delete('ps_geodis_shipping', 'cart_id = ' . (int) $id_cart);
    }


    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'geodis_france_express WHERE cart_id = ' . (int) $id_cart;
    $row = Db::getInstance()->getRow($sql, $use_cache = 1);
    if (!$row) {
        if (Db::getInstance()->insert('geodis_france_express', $data)) {
            $result = 1;
        } else {
            $result = 0;
        }
    } else {
        $updated = Db::getInstance()->Execute(
            'UPDATE `' . _DB_PREFIX_ . 'geodis_france_express` SET `email` = "' . pSQL($email) . '", `phone` = "' . pSQL($fixe) . '", `mobile` = "' . pSQL($mobile) . '"' .
            'WHERE `cart_id` ="' . (int) $id_cart . '"'
        );
        if ($updated) {
            $result = 1;
        } else {
            $result = 0;
        }
    }
    echo $result;
}
exit;
