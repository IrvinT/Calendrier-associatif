<?php
/**
 *  Order Fees
 *
 *  @author    motionSeed <ecommerce@motionseed.com>
 *  @copyright 2016 motionSeed. All rights reserved.
 *  @license   https://www.motionseed.com/license
 */

class CartRule extends CartRuleCore
{

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        Hook::exec('actionCartRuleCtor', array(
            'object' => &$this
        ));
        
        parent::__construct($id, $id_lang, $id_shop);
    }

    public function getContextualValue(
        $use_tax,
        Context $context = null,
        $filter = null,
        $package = null,
        $use_cache = true
    ) {
        $current_filter = $filter;
        
        $results = Hook::exec('actionCartRuleGetContextualValueBefore', array(
            'object' => &$this,
            'context' => &$context,
            'use_tax' => &$use_tax,
            'filter' => &$filter,
            'current_filter' => $current_filter,
            'package' => &$package,
            'use_cache' => &$use_cache
        ), null, true);
        
        if (is_array($results)) {
            foreach ($results as $result) {
                if ($result !== null) {
                    return $result;
                }
            }
        }
        
        $contextual_value = parent::getContextualValue($use_tax, $context, $filter, $package, $use_cache);
        
        $results = Hook::exec('actionCartRuleGetContextualValueAfter', array(
            'object' => &$this,
            'context' => &$context,
            'use_tax' => &$use_tax,
            'filter' => &$filter,
            'current_filter' => $current_filter,
            'package' => &$package,
            'use_cache' => &$use_cache,
            'contextual_value' => &$contextual_value
        ), null, true);
        
        if (is_array($results)) {
            foreach ($results as $result) {
                if ($result !== null) {
                    return $result;
                }
            }
        }
        
        return $contextual_value;
    }

    public function checkValidity(
        Context $context,
        $alreadyInCart = false,
        $display_error = true,
        $check_carrier = true
    ) {
        $results = Hook::exec('actionCartRuleCheckValidity', array(
            'object' => &$this,
            'context' => &$context,
            'alreadyInCart' => &$alreadyInCart,
            'display_error' => &$display_error,
            'check_carrier' => &$check_carrier
        ), null, true);
        
        if (is_array($results)) {
            foreach ($results as $result) {
                if ($result !== null) {
                    return (!$display_error) ? false : Tools::displayError($result['message']);
                }
            }
        }

        return parent::checkValidity($context, $alreadyInCart, $display_error, $check_carrier);
    }

    public static function getCustomerCartRules(
        $id_lang,
        $id_customer,
        $active = false,
        $includeGeneric = true,
        $inStock = false,
        Cart $cart = null
    ) {
        $result = parent::getCustomerCartRules(
            $id_lang,
            $id_customer,
            $active,
            $includeGeneric,
            $inStock,
            $cart
        );
        
        foreach ($result as $key => $cart_rule) {
            if ($cart_rule['is_fee'] > 0) {
                unset($result[$key]);
            }
        }
        
        return $result;
    }
    
    public function getAssociatedRestrictions(
        $type,
        $active_only,
        $i18n,
        $offset = null,
        $limit = null,
        $search_cart_rule_name = ''
    ) {
        $result = Hook::exec('actionAssociatedRestrictions' . Tools::ucfirst($type), array(
            'object' => &$this,
            'type' => &$type,
            'active_only' => &$active_only,
            'i18n' => &$i18n,
            'offset' => &$offset,
            'limit' => &$limit,
            'search_cart_rule_name' => &$search_cart_rule_name
        ), null, true);
        
        if (is_array($result) && $associated_restrictions = reset($result)) {
            return $associated_restrictions;
        }
        
        return parent::getAssociatedRestrictions(
            $type,
            $active_only,
            $i18n,
            $offset,
            $limit,
            $search_cart_rule_name
        );
    }
}
