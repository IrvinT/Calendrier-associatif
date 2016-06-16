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

if (!defined('_PS_VERSION_')) {
    exit;
}

class Geodisfranceexpress extends CarrierModule
{
    public $id_carrier;
    private $_html = '';
    private $_postErrors = array();
    private $_moduleName = 'geodisfranceexpress';

    const GEODIS_METHOD_1_NAME = 'FRANCE EXPRESS ON DEMAND premium';
    const GEODIS_METHOD_2_NAME = 'FRANCE EXPRESS ON DEMAND live';
    const GEODIS_METHOD_1_LOGO = 'FE_OD_premium.png';
    const GEODIS_METHOD_2_LOGO = 'FE_OD_live.png';
    const GEODIS_LOGO_1_NAME = 'logo1.jpg';
    const GEODIS_LOGO_2_NAME = 'logo1.jpg';
    //weight max = 1000 kg
    const GEODIS_POIDS_MAX = 1000;
    //height max = 2 m
    const GEODIS_HAUTEUR_MAX = 200;
    //lenght max = 3m
    const GEODIS_LONGUEUR_MAX = 300;
    const GEODIS_METHOD_1 = 'geodis_od_express';
    const GEODIS_METHOD_2 = 'geodis_rdv_tel_messagerie';

    //list of origin country accepted by geodis
    protected $geodisOriginCountryMethod1 = array(1 => 'France', 2 => 'Monaco');
    protected $geodisOriginCountryMethod2 = array(1 => 'France', 2 => 'Monaco', 3 => 'Belgique');
    //list of destination country accepted by geodis
    protected $geodisDestinationCountryMethod1 = array(1 => 'France', 2 => 'Monaco');
    protected $geodisDestinationCountryMethod2 = array(1 => 'France', 2 => 'Monaco', 3 => 'Belgique', 4 => 'Luxembourg');
    //list of destination country with iso code
    protected $geodisDestinationCountryMethod1IsoCode = array('FR' => 'France', 'MC' => 'Monaco');
    protected $geodisDestinationCountryMethod2IsoCode = array('FR' => 'France', 'MC' => 'Monaco', 'BE' => 'Belgique', 'LU' => 'Luxembourg');

