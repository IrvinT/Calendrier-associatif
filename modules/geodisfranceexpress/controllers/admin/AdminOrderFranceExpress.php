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

class AdminOrderFranceExpressController extends ModuleAdminController
{
    /**
     * construction de la field_list (datagrid de prestashop) + la requete qui permet de faire la 
     * selection des commandes dont geodis est le mode de livrasion choisi
     */
    public function __construct()
    {
        $this->table = 'order';
        $this->className = 'Order';
        $this->lang = false;
        $this->list_no_link = true;
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->deleted = false;
        $this->simple_header = false;
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->addRowAction('edit'); //add an edit button

        $query  = 'a.id_currency, a.date_add as date, a.id_order AS id_pdf, cl.name as country, c.*, CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`, ';
        $query .= 'osl.`name` AS `osname`, os.`color`, gs.`email`, gs.`phone`, gs.`mobile`, ca.name as carrier_name, ';
        $query .= 'IF((SELECT COUNT(so.id_order) FROM `' . _DB_PREFIX_ . 'orders` so WHERE so.id_customer = a.id_customer) > 1, 0, 1) as new';
        $this->_select = $query;

        $join  = 'LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`) ';
        $join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'address` ad ON (ad.`id_address` = a.`id_address_delivery`) ';
        $join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (cl.`id_country` = ad.`id_country` AND cl.`id_lang` = ' . (int) $this->context->language->id . ') ';
        $join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = a.`current_state`) ';
        $join .= 'INNER JOIN `' . _DB_PREFIX_ . 'geodis_france_express` gs ON (gs.`cart_id` = a.`id_cart`) ';
        $join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'carrier` ca ON (ca.`id_carrier` = a.`id_carrier`) ';
        $join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = ' . (int) $this->context->language->id . ')';

        $this->_join = $join;

        $this->_orderBy = 'id_order';
        $this->_orderWay = 'DESC';

        $statuses_array = array();
        $statuses = OrderState::getOrderStates((int) $this->context->language->id);

        foreach ($statuses as $status) {
            $statuses_array[$status['id_order_state']] = $status['name'];
        }

        $this->fields_list = array();
        $this->fields_list = array(
            'id_order' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25
            ),
            'reference' => array(
                'title' => $this->l('Référence de paiement'),
                'width' => 25,
                'orderby' => false,
                'search' => false,
                'havingFilter' => true,
            ),
            'firstname' => array(
                'title' => $this->l('Nom client'),
                'width' => 25,
                'orderby' => false,
                'search' => false,
                'havingFilter' => true,
            ),
            'lastname' => array(
                'title' => $this->l('Prénom client'),
                'width' => 25,
                'orderby' => false,
                'search' => false,
                'havingFilter' => true,
            ),
           
            'email' => array(
                'title' => $this->l('email client FE'),
                'width' => 25,
                'orderby' => false,
                'search' => false,
                'havingFilter' => true,
            ),
            'phone' => array(
                'title' => $this->l('Téléphone fixe client FE'),
                'width' => 25,
                'orderby' => false,
                'search' => false,
                'havingFilter' => true,
            ),
            'mobile' => array(
                'title' => $this->l('Téléphone mobile client FE'),
                'width' => 25,
                'orderby' => false,
                'search' => false,
                'havingFilter' => true,
            ),
            'total_paid_tax_incl' => array(
                'title' => $this->l('Total'),
                'width' => 70,
                'align' => 'right',
                'prefix' => '<b>',
                'suffix' => '</b>',
                'type' => 'price',
                'orderby' => false,
                'search' => false,
                'currency' => true
            ),
            'payment' => array(
                'title' => $this->l('Paiement '),
                'orderby' => false,
                'search' => false,
                'width' => 100
            ),
            'carrier_name' => array(
                'title' => $this->l('Shipping method '),
                'orderby' => false,
                'search' => false,
                'width' => 100,
                'callback' => 'transporteur'
            ),
            'osname' => array(
                'title' => $this->l('Status'),
                'color' => 'color',
                'width' => 100,
                'type' => 'select',
                'list' => $statuses_array,
                'orderby' => false,
                'search' => false,
                'filter_key' => 'os!id_order_state',
                'filter_type' => 'int'
            ),
            'date' => array(
                'title' => $this->l('Date'),
                'width' => 130,
                'align' => 'right',
                'type' => 'datetime',
                'filter_key' => 'date'
            ),
            'country' => array(
                'title' => $this->l('Pays'),
                'width' => 60,
                'align' => 'center',
                'orderby' => false,
                'search' => false,
                'filter_key' => 'cl!name'),
        );

        parent::__construct();
    }

    public function processFilter()
    {
        parent::processFilter();
        $this->_filter = str_replace('`carrier_name`', 'ca.name', $this->_filter);
    }

    //display france express shipping method with other color
    public function transporteur($carrier_name)
    {
        if (strpos(Tools::strtolower($carrier_name), 'geodis') !== false) {
            return '<span class="color_field" style="background-color:salmon;color:white">' . $carrier_name . '</span>';
        } else {
            return $carrier_name;
        }
    }

    /**
     * suppression de boutton edit 
     */
    public function initToolbar()
    {

        $this->toolbar_title = $this->l('List of orders by France Express ON DEMAND LOGO.');
        unset($this->toolbar_btn['edit']);
    }

    /**
     * action de la formulaire : 
     */
    public function postProcess()
    {
        if (Tools::isSubmit('updateorder')) {
            $context = Context::getcontext();
            $id_order = Tools::getValue('id_order');
            $url = 'index.php?controller=AdminOrders&id_order=' . $id_order . '&vieworder';
            $url .= '&token=' . Tools::getAdminToken('AdminOrders' . (int) (Tab::getIdFromClassName('AdminOrders')) . (int)($context->employee->id));

            Tools::redirectAdmin($url);
        }
    }
}
