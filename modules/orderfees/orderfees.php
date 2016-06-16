<?php
/**
 *  Order Fees
 *
 *  @author    motionSeed <ecommerce@motionseed.com>
 *  @copyright 2016 motionSeed. All rights reserved.
 *  @license   https://www.motionseed.com/license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!class_exists('MotionSeedModule')) {
    include_once(dirname(__FILE__) . '/helpers/motionseed-module/MotionSeedModule.php');
}

class OrderFees extends MotionSeedModule
{
    const IS_FEE = 1;
    const IN_SHIPPING = 2;
    
    const CONTEXT_CART = 4;
    const CONTEXT_PRODUCT = 8;
    const CONTEXT_PAYMENT = 16;
    const CONTEXT_CARRIER = 32;
    const CONTEXT_MAIL = 64;
    const CONTEXT_PDF = 128;
    const CONTEXT_ALL = 252;
    
    const DATE_FROM_MASK = '0001-01-01 00:00:00';
    const DATE_TO_MASK = '9999-01-01 00:00:00';
    
    public function __construct()
    {
        $this->name = 'orderfees';
        $this->tab = 'pricing_promotion';
        $this->version = '1.7.1';
        $this->author = 'motionSeed';
        $this->need_instance = 0;
        $this->ps_versions_compliancy['min'] = '1.6.0.0';

        parent::__construct();
        
        $this->displayName = $this->l('Order Fees');
        $this->description = $this->l('Add any kind of fees to your client\'s order');

        $this->error = false;
        $this->secure_key = Tools::encrypt($this->name);
        $this->module_key = '4c0a83cf8d16bec8068ffd6d9ffdeeed';
        
        $this->configurations = array(
            array(
                'name' => 'MS_ORDERFEES_CONDITIONS_DISPLAY_SKU',
                'label' => 'Display SKU on Products Selection',
                'default' => '0'
            )
        );
    }
    
    public function getContent()
    {
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminOrderFees'));
    }

    public function registerHooks()
    {
        return parent::registerHooks()
            && $this->registerHook('actionAdminCartRulesListingFieldsModifier')
            && $this->registerHook('actionObjectCartRuleUpdateBefore')
            && $this->registerHook('actionAssociatedRestrictionsPayment')
            && $this->registerHook('actionCartRuleCtor')
            && $this->registerHook('actionCartRuleCheckValidity')
            && $this->registerHook('actionCartRuleGetContextualValueBefore')
            && $this->registerHook('actionCartRuleGetContextualValueAfter')
            && $this->registerHook('actionCartRuleAdd')
            && $this->registerHook('actionCartRuleRemove')
            && $this->registerHook('actionCartGetPackageShippingCost')
            && $this->registerHook('actionAdminCartsControllerHelperDisplay')
            && $this->registerHook('actionAdminOrdersControllerHelperDisplay')
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayAdminCartsView')
            && $this->registerHook('displayAdminOrder')
            && $this->registerHook('displayCartRuleBlockCart')
            && $this->registerHook('displayCartRuleBlockCartLayer')
            && $this->registerHook('displayCartRuleShoppingCart')
            && $this->registerHook('displayCartRuleOrderDetail')
            && $this->registerHook('displayCartRuleOrderPayment')
            && $this->registerHook('displayCartRuleInvoiceProductTab')
            && $this->registerHook('displayCartRuleInvoiceB2B')
            && $this->registerHook('displayCartRuleDeliverySlipProductTab')
            && $this->registerHook('displayCartRuleOrderSlipProductTab');
    }
    
    public function hookActionAdminCartRulesListingFieldsModifier($params)
    {
        $params['where'] = ' AND is_fee = 0';
    }
    
    public function hookActionObjectCartRuleUpdateBefore($params)
    {
        $object = $params['object'];
        
        if ($object->is_fee & self::IS_FEE) {
            $object->quantity = 1;
        }
    }
    
    public function hookActionAssociatedRestrictionsPayment($params)
    {
        $object = $params['object'];
        $type = $params['type'];
        $offset = $params['offset'];
        $limit = $params['limit'];
        $active_only = $params['active_only'];
        
        $array = array('selected' => array(), 'unselected' => array());
        
        $hook_payment = 'Payment';
        
        if (Db::getInstance()->getValue(
            'SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = \'displayPayment\''
        )) {
            $hook_payment = 'displayPayment';
        }

        if ($offset !== null && $limit !== null) {
            $sql_limit = ' LIMIT '.(int)$offset.', '.(int)($limit+1);
        } else {
            $sql_limit = '';
        }

        if (!Validate::isLoadedObject($object) || $object->{$type.'_restriction'} == 0) {
            $array['selected'] = Db::getInstance()->executeS(
                'SELECT t.*, 1 as selected
                FROM `'._DB_PREFIX_.'module` t
                INNER JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = t.`id_module`
                INNER JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook` 
                WHERE h.`name` = \''.pSQL($hook_payment).'\'
                    '.($active_only ? ' AND t.active = 1' : '').
                ' ORDER BY t.name ASC ' . bqSQL($sql_limit)
            );
        } else {
            $resource = Db::getInstance()->query(
                'SELECT t.*, IF(crt.id_module IS NULL, 0, 1) as selected
                FROM `'._DB_PREFIX_.'module` t
                INNER JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = t.`id_module`
                INNER JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook` 
                LEFT JOIN (
                    SELECT id_module FROM `'._DB_PREFIX_.'cart_rule_'. bqSQL($type) .'`
                        WHERE id_cart_rule = '.(int)$object->id.'
                    ) crt
                    ON t.id_module = crt.id_module
                WHERE h.`name` = \''.pSQL($hook_payment).'\' '.($active_only ? ' AND t.active = 1' : '').
                ' ORDER BY t.name ASC ' . bqSQL($sql_limit),
                false
            );
            
            while ($row = Db::getInstance()->nextRow($resource)) {
                $array[($row['selected'] || $object->{$type.'_restriction'} == 0) ? 'selected' : 'unselected'][] = $row;
            }
        }
        
        return $array;
    }
    
    public function hookActionCartRuleCtor($params)
    {
        $object = &$params['object'];
        
        if (isset($object->is_fee)) {
            return;
        }
        
        $object->is_fee = 0;
        $object->payment_restriction = 0;
        $object->dimension_restriction = 0;
        $object->zipcode_restriction = 0;
        
        // Quantity
        $object->quantity = 1;
        $object->unit_value_real = 0;
        $object->unit_value_tax_exc = 0;
        
        // Maximum amount
        $object->maximum_amount = 0;
        $object->maximum_amount_tax = 0;
        $object->maximum_amount_currency = 0;
        $object->maximum_amount_shipping = 0;
        
        if (!property_exists($object, 'definition')) {
            $object::$definition = array('fields' => array());
        }
        
        $object::$definition['fields']['is_fee'] = array(
            'type' => $object::TYPE_INT,
            'validate' => 'isInt'
        );
        $object::$definition['fields']['payment_restriction'] = array(
            'type' => $object::TYPE_BOOL,
            'validate' => 'isBool'
        );
        $object::$definition['fields']['dimension_restriction'] = array(
            'type' => $object::TYPE_BOOL,
            'validate' => 'isBool'
        );
        $object::$definition['fields']['zipcode_restriction'] = array(
            'type' => $object::TYPE_BOOL,
            'validate' => 'isBool'
        );
        
        $object::$definition['fields']['maximum_amount'] = array(
            'type' => $object::TYPE_FLOAT,
            'validate' => 'isFloat'
        );
        $object::$definition['fields']['maximum_amount_tax'] = array(
            'type' => $object::TYPE_BOOL,
            'validate' => 'isBool'
        );
        $object::$definition['fields']['maximum_amount_currency'] = array(
            'type' => $object::TYPE_INT,
            'validate' => 'isInt'
        );
        $object::$definition['fields']['maximum_amount_shipping'] = array(
            'type' => $object::TYPE_BOOL,
            'validate' => 'isBool'
        );
    }
    
    public function hookActionCartRuleCheckValidity(&$params)
    {
        $object = $params['object'];
        $context = $params['context'];
        $check_carrier = $params['check_carrier'];
        
        if ($object->is_fee & self::IS_FEE) {
            if ($context->cart->id_customer) {
                $quantity_used = Db::getInstance()->getValue(
                    'SELECT count(*)
                        FROM ' . _DB_PREFIX_ . 'orders o
                        LEFT JOIN ' . _DB_PREFIX_ . 'order_cart_rule od
                            ON o.id_order = od.id_order
                        WHERE o.id_customer = ' . (int) $context->cart->id_customer . '
                            AND od.id_cart_rule = ' . (int) $object->id . '
                            AND ' . (int) Configuration::get('PS_OS_ERROR') . ' != (
                                SELECT oh.id_order_state
                                    FROM ' . _DB_PREFIX_ . 'order_history oh
                                    WHERE oh.id_order = o.id_order
                                    ORDER BY oh.date_add DESC
                                    LIMIT 1
                                )'
                );
            
                if ($quantity_used + 1 > $object->quantity_per_user) {
                    $object->quantity_per_user = max($object->quantity_per_user, $quantity_used + 1);
                }
            }
            
            if ($object->date_to == self::DATE_TO_MASK) {
                $object->date_to = date('Y-m-d h:i:s', strtotime('+1 year'));
            }
            
            // Payment restriction
            if ($object->payment_restriction) {
                if (!isset($context->controller->module)) {
                    return array(
                        'message' => 'You must choose a payment method before applying this fee to your order'
                    );
                }
                
                $id_cart_rule = (int)Db::getInstance()->getValue(
                    'SELECT crp.id_cart_rule
                    FROM '._DB_PREFIX_.'cart_rule_payment crp
                    INNER JOIN '._DB_PREFIX_.'module m
                        ON crp.id_module = m.id_module
                            AND crp.id_cart_rule = ' . (int)$object->id . '
                            AND m.active = 1
                            AND m.name = "' . pSQL($context->controller->module->name) . '"'
                );
                
                if (!$id_cart_rule) {
                    return array(
                        'message' => 'You cannot use this fee with this payment module'
                    );
                }
            }
            
            // Maximum amount
            if ((int)$object->maximum_amount && $check_carrier) {
                $maximum_amount = $object->maximum_amount;
                if ($object->maximum_amount_currency != Context::getContext()->currency->id) {
                    $maximum_amount = Tools::convertPriceFull(
                        $maximum_amount,
                        new Currency($object->maximum_amount_currency),
                        Context::getContext()->currency
                    );
                }

                $cartTotal = $context->cart->getOrderTotal($object->maximum_amount_tax, Cart::ONLY_PRODUCTS);
                if ($object->maximum_amount_shipping) {
                    $cartTotal += $context->cart->getOrderTotal($object->maximum_amount_tax, Cart::ONLY_SHIPPING);
                }
                $products = $context->cart->getProducts();
                $cart_rules = $context->cart->getCartRules();

                foreach ($cart_rules as &$cart_rule) {
                    if ($cart_rule['gift_product']) {
                        foreach ($products as &$product) {
                            if (empty($product['gift'])
                                && $product['id_product'] == $cart_rule['gift_product']
                                && $product['id_product_attribute'] == $cart_rule['gift_product_attribute']
                            ) {
                                $cartTotal = Tools::ps_round(
                                    $cartTotal - $product[$object->maximum_amount_tax ? 'price_wt' : 'price'],
                                    (int) $context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_
                                );
                            }
                        }
                    }
                }

                if ($cartTotal >= $maximum_amount) {
                    return array(
                        'message' => 'Maximum amount reached'
                    );
                }
            }
            
            // Zipcode restriction
            if ($object->zipcode_restriction) {
                if (!$context->cart->id_address_delivery) {
                    return array(
                        'message' => 'You must choose a delivery address before applying this fee to your order'
                    );
                }
                
                $address = Db::getInstance()->getRow(
                    'SELECT a.id_country, a.postcode
                        FROM '._DB_PREFIX_.'address a
                        WHERE a.id_address = ' . (int)$context->cart->id_address_delivery
                );
                
                $id_country = $address['id_country'];
                $postcode = trim(Tools::strtolower($address['postcode']));
            
                $zipcode_rule_groups = $this->getZipcodeRuleGroups($object);
                
                foreach (array_keys($zipcode_rule_groups) as $id_zipcode_rule_group) {
                    $zipcode_rules = $this->getZipcodeRules($object, $id_zipcode_rule_group);
                    
                    foreach ($zipcode_rules as $zipcode_rule) {
                        if ($zipcode_rule['type'] != '' && $zipcode_rule['type'] != $id_country) {
                            continue;
                        }
                        
                        $operator = $zipcode_rule['operator'];
                        $values = explode(',', $zipcode_rule['value']);
                        
                        foreach ($values as $value) {
                            $value = trim(Tools::strtolower($value));
                            
                            if ($operator == 'begin') {
                                if (strpos($postcode, $value) === 0) {
                                    continue 3;
                                }
                            } elseif ($operator == 'end') {
                                if (strrpos($postcode, $value) + Tools::strlen($value) === Tools::strlen($postcode)) {
                                    continue 3;
                                }
                            } else {
                                $cmp = ($postcode > $value ? 1 : ($postcode == $value ? 0 : - 1));
                                
                                if ($operator == '=' && $cmp == 0) {
                                    continue 3;
                                } elseif ($operator == '>' && $cmp > 0) {
                                    continue 3;
                                } elseif ($operator == '<' && $cmp < 0) {
                                    continue 3;
                                } elseif ($operator == '>=' && $cmp >= 0) {
                                    continue 3;
                                } elseif ($operator == '<=' && $cmp <= 0) {
                                    continue 3;
                                } elseif ($operator == '!=' && $cmp != 0) {
                                    continue 3;
                                }
                            }
                        }
                    }
                    
                    return array(
                        'message' => 'You cannot use this fee with these zipcodes'
                    );
                }
            }
            
            // Dimension restriction
            if ($object->dimension_restriction) {
                $dimensions_available = array('width', 'height', 'depth', 'weight', 'volume');
                $dimensions_products = array('product' => array(), 'all' => array());
                
                $products = $context->cart->getProducts();
                
                foreach ($products as $product) {
                    foreach ($dimensions_available as $dim) {
                        if (isset($product[$dim])) {
                            $dimensions_products['product'][$dim][] = $product[$dim];
                            
                            if (!isset($dimensions_products['all'][$dim])) {
                                $dimensions_products['all'][$dim][0] = 0;
                            }
                            
                            $dimensions_products['all'][$dim][0] += ($product[$dim] * $product['quantity']);
                        }
                    }
                    
                    $volume = $product['height'] * $product['width'] * $product['depth'];
                    
                    $dimensions_products['product']['volume'][] = $volume;
                    
                    if (!isset($dimensions_products['all']['volume'])) {
                        $dimensions_products['all']['volume'][0] = 0;
                    }
                    
                    $dimensions_products['all']['volume'][0] += ($volume * $product['quantity']);
                }
                
                if (!empty($dimensions_products['product'])) {
                    $dimension_rule_groups = $this->getDimensionRuleGroups($object);
                
                    foreach ($dimension_rule_groups as $id_dimension_rule_group => $dimension_rule_group) {
                        $base = $dimension_rule_group['base'];
                        $dimension_rules = $this->getDimensionRules($object, $id_dimension_rule_group);

                        foreach ($dimension_rules as $dimension_rule) {
                            $type = $dimension_rule['type'];
                            $operator = $dimension_rule['operator'];
                            $values = explode(',', $dimension_rule['value']);

                            foreach ($values as $value) {
                                $dimensions = $dimensions_products[$base][$type];
                                $value = trim(Tools::strtolower($value));

                                foreach ($dimensions as $dimension) {
                                    $cmp = ($dimension > $value ? 1 : ($dimension == $value ? 0 : - 1));

                                    if ($operator == '=' && $cmp == 0) {
                                        continue 4;
                                    } elseif ($operator == '>' && $cmp > 0) {
                                        continue 4;
                                    } elseif ($operator == '<' && $cmp < 0) {
                                        continue 4;
                                    } elseif ($operator == '>=' && $cmp >= 0) {
                                        continue 4;
                                    } elseif ($operator == '<=' && $cmp <= 0) {
                                        continue 4;
                                    } elseif ($operator == '!=' && $cmp != 0) {
                                        continue 4;
                                    }
                                }
                            }
                        }

                        return array(
                            'message' => 'You cannot use this fee with these dimensions'
                        );
                    }
                }
            }
        }
    }
    
    public function hookActionCartRuleGetContextualValueBefore($params)
    {
        $object = $params['object'];
        
        if (($object->is_fee & self::IS_FEE) && $object->reduction_product == 0) {
            $params['filter'] = CartRule::FILTER_ACTION_ALL_NOCAP;
        }
    }
    
    public function hookActionCartRuleGetContextualValueAfter($params)
    {
        $object = $params['object'];
        $context = $params['context'];
        $use_tax = $params['use_tax'];
        $current_filter = $params['current_filter'];
        $contextual_value = &$params['contextual_value'];
        
        if ($object->is_fee & self::IN_SHIPPING) {
            $contextual_value = 0;
        } elseif (($object->is_fee & self::IS_FEE) && $current_filter != CartRule::FILTER_ACTION_ALL_NOCAP) {
            $contextual_value = ($context->cart->current_type != Cart::ONLY_DISCOUNTS) ? $contextual_value*-1 : 0;
        }
        
        if ($use_tax) {
            $object->unit_value_real = $contextual_value;
        } else {
            $object->unit_value_tax_exc = $contextual_value;
        }
    }
    
    public function hookActionCartRuleRemove($params)
    {
        $id_cart_rule = $params['id_cart_rule'];
        
        if (Tools::getIsset('deleteDiscount')) {
            $id_cart_rule = (int) Tools::getValue('deleteDiscount');
            $cart_rule = new CartRule($id_cart_rule, Configuration::get('PS_LANG_DEFAULT'));
            if ($cart_rule->id && ($cart_rule->is_fee & self::IS_FEE)) {
                return true;
            }
        }
    }
    
    public function hookActionCartGetPackageShippingCost($params)
    {
        $cart = $params['object'];
        $id_carrier = $params['id_carrier'];
        $use_tax = $params['use_tax'];
        $total = &$params['total'];
        
        $items = Db::getInstance()->executeS(
            'SELECT cr.id_cart_rule, c.id_carrier
            FROM '._DB_PREFIX_.'cart_rule cr
            INNER JOIN '._DB_PREFIX_.'cart_cart_rule ccr
                ON cr.id_cart_rule = ccr.id_cart_rule
                    AND ccr.id_cart = ' . (int) $cart->id . '
            LEFT JOIN '._DB_PREFIX_.'cart_rule_carrier crc
                ON cr.id_cart_rule = crc.id_cart_rule 
            LEFT JOIN '._DB_PREFIX_.'carrier c 
                ON c.id_reference = crc.id_carrier
                    AND c.deleted = 0
            WHERE cr.is_fee & ' . (int) self::IN_SHIPPING . ' AND cr.active = 1'
        );

        if ($items) {
            foreach ($items as $item) {
                if ($item['id_carrier'] == null || $item['id_carrier'] == $id_carrier) {
                    $cart_rule = new CartRule($item['id_cart_rule']);
                    
                    $cart_rule->is_fee = 1;
                    
                    $total += abs($cart_rule->getContextualValue($use_tax, Context::getContext()));
                }
            }
            
        }
        
        // Payment restriction refresh
        static $dummy = false;
        
        if (!$dummy) {
            CartRule::autoRemoveFromCart();
            CartRule::autoAddToCart();
            
            $dummy = true;
        }
    }
    
    public function hookActionAdminCartsControllerHelperDisplay($params)
    {
        $controller = &$params['controller'];
        
        $cart = $controller->tpl_view_vars['cart'];
        
        $controller->tpl_view_vars['discounts'] = $this->getCartRulesByCart($cart);
    }
    
    public function hookActionAdminOrdersControllerHelperDisplay(&$params)
    {
        $controller = $params['controller'];
        
        if (!property_exists($controller, 'currentIndex')) {
            $controller::$currentIndex = Tools::getAdminTokenLite('AdminOrders');
        }
        
        if (Tools::isSubmit('id_order') && Tools::getValue('id_order') > 0) {
            $order = new Order(Tools::getValue('id_order'));
            
            if (!Validate::isLoadedObject($order)) {
                $controller->errors[] = Tools::displayError('The order cannot be found within your database.');
            }
        }
        
        if (Tools::isSubmit('submitNewFee') && isset($order)) {
            if ($controller->tabAccess['edit'] === '1') {
                if (!Tools::getValue('fee_name')) {
                    $controller->errors[] = Tools::displayError('You must specify a name in order to create a new fee');
                } else {
                    if ($order->hasInvoice()) {
                        if (!Tools::isSubmit('fee_all_invoices')) {
                            $order_invoice = new OrderInvoice(Tools::getValue('fee_invoice'));
                            if (!Validate::isLoadedObject($order_invoice)) {
                                throw new PrestaShopException('Can\'t load Order Invoice object');
                            }
                        }
                    }
                    $cart_rules = array();
                    switch (Tools::getValue('fee_type')) {
                        case 1:
                            if (Tools::getValue('fee_value') < 100) {
                                if (isset($order_invoice)) {
                                    $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round(
                                        $order_invoice->total_paid_tax_incl * Tools::getValue('fee_value') / 100,
                                        2
                                    );
                                    
                                    $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round(
                                        $order_invoice->total_paid_tax_excl * Tools::getValue('fee_value') / 100,
                                        2
                                    );
                                    $this->applyFeeOnInvoice(
                                        $order_invoice,
                                        $cart_rules[$order_invoice->id]['value_tax_incl'],
                                        $cart_rules[$order_invoice->id]['value_tax_excl']
                                    );
                                } elseif ($order->hasInvoice()) {
                                    $order_invoices_collection = $order->getInvoicesCollection();
                                    foreach ($order_invoices_collection as $order_invoice) {
                                        $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round(
                                            $order_invoice->total_paid_tax_incl * Tools::getValue('fee_value') / 100,
                                            2
                                        );
                                        
                                        $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round(
                                            $order_invoice->total_paid_tax_excl * Tools::getValue('fee_value') / 100,
                                            2
                                        );
                                        $this->applyFeeOnInvoice(
                                            $order_invoice,
                                            $cart_rules[$order_invoice->id]['value_tax_incl'],
                                            $cart_rules[$order_invoice->id]['value_tax_excl']
                                        );
                                    }
                                } else {
                                    $cart_rules[0]['value_tax_incl'] = Tools::ps_round(
                                        $order->total_paid_tax_incl * Tools::getValue('fee_value') / 100,
                                        2
                                    );
                                    
                                    $cart_rules[0]['value_tax_excl'] = Tools::ps_round(
                                        $order->total_paid_tax_excl * Tools::getValue('fee_value') / 100,
                                        2
                                    );
                                }
                            } else {
                                $controller->errors[] = Tools::displayError('Fee value is invalid');
                            }
                            break;
                        case 2:
                            if (isset($order_invoice)) {
                                $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round(
                                    Tools::getValue('fee_value'),
                                    2
                                );
                                
                                $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round(
                                    Tools::getValue('fee_value') / (1 + ($order->getTaxesAverageUsed() / 100)),
                                    2
                                );
                                $this->applyFeeOnInvoice(
                                    $order_invoice,
                                    $cart_rules[$order_invoice->id]['value_tax_incl'],
                                    $cart_rules[$order_invoice->id]['value_tax_excl']
                                );
                            } elseif ($order->hasInvoice()) {
                                $order_invoices_collection = $order->getInvoicesCollection();
                                foreach ($order_invoices_collection as $order_invoice) {
                                    $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round(
                                        Tools::getValue('fee_value'),
                                        2
                                    );
                                    
                                    $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round(
                                        Tools::getValue('fee_value') / (1 + ($order->getTaxesAverageUsed() / 100)),
                                        2
                                    );
                                    $this->applyFeeOnInvoice(
                                        $order_invoice,
                                        $cart_rules[$order_invoice->id]['value_tax_incl'],
                                        $cart_rules[$order_invoice->id]['value_tax_excl']
                                    );
                                }
                            } else {
                                $cart_rules[0]['value_tax_incl'] = Tools::ps_round(Tools::getValue('fee_value'), 2);
                                $cart_rules[0]['value_tax_excl'] = Tools::ps_round(
                                    Tools::getValue('fee_value') / (1 + ($order->getTaxesAverageUsed() / 100)),
                                    2
                                );
                            }
                            break;
                        default:
                            $controller->errors[] = Tools::displayError('Fee type is invalid');
                    }
                    $res = true;
                    
                    foreach ($cart_rules as &$cart_rule) {
                        $cartRuleObj = new CartRule();
                        $cartRuleObj->is_fee = self::IS_FEE + self::CONTEXT_ALL;
                        $cartRuleObj->date_from = date(
                            'Y-m-d H:i:s',
                            strtotime('-1 hour', strtotime($order->date_add))
                        );
                        $cartRuleObj->date_to = date('Y-m-d H:i:s', strtotime('+1 hour'));
                        $cartRuleObj->name[Configuration::get('PS_LANG_DEFAULT')] = Tools::getValue('fee_name');
                        $cartRuleObj->quantity = 1;
                        $cartRuleObj->partial_use = 0;
                        $cartRuleObj->quantity_per_user = 1;
                        if (Tools::getValue('fee_type') == 1) {
                            $cartRuleObj->reduction_percent = Tools::getValue('fee_value');
                        } elseif (Tools::getValue('fee_type') == 2) {
                            $cartRuleObj->reduction_amount = $cart_rule['value_tax_excl'];
                        } elseif (Tools::getValue('fee_type') == 3) {
                            $cartRuleObj->free_shipping = 1;
                        }
                        $cartRuleObj->active = 0;
                        
                        if ($res = $cartRuleObj->add()) {
                            $cart_rule['id'] = $cartRuleObj->id;
                        } else {
                            break;
                        }
                    }
                    if ($res) {
                        foreach ($cart_rules as $id_order_invoice => $cart_rule) {
                            $order_cart_rule = new OrderCartRule();
                            $order_cart_rule->id_order = $order->id;
                            $order_cart_rule->id_cart_rule = $cart_rule['id'];
                            $order_cart_rule->id_order_invoice = $id_order_invoice;
                            $order_cart_rule->name = Tools::getValue('fee_name');
                            $order_cart_rule->value = $cart_rule['value_tax_incl'];
                            $order_cart_rule->value_tax_excl = $cart_rule['value_tax_excl'];
                            $res &= $order_cart_rule->add();
                            $order->total_paid += $order_cart_rule->value;
                            $order->total_paid_tax_incl += $order_cart_rule->value;
                            $order->total_paid_tax_excl += $order_cart_rule->value_tax_excl;
                        }
                        $res &= $order->update();
                    }
                    if ($res) {
                        Tools::redirectAdmin(
                            $controller::$currentIndex . '&id_order=' . $order->id
                            . '&vieworder&conf=4&token='. $controller->token
                        );
                    } else {
                        $controller->errors[] = Tools::displayError('An error occurred on OrderCartRule creation');
                    }
                }
            } else {
                $controller->errors[] = Tools::displayError('You do not have permission to edit here.');
            }
        } elseif (Tools::isSubmit('submitDeleteFee') && isset($order)) {
            if ($controller->tabAccess['edit'] === '1') {
                $order_cart_rule = new OrderCartRule(Tools::getValue('id_order_cart_rule'));
                if (Validate::isLoadedObject($order_cart_rule) && $order_cart_rule->id_order == $order->id) {
                    if ($order_cart_rule->id_order_invoice) {
                        $order_invoice = new OrderInvoice($order_cart_rule->id_order_invoice);
                        if (!Validate::isLoadedObject($order_invoice)) {
                            throw new PrestaShopException('Can\'t load Order Invoice object');
                        }
                        $order_invoice->total_paid_tax_excl += $order_cart_rule->value_tax_excl;
                        $order_invoice->total_paid_tax_incl += $order_cart_rule->value;
                        $order_invoice->update();
                    }
                    $order->total_paid += $order_cart_rule->value;
                    $order->total_paid_tax_incl += $order_cart_rule->value;
                    $order->total_paid_tax_excl += $order_cart_rule->value_tax_excl;
                    $order_cart_rule->delete();
                    $order->update();
                    Tools::redirectAdmin(
                        $controller::$currentIndex . '&id_order=' . $order->id
                        . '&vieworder&conf=4&token=' . $controller->token
                    );
                } else {
                    $controller->errors[] = Tools::displayError('Cannot edit this Fee');
                }
            } else {
                $controller->errors[] = Tools::displayError('You do not have permission to edit here.');
            }
        }
        
        $order = $controller->tpl_view_vars['order'];
        $controller->tpl_view_vars['fees'] = $this->getFeesByOrder($order);
        
        foreach ($controller->tpl_view_vars['discounts'] as $index => $discount) {
            $object = new CartRule($discount['id_cart_rule']);
            
            if ($object->is_fee > 0 && ($object->is_fee & self::IS_FEE)) {
                unset($controller->tpl_view_vars['discounts'][$index]);
            }
        }
    }

    public function hookDisplayHeader()
    {
        if ((int) Configuration::get('PS_BLOCK_CART_AJAX')) {
            $this->context->controller->addJS(($this->_path) . 'views/js/ajax-cart.js');
        }
    }
    
    public function hookDisplayAdminOrder($params)
    {
        $order = new Order((int) $params['id_order']);
        
        if (!Validate::isLoadedObject($order)) {
            return;
        }
        
        $controller = $this->context->controller;
        
        if (!property_exists($controller, 'currentIndex')) {
            $controller::$currentIndex = Tools::getAdminTokenLite('AdminOrders');
        }
        
        $this->context->smarty->assign(array(
            'fees' => $this->getFeesByOrder($order),
            'order' => $order,
            'currency' => Currency::getCurrencyInstance($order->id_currency),
            'can_edit' => ($controller->tabAccess['edit'] == 1),
            'current_index' => $controller::$currentIndex,
            'current_id_lang' => $this->context->language->id,
            'invoices_collection' => $order->getInvoicesCollection()
        ));
        
        return $this->display(__FILE__, 'admin-order.tpl');
    }
    
    public function hookDisplayAdminCartsView()
    {
        $cart = new Cart((int) Tools::getValue('id_cart'));
        
        if (!Validate::isLoadedObject($cart)) {
            return;
        }
        
        $this->context->smarty->assign(array(
            'fees' => $this->getFeesByCart($cart),
            'currency' => Currency::getCurrencyInstance($cart->id_currency)
        ));
        
        return $this->display(__FILE__, 'admin-cart.tpl');
    }
    
    public function hookDisplayCartRuleInvoiceProductTab($params)
    {
        return $this->displayFeesOnPDF($params, 'invoice-product-tab.tpl');
    }
    
    public function hookDisplayCartRuleInvoiceB2B($params)
    {
        return $this->displayFeesOnPDF($params, 'invoice-b2b.tpl');
    }
    
    public function hookDisplayCartRuleDeliverySlipProductTab($params)
    {
        return $this->displayFeesOnPDF($params, 'delivery-slip-product-tab.tpl');
    }
    
    public function hookDisplayCartRuleOrderSlipProductTab($params)
    {
        return $this->displayFeesOnPDF($params, 'order-slip-product-tab.tpl');
    }
    
    public function displayFeesOnPDF($params, $template, $context = self::CONTEXT_PDF)
    {
        $order = $params['order'];
        
        $this->context->smarty->assign(array(
            'order' => $order,
            'fees' => $this->getFeesByOrder($order, $context)
        ));
        
        $discounts = $params['discounts'];
        
        foreach ($discounts as $index => $discount) {
            $object = new CartRule($discount['id_cart_rule']);
            
            if ($object->is_fee > 0 && ($object->is_fee & self::IS_FEE)) {
                unset($discounts[$index]);
            }
        }
        
        $params['smarty']->assign('cart_rules', $discounts);
        
        return $this->display(__FILE__, $template);
    }
    
    public function hookDisplayCartRuleBlockCart(&$params)
    {
        return $this->displayFees($params, 'blockcart.tpl', self::CONTEXT_CART);
    }
    
    public function hookDisplayCartRuleBlockCartLayer(&$params)
    {
        return $this->displayFees($params, 'blockcart-layer.tpl', self::CONTEXT_CART);
    }
    
    public function hookDisplayCartRuleShoppingCart(&$params)
    {
        return $this->displayFees($params, 'shopping-cart.tpl', self::CONTEXT_CART, true);
    }
    
    public function hookDisplayCartRuleOrderDetail(&$params)
    {
        if (!isset($params['discounts']) || empty($params['discounts'])) {
            return;
        }
        
        $fees = array();
        $discounts = $params['discounts'];
        
        foreach ($discounts as $index => $discount) {
            $object = new CartRule($discount['id_cart_rule']);
            
            if ($object->is_fee > 0 && ($object->is_fee & self::IS_FEE) && ($object->is_fee & self::CONTEXT_ALL)) {
                $fees[] = array(
                    'obj' => $object,
                    'name' => $discount['name'],
                    'quantity' => 1,
                    'value_unit' => $discount['value'],
                    'value' => $discount['value']
                );
                
                unset($discounts[$index]);
            }
        }
        
        $this->context->smarty->assign('fees', $fees);
        $params['smarty']->assign('discounts', $discounts);
        
        return $this->display(__FILE__, 'order-detail.tpl');
    }
    
    public function hookDisplayCartRuleOrderPayment(&$params)
    {
        return $this->displayFees($params, 'order-payment.tpl', self::CONTEXT_PAYMENT, true);
    }
    
    public function displayFees(&$params, $template, $context = self::CONTEXT_ALL, $unset = false)
    {
        if (!isset($params['discounts']) || empty($params['discounts'])) {
            return;
        }
        
        $fees = array();
        $discounts = $params['discounts'];
        
        foreach ($discounts as $index => $discount) {
            if ($discount['is_fee'] > 0 && ($discount['is_fee'] & $context)) {
                $fees[] = $discount;
                
                if ($unset) {
                    unset($discounts[$index]);
                }
            }
        }
        
        $this->context->smarty->assign('fees', $fees);
        $params['smarty']->assign('discounts', $discounts);
        
        return $this->display(__FILE__, $template);
    }
    
    public function getFeesByOrder($order, $context = self::CONTEXT_ALL)
    {
        $order = !is_numeric($order) ? $order : new Order($order);
        
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT ocr.*, crl.`id_lang`, crl.`name`
            FROM `' . _DB_PREFIX_ . 'order_cart_rule` ocr
            LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule` cr
                ON cr.`id_cart_rule` = ocr.`id_cart_rule`
            LEFT JOIN `'._DB_PREFIX_.'cart_rule_lang` crl
                ON cr.`id_cart_rule` = crl.`id_cart_rule`
					AND crl.id_lang = ' . (int) Context::getContext()->language->id . '
            WHERE (cr.is_fee & ' . (int) self::IS_FEE . ')
                AND (cr.is_fee & ' . (int) $context . ')
                AND ocr.`id_order` = ' . (int) $order->id
        );
        
        foreach ($result as &$row) {
            $row['obj'] = new CartRule($row['id_cart_rule'], (int) $order->id);
            $row['name'] = $row['name'];
            $row['quantity'] = 1;
            $row['value_unit'] = $row['value'];
            $row['value'] = $row['value'];
        }
        
        return $result;
    }
    
    public function getFeesByCart($cart)
    {
        $cart = !is_numeric($cart) ? $cart : new Cart($cart);
        
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT *
                FROM `' . _DB_PREFIX_ . 'cart_cart_rule` cd
                LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule` cr ON cd.`id_cart_rule` = cr.`id_cart_rule`
                LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule_lang` crl ON (
                    cd.`id_cart_rule` = crl.`id_cart_rule`
                    AND crl.id_lang = ' . (int) $cart->id_lang . '
                    )
                WHERE (cr.is_fee & ' . (int) self::IS_FEE . ') AND `id_cart` = ' . (int) $cart->id
        );
        
        $virtual_context = Context::getContext()->cloneContext();
        $virtual_context->cart = $cart;
        
        foreach ($result as &$row) {
            $row['obj'] = new CartRule($row['id_cart_rule'], (int) $cart->id_lang);
            $row['value_real'] = $row['obj']->getContextualValue(true, $virtual_context);
            $row['value_tax_exc'] = $row['obj']->getContextualValue(false, $virtual_context);
            $row['id_discount'] = $row['id_cart_rule'];
            $row['description'] = $row['name'];
        }
        
        return $result;
    }
    
    public function getCartRulesByCart($cart)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT *
                FROM `' . _DB_PREFIX_ . 'cart_cart_rule` cd
                LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule` cr ON cd.`id_cart_rule` = cr.`id_cart_rule`
                LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule_lang` crl ON (
                    cd.`id_cart_rule` = crl.`id_cart_rule`
                    AND crl.id_lang = ' . (int) $cart->id_lang . '
                    )
                WHERE cr.is_fee = 0 AND `id_cart` = ' . (int) $cart->id
        );
        
        $virtual_context = Context::getContext()->cloneContext();
        $virtual_context->cart = $cart;
        
        foreach ($result as &$row) {
            $row['obj'] = new CartRule($row['id_cart_rule'], (int) $cart->id_lang);
            $row['value_real'] = $row['obj']->getContextualValue(true, $virtual_context);
            $row['value_tax_exc'] = $row['obj']->getContextualValue(false, $virtual_context);
            $row['id_discount'] = $row['id_cart_rule'];
            $row['description'] = $row['name'];
        }
        
        return $result;
    }
    
    public function applyFeeOnInvoice($order_invoice, $value_tax_incl, $value_tax_excl)
    {
        $order_invoice->total_paid_tax_incl += $value_tax_incl;
        $order_invoice->total_paid_tax_excl += $value_tax_excl;
        $order_invoice->update();
    }
    
    public function getDimensionRuleGroups($object)
    {
        if (!Validate::isLoadedObject($object) || $object->dimension_restriction == 0) {
            return array();
        }

        $dimensionRuleGroups = array();
        
        $result = Db::getInstance()->executeS(
            'SELECT * FROM '._DB_PREFIX_.'cart_rule_dimension_rule_group WHERE id_cart_rule = '.(int)$object->id
        );
        
        foreach ($result as $row) {
            if (!isset($dimensionRuleGroups[$row['id_dimension_rule_group']])) {
                $dimensionRuleGroups[$row['id_dimension_rule_group']] = array(
                    'id_dimension_rule_group' => $row['id_dimension_rule_group'],
                    'base' => $row['base']
                );
            }
            $dimensionRuleGroups[$row['id_dimension_rule_group']]['dimension_rules'] = $this->getDimensionRules(
                $object,
                $row['id_dimension_rule_group']
            );
        }
        return $dimensionRuleGroups;
    }

    public function getDimensionRules($object, $id_dimension_rule_group)
    {
        if (!Validate::isLoadedObject($object) || $object->dimension_restriction == 0) {
            return array();
        }

        $dimensionRules = array();
        $results = Db::getInstance()->executeS(
            'SELECT *
                FROM '._DB_PREFIX_.'cart_rule_dimension_rule pr
                WHERE pr.id_dimension_rule_group = ' . (int) $id_dimension_rule_group
        );
        
        foreach ($results as $row) {
            $dimensionRules[$row['id_dimension_rule']] = array(
                'type' => $row['type'],
                'operator' => $row['operator'],
                'value' => $row['value']
            );
        }
        
        return $dimensionRules;
    }
    
    public function getZipcodeRuleGroups($object)
    {
        if (!Validate::isLoadedObject($object) || $object->zipcode_restriction == 0) {
            return array();
        }

        $zipcodeRuleGroups = array();
        
        $result = Db::getInstance()->executeS(
            'SELECT * FROM '._DB_PREFIX_.'cart_rule_zipcode_rule_group WHERE id_cart_rule = '.(int)$object->id
        );
        
        foreach ($result as $row) {
            if (!isset($zipcodeRuleGroups[$row['id_zipcode_rule_group']])) {
                $zipcodeRuleGroups[$row['id_zipcode_rule_group']] = array(
                    'id_zipcode_rule_group' => $row['id_zipcode_rule_group']
                );
            }
            $zipcodeRuleGroups[$row['id_zipcode_rule_group']]['zipcode_rules'] = $this->getZipcodeRules(
                $object,
                $row['id_zipcode_rule_group']
            );
        }
        return $zipcodeRuleGroups;
    }

    public function getZipcodeRules($object, $id_zipcode_rule_group)
    {
        if (!Validate::isLoadedObject($object) || $object->zipcode_restriction == 0) {
            return array();
        }

        $zipcodeRules = array();
        $results = Db::getInstance()->executeS(
            'SELECT *
                FROM '._DB_PREFIX_.'cart_rule_zipcode_rule pr
                WHERE pr.id_zipcode_rule_group = ' . (int) $id_zipcode_rule_group
        );
        
        foreach ($results as $row) {
            $zipcodeRules[$row['id_zipcode_rule']] = array(
                'type' => $row['type'],
                'operator' => $row['operator'],
                'value' => $row['value']
            );
        }
        
        return $zipcodeRules;
    }
}