    /*
     * * Construct Method
     * *
     */
    public function __construct()
    {
        $this->name = 'geodisfranceexpress';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'GEODIS';
        $this->module_key = 'afb255ea644dbddc2f8af9ecb5c0d038';

        $this->limited_countries = array('fr', 'us');

        parent::__construct();

        $this->context = Context::getContext();
        $datas = array(
            'display_header' => true,
            'display_header_javascript' => true,
            'display_footer' => true,
        );
        
        $this->context->smarty->assign($datas);

        $this->displayName = $this->l('FRANCE EXPRESS ON DEMAND - Logos ');
        $this->description = $this->l('Allow your customers to choose their delivery date');

        if (self::isInstalled($this->name)) {
            
            // Getting carrier list
            $carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);

            // Saving id carrier list
            $id_carrier_list = array();
            foreach ($carriers as $carrier) {
                $id_carrier_list[] .= $carrier['id_carrier'];
            }

            // Testing if Carrier Id exists
            $warning = array();
            if (!in_array((int) (Configuration::get('FE_CARRIER1_CARRIER_ID')), $id_carrier_list)) {
                $warning[] .= $this->l('"Carrier 1"') . ' ';
            }

            if (!in_array((int) (Configuration::get('FE_CARRIER2_CARRIER_ID')), $id_carrier_list)) {
                $warning[] .= $this->l('"Carrier 2"') . ' ';
            }

            if (!Configuration::get('FE_CARRIER1_OVERCOST')) {
                $warning[] .= $this->l('"Carrier 1 Overcost"') . ' ';
            }

            if (!Configuration::get('FE_CARRIER2_OVERCOST')) {
                $warning[] .= $this->l('"Carrier 2 Overcost"') . ' ';
            }

            if (count($warning)) {
                $this->warning .= implode(' , ', $warning) . $this->l('must be configured to use this module correctly') . ' ';
            }
        }
    }

    /*
     * * Install / Uninstall Methods
     * *
     */

    public function install()
    {
        $this->createDB();

        $config_geodis_france_express = new Tab();
        $config_geodis_france_express->name[$this->context->language->id] = $this->l('Ordres France Express');
        $config_geodis_france_express->class_name = 'AdminOrderFranceExpress';
        $config_geodis_france_express->id_parent = Tab::getIdFromClassName('AdminParentOrders');
        $config_geodis_france_express->module = $this->name;
        $config_geodis_france_express->token = Tools::getValue('token');
        $config_geodis_france_express->add();

        $this->createGeodisZoneCountry("Europe");
        $zones = Db::getInstance()->executeS('SELECT `id_zone` FROM `' . _DB_PREFIX_ . 'zone` WHERE  `name` LIKE "Europe%"');
        $zones_id = array();
        foreach ($zones as $zone) {
            $zones_id[]= $zone['id_zone'];
        }
        $carrierConfig = array(
            0 => array('name' => self::GEODIS_METHOD_1_NAME,
                'id_tax_rules_group' => 0,
                'active' => true,
                'deleted' => 0,
                'shipping_handling' => false,
                'range_behavior' => 0,
                'delay' => array('fr' => 'Dès ma commande expédiée, je choisis sur internet ma demi-journée de livraison en express, du lundi au samedi matin.', 'en' => 'I will schedule the delivery date and time online once my order has been shipped. Deliveries Monday to Saturday morning.', 2 => 'I will schedule the delivery date and time online once my order has been shipped. Deliveries Monday to Saturday morning.'),
                'id_zone' => $zones_id,
                'is_module' => true,
                'shipping_external' => true,
                'external_module_name' => 'geodisfranceexpress',
                'need_range' => true,
                'max_width' => 0,
                'max_height' => 0,
                'max_weight' => 0
            ),
            1 => array('name' => self::GEODIS_METHOD_2_NAME,
                'id_tax_rules_group' => 0,
                'active' => true,
                'deleted' => 0,
                'shipping_handling' => false,
                'range_behavior' => 0,
                'delay' => array('fr' => 'FRANCE EXPRESS me contactera par téléphone pour convenir d\'un créneau horaire de livraison sur-mesure.', 'en' => 'FRANCE EXPRESS will call me to schedule a bespoke delivery appointment.', 2 => 'FRANCE EXPRESS will call me to schedule a bespoke delivery appointment.'),
                'id_zone' => $zones_id,
                'is_module' => true,
                'shipping_external' => true,
                'external_module_name' => 'geodisfranceexpress',
                'need_range' => true,
                'max_width' => 0,
                'max_height' => 0,
                'max_weight' => 0
            ),
        );

        $id_carrier1 = $this->installExternalCarrier($carrierConfig[0], self::GEODIS_LOGO_1_NAME);
        $france_express_id_carrier2 = $this->installExternalCarrier($carrierConfig[1], self::GEODIS_LOGO_2_NAME);


        Configuration::updateValue('FE_CARRIER1_CARRIER_ID', (int) $id_carrier1);
        Configuration::updateValue('FE_CARRIER2_CARRIER_ID', (int) $france_express_id_carrier2);


        Configuration::updateValue('FE_CARRIER1_ACTIVATED', (int) 1);
        Configuration::updateValue('FE_CARRIER1_DESCRIPTION', 'Dès ma commande expédiée, je choisis sur internet ma demi-journée de livraison en express, du lundi au samedi matin.');
        Configuration::updateValue('FE_CARRIER1_LONG_DESCRIPTION', 'Dès ma commande expédiée, je choisis sur internet ma demi-journée de livraison en express, du lundi au samedi matin.');
        
        $languages = Language::getLanguages(false);

        $carrier_description = array();
        foreach ($languages as $lang) {
            
            if ($lang['iso_code'] == 'en') {
                $carrier_description[$lang['id_lang']] = 'I will schedule the delivery date and time online once my order has been shipped. Deliveries Monday to Saturday morning.';
            }

            if ($lang['iso_code'] == 'fr') {
                $carrier_description[$lang['id_lang']] = 'Dès ma commande expédiée, je choisis sur internet ma demi-journée de livraison en express, du lundi au samedi matin.';
            }
        }

        Configuration::updateValue('FE_CARRIER1_LONG_DESCRIPTION', $carrier_description);

        Db::getInstance()->Execute(
            'UPDATE `' . _DB_PREFIX_ . 'delivery` SET `price` = "0"'
            . 'WHERE `id_carrier` ="' . $id_carrier1 . '"'
        );

        Configuration::updateValue('FE_CARRIER2_ACTIVATED', (int) 1);
        Configuration::updateValue('FE_CARRIER2_DESCRIPTION', 'FRANCE EXPRESS me contactera par téléphone pour convenir d\'un créneau horaire de livraison sur-mesure.');
        Configuration::updateValue('FE_CARRIER2_LONG_DESCRIPTION', 'FRANCE EXPRESS me contactera par téléphone pour convenir d\'un créneau horaire de livraison sur-mesure.');

        $carrier_description = array();
        foreach ($languages as $lang) {
            
            if ($lang['iso_code'] == 'en') {
                $carrier_description[$lang['id_lang']] = 'FRANCE EXPRESS will call me to schedule a bespoke delivery appointment.';
            }

            if ($lang['iso_code'] == 'fr') {
                $carrier_description[$lang['id_lang']] = 'FRANCE EXPRESS me contactera par téléphone pour convenir d\'un créneau horaire de livraison sur-mesure.';
            }
        }

        Configuration::updateValue('FE_CARRIER2_LONG_DESCRIPTION', $carrier_description);


        Db::getInstance()->Execute(
            'UPDATE `' . _DB_PREFIX_ . 'delivery` SET `price` = "0"'
            . 'WHERE `id_carrier` ="' . $france_express_id_carrier2 . '"'
        );

        if (!parent::install() ||
            !Configuration::updateValue('FE_CARRIER1_OVERCOST', 0) ||
            !Configuration::updateValue('FE_CARRIER2_OVERCOST', 0) ||
            !$this->registerHook('updateCarrier') ||
            !$this->registerHook('OrderDetailDisplayed') ||
            !$this->registerHook('displayCarrierList') ||
            !$this->registerHook('displayAdminOrder')) {
            return false;
        }
            
        return true;
    }

    public function uninstall()
    {
        // Uninstall
        // Delete External Carrier
        $id_tab_order_geodis_france_express = Tab::getIdFromClassName('AdminOrderFranceExpress');
        $tab = new Tab($id_tab_order_geodis_france_express);
        $tab->delete();

        Db::getInstance()->Execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'geodis_france_express`');

        // Delete External Carrier
        $Carrier1 = new Carrier((int) (Configuration::get('FE_CARRIER1_CARRIER_ID')));
        $Carrier2 = new Carrier((int) (Configuration::get('FE_CARRIER2_CARRIER_ID')));


        // If external carrier is default set other one as default
        if (Configuration::get('PS_CARRIER_DEFAULT') == (int) ($Carrier1->id) || Configuration::get('PS_CARRIER_DEFAULT') == (int) ($Carrier2->id)) {
            
            $carriersD = Carrier::getCarriers($this->context->language->id, true, false, false, null, PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
            foreach ($carriersD as $carrierD) {
                if ($carrierD['active'] && ! $carrierD['deleted'] && ( $carrierD['name'] != $this->_config['name'])) {
                    Configuration::updateValue('PS_CARRIER_DEFAULT', $carrierD['id_carrier']);
                }
            }
        }

        // Then delete Carrier
        $Carrier1->deleted = 1;
        $Carrier2->deleted = 1;

        if (!$Carrier1->update() || !$Carrier2->update()) {
            return false;
        }

        if (!parent::uninstall() ||
            !Configuration::deleteByName('FE_CARRIER1_OVERCOST') ||
            !Configuration::deleteByName('FE_CARRIER2_OVERCOST') ||
            !Configuration::deleteByName('FE_CARRIER1_CARRIER_ID') ||
            !Configuration::deleteByName('FE_CARRIER1_ACTIVATED') ||
            !Configuration::deleteByName('FE_CARRIER1_DESCRIPTION') ||
            !Configuration::deleteByName('FE_CARRIER1_LONG_DESCRIPTION') ||
            !Configuration::deleteByName('FE_CARRIER2_CARRIER_ID') ||
            !Configuration::deleteByName('FE_CARRIER2_ACTIVATED') ||
            !Configuration::deleteByName('FE_CARRIER2_DESCRIPTION') ||
            !Configuration::deleteByName('FE_CARRIER2_LONG_DESCRIPTION') ||
            !$this->unregisterHook('updateCarrier') ||
            !$this->unregisterHook('OrderDetailDisplayed') ||
            !$this->unregisterHook('displayCarrierList') ||
            !$this->unregisterHook('displayAdminOrder')) {
            return false;
        }

        return true;
    }

    public static function installExternalCarrier($config, $logo)
    {
        $carrier = new Carrier();
        $carrier->name = $config['name'];
        $carrier->id_tax_rules_group = $config['id_tax_rules_group'];
        $carrier->id_zone = $config['id_zone'];
        $carrier->active = $config['active'];
        $carrier->deleted = $config['deleted'];
        $carrier->delay = $config['delay'];
        $carrier->shipping_handling = $config['shipping_handling'];
        $carrier->range_behavior = $config['range_behavior'];
        $carrier->is_module = $config['is_module'];
        $carrier->shipping_external = $config['shipping_external'];
        $carrier->external_module_name = $config['external_module_name'];
        $carrier->need_range = $config['need_range'];

        $languages = Language::getLanguages(true);
        foreach ($languages as $language) {
            if ($language['iso_code'] == 'fr') {
                $carrier->delay[(int) $language['id_lang']] = $config['delay'][$language['iso_code']];
            }
            if ($language['iso_code'] == 'en') {
                $carrier->delay[(int) $language['id_lang']] = $config['delay'][$language['iso_code']];
            }
            if ($language['iso_code'] == Language::getIsoById(Configuration::get('PS_LANG_DEFAULT'))) {
                $carrier->delay[(int) $language['id_lang']] = $config['delay'][$language['iso_code']];
            }
        }

        if ($carrier->add()) {
            $groups = Group::getGroups(true);
            foreach ($groups as $group) {
                Db::getInstance()->autoExecute(_DB_PREFIX_ . 'carrier_group', array('id_carrier' => (int) ($carrier->id), 'id_group' => (int) ($group['id_group'])), 'INSERT');
            }

            $rangePrice = new RangePrice();
            $rangePrice->id_carrier = $carrier->id;
            $rangePrice->delimiter1 = '0';
            $rangePrice->delimiter2 = '1000';
            $rangePrice->add();

            $rangeWeight = new RangeWeight();
            $rangeWeight->id_carrier = $carrier->id;
            $rangeWeight->delimiter1 = '0';
            $rangeWeight->delimiter2 = '1000';
            $rangeWeight->add();

            foreach ($carrier->id_zone as $zone) {
                Db::getInstance()->autoExecute(
                    _DB_PREFIX_ . 'carrier_zone',
                    array(
                        'id_carrier' => (int) ($carrier->id),
                        'id_zone' => (int)$zone
                    ),
                    'INSERT'
                );
                Db::getInstance()->autoExecuteWithNullValues(
                    _DB_PREFIX_ . 'delivery',
                    array(
                        'id_carrier' => (int) ($carrier->id),
                        'id_range_price' => (int) ($rangePrice->id),
                        'id_range_weight' => null,
                        'id_zone' => (int)$zone,
                        'price' => '0'
                    ),
                    'INSERT'
                );
                Db::getInstance()->autoExecuteWithNullValues(
                    _DB_PREFIX_ . 'delivery',
                    array(
                        'id_carrier' => (int) ($carrier->id),
                        'id_range_price' => null,
                        'id_range_weight' => (int) ($rangeWeight->id),
                        'id_zone' => (int)$zone,
                        'price' => '0'
                    ),
                    'INSERT'
                );
            }


            // Copy Logo
            if (!copy(dirname(__FILE__) . '/views/img/' . $logo, _PS_SHIP_IMG_DIR_ . '/' . (int) $carrier->id . '.jpg')) {
                return false;
            }

            // Return ID Carrier
            return (int) ($carrier->id);
        }

        return false;
    }

    //creation de la table ps_geodis_france_express
    public static function createDB()
    {
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'geodis_france_express`');
        Db::getInstance()->Execute(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'geodis_france_express` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `cart_id` int(11) NOT NULL,
                `store` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `phone` varchar(255) NOT NULL,
                `mobile` varchar(255) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
        );
    }

    /*
     * * Form Config Methods
     * *
     */

    public function getContent()
    {
        $this->_html .= '<h2>' . $this->l('FRANCE EXPRESS ON DEMAND LOGO') . '</h2>';
        
        if (!empty($_POST) && Tools::isSubmit('submitSave')) {
            $this->_postValidation();
            if (!sizeof($this->_postErrors)) {
                $this->_postProcess();
            } else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        }
        $this->_html .= $this->_displayForm();
        return $this->_html;
    }

    private function _displayForm()
    {
        $result_method1 = Db::getInstance()->getRow('SELECT `price` FROM `' . _DB_PREFIX_ . 'delivery` WHERE `id_carrier` = ' . (int) (Configuration::get('FE_CARRIER1_CARRIER_ID')));
        if ($result_method1["price"]) {
            $priceMethod1 = $result_method1["price"];
        }

        $result_method2 = Db::getInstance()->getRow('SELECT `price` FROM `' . _DB_PREFIX_ . 'delivery` WHERE `id_carrier` = ' . (int) (Configuration::get('FE_CARRIER2_CARRIER_ID')));
        if ($result_method2["price"]) {
            $priceMethod2 = $result_method2["price"];
        }

        $result_descrip_method1 = Db::getInstance()->getRow('SELECT `delay` FROM `' . _DB_PREFIX_ . 'carrier_lang` WHERE `id_carrier` = ' . (int) (Configuration::get('FE_CARRIER1_CARRIER_ID')) . ' && `id_lang` = ' . $this->context->language->id);
        if ($result_descrip_method1["delay"]) {
            $delayMethod1 = $result_descrip_method1["delay"];
        }


        $result_descrip_method2 = Db::getInstance()->getRow('SELECT `delay` FROM `' . _DB_PREFIX_ . 'carrier_lang` WHERE `id_carrier` = ' . (int) (Configuration::get('FE_CARRIER2_CARRIER_ID')) . ' && `id_lang` = ' . $this->context->language->id);
        if ($result_descrip_method2["delay"]) {
            $delayMethod2 = $result_descrip_method2["delay"];
        }

        $alert = array();
        if (Configuration::get('FE_CARRIER1_OVERCOST') == '') {
            $alert['carrier1'] = 1;
        }
        if (Configuration::get('FE_CARRIER2_OVERCOST') == '') {
            $alert['carrier2'] = 1;
        }
       
        $display_alert = (count($alert) != 0);

        $this->smarty->assign(array(
            'display_alert' => $display_alert,
            'alert' => $alert,
            'tab' => Tools::getValue('tab'),
            'configure' => Tools::getValue('configure'),
            'token' => Tools::getValue('token'),
            'tab_module' => Tools::getValue('tab_module'),
            'module_name' => Tools::getValue('module_name'),
            'geodis_method1_name' => self::GEODIS_METHOD_1_NAME,
            'geodis_method2_name' => self::GEODIS_METHOD_2_NAME,
            'carrier1_activated' => Configuration::get('FE_CARRIER1_ACTIVATED'),
            'carrier2_activated' => Configuration::get('FE_CARRIER2_ACTIVATED'),
            'method_1_delay' => $delayMethod1,
            'method_2_delay' => $delayMethod2,
            'method_1_price' => $priceMethod1,
            'method_2_price' => $priceMethod2,
            'img_ps_dir' => _PS_IMG_

        ));

        return $this->display(__FILE__, 'views/templates/admin/form.tpl');

    }

    private function _postValidation()
    {
        // Check configuration values
        if (!is_numeric(pSQL(Tools::getValue('mycarrier1_overcost')))) {
            $this->_postErrors[] = $this->l('Please enter a valid price for the shipping method 1 (ex: 7.50)');
        }

        if (!is_numeric(pSQL(Tools::getValue('mycarrier2_overcost')))) {
            $this->_postErrors[] = $this->l('Please enter a valid price for the shipping method 2 (ex: 7.50)');
        }
    }

    private function _postProcess()
    {
        
        // Saving carrier 1 settings
        Configuration::updateValue('FE_CARRIER1_ACTIVATED', (int) Tools::getValue('carrier1_activated'));

        $id_lang = (int) $this->context->language->id;

        Db::getInstance()->Execute(
            'UPDATE `' . _DB_PREFIX_ . 'carrier_lang` SET `delay` = "' . pSQL(Tools::getValue('france_express_carrier1_description')) . '"'
            . 'WHERE `id_carrier` ="' . Configuration::get('FE_CARRIER1_CARRIER_ID') . '" && `id_lang` = '.$id_lang
        );

        $carrier_description = array(
            $id_lang => pSQL(Tools::getValue('france_express_carrier1_long_description'))
        );

        Configuration::updateValue('FE_CARRIER1_LONG_DESCRIPTION', $carrier_description);

        Configuration::updateValue('FE_CARRIER1_OVERCOST', pSQL(Tools::getValue('mycarrier1_overcost')));

        Db::getInstance()->Execute(
            'UPDATE `' . _DB_PREFIX_ . 'delivery` SET `price` = "' . pSQL(Tools::getValue('mycarrier1_overcost')) . '"'
            . 'WHERE `id_carrier` ="' . Configuration::get('FE_CARRIER1_CARRIER_ID') . '"'
        );

        // Saving carrier 2 settings
        Configuration::updateValue('FE_CARRIER2_ACTIVATED', (int) Tools::getValue('carrier2_activated'));
        
        Db::getInstance()->Execute(
            'UPDATE `' . _DB_PREFIX_ . 'carrier_lang` SET `delay` = "' . pSQL(Tools::getValue('france_express_carrier2_description')) . '"'
            . 'WHERE `id_carrier` ="' . Configuration::get('FE_CARRIER2_CARRIER_ID') . '" && `id_lang` = ' . $id_lang
        );
        
        $carrier_description = array(
            $id_lang => pSQL(Tools::getValue('france_express_carrier2_long_description'))
        );

        Configuration::updateValue('FE_CARRIER2_LONG_DESCRIPTION', $carrier_description);

        Configuration::updateValue('FE_CARRIER2_OVERCOST', pSQL(Tools::getValue('mycarrier2_overcost')));
        
        Db::getInstance()->Execute(
            'UPDATE `' . _DB_PREFIX_ . 'delivery` SET `price` = "' . pSQL(Tools::getValue('mycarrier2_overcost')) . '"'
            . 'WHERE `id_carrier` ="' . Configuration::get('FE_CARRIER2_CARRIER_ID') . '"'
        );

        $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
    }

    public function createGeodisZoneCountry($name)
    {
        
        //verify zone exesting

        $zones = Db::getInstance()->executeS('SELECT `id_zone` FROM `' . _DB_PREFIX_ . 'zone` WHERE  `name` LIKE "'.$name.'%"');
        //create zone
        if (!$zones) {

            Db::getInstance()->insert(
                'zone',
                array(
                    'name' => $name,
                    'active' => '1'
                )
            );
            Db::getInstance()->insert(
                'zone_shop',
                array(
                    'id_zone' => (int) Db::getInstance()->Insert_ID(),
                    'id_shop' => '1'
                )
            );
        } else {
            foreach ($zones as $zone) {
                Db::getInstance()->update(
                    'zone',
                    array(
                        'active' => '1',
                    ),
                    'id_zone = '. (int)$zone['id_zone']
                );
            }
        }

        $list_destinations_country = array_unique(array_merge($this->geodisDestinationCountryMethod1IsoCode, $this->geodisDestinationCountryMethod2IsoCode));
        foreach ($list_destinations_country as $country) {
            $result_country = Db::getInstance()->getRow('SELECT `id_country` FROM `' . _DB_PREFIX_ . 'country_lang` WHERE `name` LIKE "' . $country . '"');
            if ($result_country['id_country']) {
                Db::getInstance()->update(
                    'country',
                    array(
                        'active' => '1'
                    ),
                    'id_country = '. $result_country['id_country']
                );
            }
        }

    }

    /**
     *
     * @param type $params
     * @return type
     */
    public function hookdisplayCarrierList($params)
    {
        $return = array();
        $this->context->controller->addCSS(($this->_path) . 'views/css/geodis.css');
        
        $customer = new Customer((int) $this->context->customer->id);
        $customerInfo = $customer->getAddresses((int) $this->context->language->id);

        $cart = new Cart($this->context->cart->id);
        $adress = new Address((int) $cart->id_address_delivery);
        $id_country = $adress->id_country;
        $result = Db::getInstance()->getRow(
            'SELECT `name` FROM `' . _DB_PREFIX_ . 'country_lang`
            WHERE `id_country` = ' . (int)($id_country) . '
            && `id_lang` = ' . ($this->context->language->id ? (int)($this->context->language->id) : Configuration::get('PS_LANG_DEFAULT'))
        );
        $customerAdressCountryCode = $result['name'];

        $idcart = $this->context->cart->id;

        $products = $this->context->cart->getProducts();
        $PackageWeight = 0;
        $PackageHeight = 0;
        $PackageWidth = 0;
        foreach ($products as $product) {
            $PackageWeight += ($product['weight'] * $product['quantity']);
            if ($product['height'] > $PackageHeight) {
                $PackageHeight = $product['height'];
            }
            if ($product['width'] > $PackageWidth) {
                $PackageWidth = $product['width'];
            }
        }

        $keyOdExpressDestinationCountry = array_search($customerAdressCountryCode, $this->geodisDestinationCountryMethod1);

        $keyRdvTelMessagerieDestinationCountry = array_search($customerAdressCountryCode, $this->geodisDestinationCountryMethod2);

        if ($keyOdExpressDestinationCountry && ((int) $PackageWeight < (int) self::GEODIS_POIDS_MAX) && ((int) $PackageHeight < (int) self::GEODIS_HAUTEUR_MAX) && ((int) $PackageWidth < (int) self::GEODIS_LONGUEUR_MAX)) {
            $return[Configuration::get('FE_CARRIER1_CARRIER_ID')] = 1;
        } else {
            $return[Configuration::get('FE_CARRIER1_CARRIER_ID')] = 0;
        }



        if ($keyRdvTelMessagerieDestinationCountry && ((int) $PackageWeight < (int) self::GEODIS_POIDS_MAX) && ((int) $PackageHeight < (int) self::GEODIS_HAUTEUR_MAX) && ((int) $PackageWidth < (int) self::GEODIS_LONGUEUR_MAX)) {
            $return[Configuration::get('FE_CARRIER2_CARRIER_ID')] = 1;
        } else {
            $return[Configuration::get('FE_CARRIER2_CARRIER_ID')] = 0;
        }

        //echo'<pre>';var_dump($return);die();

        $this->context->smarty->assign(
            array(
                'france_express_id_carrier1' => Configuration::get('FE_CARRIER1_CARRIER_ID'),
                'france_express_carrier1_name' => self::GEODIS_METHOD_1_NAME,
                'france_express_carrier1_description' => Configuration::get('FE_CARRIER1_DESCRIPTION', $this->context->language->id),
                'france_express_carrier1_logo' => self::GEODIS_METHOD_1_LOGO,
                'france_express_id_carrier2' => Configuration::get('FE_CARRIER2_CARRIER_ID'),
                'france_express_carrier2_name' => self::GEODIS_METHOD_2_NAME,
                'france_express_carrier2_description' => Configuration::get('FE_CARRIER2_DESCRIPTION', $this->context->language->id),
                'france_express_carrier2_logo' => self::GEODIS_METHOD_2_LOGO,
                'mobile' => $customerInfo[0]['phone_mobile'],
                'fixe' => $customerInfo[0]['phone'],
                'email' => $customer->email,
                'id_card' => $idcart,
                'is_enabled' => $return,
                'customer_adress_country_code' => $customerAdressCountryCode
            )
        );
        return $this->display(__FILE__, 'views/templates/front/formFranceExpress.tpl');
    }

    /*
     * * Hook update carrier
     * *
     */

    public function hookupdateCarrier($params)
    {
        if ((int) ($params['id_carrier']) == (int) (Configuration::get('FE_CARRIER1_CARRIER_ID'))) {
            Configuration::updateValue('FE_CARRIER1_CARRIER_ID', (int) ($params['carrier']->id));
        }

        if ((int) ($params['id_carrier']) == (int) (Configuration::get('FE_CARRIER2_CARRIER_ID'))) {
            Configuration::updateValue('FE_CARRIER2_CARRIER_ID', (int) ($params['carrier']->id));
        }
    }

    public function hookdisplayAdminOrder($params)
    {
        if (($id_order = (int) Tools::getValue('id_order')) || Validate::isUnsignedId($id_order)) {
            $order = new Order($id_order);
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'orders o INNER JOIN ' . _DB_PREFIX_ . 'geodis_france_express gs ON o.id_cart = gs.cart_id WHERE  o.id_order =' . $order->id;
            $row = Db::getInstance()->getRow($sql);
        }
        $this->context->smarty->assign(array(
            'geodis_france_express_information' => $row,
            'version' => _PS_VERSION_
        ));
        return $this->display(__FILE__, 'views/templates/admin/franceexpressorderdetail.tpl');
    }

    /*
     * * Front Methods
     * *
     * * If you set need_range at true when you created your carrier (in install method), the method called by the cart will be getOrderShippingCost
     * * If not, the method called will be getOrderShippingCostExternal
     * *
     * * $params var contains the cart, the customer, the address
     * * $shipping_cost var contains the price calculated by the range in carrier tab
     * *
     */

    /**
     * display Geodis method informations in order detail
     * @param type $params
     * @return type
     */
    public function hookOrderDetailDisplayed()
    {
        if (($id_order = (int) Tools::getValue('id_order')) || Validate::isUnsignedId($id_order)) {
            $order = new Order($id_order);
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'orders o INNER JOIN ' . _DB_PREFIX_ . 'geodis_france_express gs ON o.id_cart = gs.cart_id WHERE  o.id_order =' . $order->id;
            $row = Db::getInstance()->getRow($sql);
        }
        $this->context->smarty->assign(array(
            'geodis_france_express_information' => $row,
            'version' => _PS_VERSION_
        ));
        return $this->display(__FILE__, 'views/templates/front/geodisFranceExpressOrderDetail.tpl');
    }

    public function getOrderShippingCost($params, $shipping_cost)
    {
        
        if (Configuration::get('PS_SHOP_COUNTRY_ID')) {
            $id_origin_country = Configuration::get('PS_SHOP_COUNTRY_ID');
        } else {
            $id_origin_country = Configuration::get('PS_COUNTRY_DEFAULT');
        }

        $result_Country = Db::getInstance()->getRow('SELECT `name` FROM `' . _DB_PREFIX_ . 'country_lang`
                                            WHERE `id_country` = ' . (int)($id_origin_country) . '
                                            && `id_lang` = ' . ($this->context->language->id ? (int)($this->context->language->id) : Configuration::get('PS_LANG_DEFAULT')));
        $originCountryCode = $result_Country['name'];

        $keyOdExpressOriginCountry = array_search($originCountryCode, $this->geodisOriginCountryMethod1);

        $keyRdvTelMessagerieOriginCountry = array_search($originCountryCode, $this->geodisOriginCountryMethod2);

        // This example returns shipping cost with overcost set in the back-office, but you can call a webservice or calculate what you want before returning the final value to the Cart
        if ($this->id_carrier == (int) (Configuration::get('FE_CARRIER1_CARRIER_ID')) && Configuration::get('FE_CARRIER1_OVERCOST') !== null && Configuration::get('FE_CARRIER1_OVERCOST') >= 0 && $keyOdExpressOriginCountry) {
            $result_method1 = Db::getInstance()->getRow('SELECT `price` FROM `' . _DB_PREFIX_ . 'delivery` WHERE `id_carrier` = ' . (int)(Configuration::get('FE_CARRIER1_CARRIER_ID')));
            return $result_method1["price"];
        }


        if ($this->id_carrier == (int) (Configuration::get('FE_CARRIER2_CARRIER_ID')) && Configuration::get('FE_CARRIER2_OVERCOST') !== null && Configuration::get('FE_CARRIER2_OVERCOST') >= 0 && $keyRdvTelMessagerieOriginCountry) {
            $result_method2 = Db::getInstance()->getRow('SELECT `price` FROM `' . _DB_PREFIX_ . 'delivery` WHERE `id_carrier` = ' . (int)(Configuration::get('FE_CARRIER2_CARRIER_ID')));
            return $result_method2["price"];
        }
        // If the carrier is not known, you can return false, the carrier won't appear in the order process
        return false;
    }

    public function getOrderShippingCostExternal($params)
    {

        if (Configuration::get('PS_SHOP_COUNTRY_ID')) {
            $id_origin_country = Configuration::get('PS_SHOP_COUNTRY_ID');
        } else {
            $id_origin_country = Configuration::get('PS_COUNTRY_DEFAULT');
        }

        $result_Country = Db::getInstance()->getRow('SELECT `name` FROM `' . _DB_PREFIX_ . 'country_lang`
                                            WHERE `id_country` = ' . (int)($id_origin_country) . '
                                            && `id_lang` = ' . ($this->context->language->id ? (int)($this->context->language->id) : Configuration::get('PS_LANG_DEFAULT')));
        $originCountryCode = $result_Country['name'];

        $keyOdExpressOriginCountry = array_search($originCountryCode, $this->geodisOriginCountryMethod1);

        $keyRdvTelMessagerieOriginCountry = array_search($originCountryCode, $this->geodisOriginCountryMethod2);

        // This example returns shipping cost with overcost set in the back-office, but you can call a webservice or calculate what you want before returning the final value to the Cart
        if ($this->id_carrier == (int) (Configuration::get('FE_CARRIER1_CARRIER_ID')) && Configuration::get('FE_CARRIER1_OVERCOST') !== null && Configuration::get('FE_CARRIER1_OVERCOST') >= 0 && $keyOdExpressOriginCountry) {
            return (float) (Configuration::get('FE_CARRIER1_OVERCOST'));
        }

        if ($this->id_carrier == (int) (Configuration::get('FE_CARRIER2_CARRIER_ID')) && Configuration::get('FE_CARRIER2_OVERCOST') !== null && Configuration::get('FE_CARRIER2_OVERCOST') >= 0 && $keyRdvTelMessagerieOriginCountry) {
            return (float) (Configuration::get('FE_CARRIER2_OVERCOST'));
        }
        // If the carrier is not known, you can return false, the carrier won't appear in the order process
        return false;
    }
}
