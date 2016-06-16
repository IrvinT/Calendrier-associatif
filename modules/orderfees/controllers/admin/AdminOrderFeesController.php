<?php
/**
 *  Order Fees
 *
 *  @author    motionSeed <ecommerce@motionseed.com>
 *  @copyright 2016 motionSeed. All rights reserved.
 *  @license   https://www.motionseed.com/license
 */

class AdminOrderFeesController extends AdminCartRulesControllerCore
{
    
    public function __construct()
    {
        parent::__construct();

        // Remove unused fields
        unset($this->fields_list['code'], $this->fields_list['quantity'], $this->fields_list['date_to']);

        $tab = new Tab($this->id);
        $this->override_folder = $tab->module . '/' . $this->override_folder;
        
        if (!$tab->module) {
            throw new PrestaShopException('Admin tab '.get_class($this).' is not a module tab');
        }

        $this->module = Module::getInstanceByName($tab->module);
        
        $this->_where = ' AND (is_fee & ' . (int) $this->module->getConstant('IS_FEE') . ')';
    }

    public function renderForm()
    {
        $current_object = $this->loadObject(true);
        $dimension_rule_groups = $this->getDimensionRuleGroupsDisplay($current_object);
        $zipcode_rule_groups = $this->getZipcodeRuleGroupsDisplay($current_object);
        $id_lang = $this->context->language->id;
        
        $this->context->smarty->assign(
            array(
                'adminCartRulesToken' => Tools::getAdminTokenLite('AdminCartRules'),
                'payments' => $current_object->getAssociatedRestrictions('payment', true, false),
                'dimension_rule_groups' => $dimension_rule_groups,
                'dimension_rule_groups_counter' => count($dimension_rule_groups),
                'zipcode_rule_groups' => $zipcode_rule_groups,
                'zipcode_rule_groups_counter' => count($zipcode_rule_groups),
                'zipcode_countries_nb' => count(Country::getCountries($id_lang, true, false, false)),
                'module' => $this->module
            )
        );
        
        $this->addJS($this->module->getPathUri().'views/js/admin.js');
        
        Hook::exec('actionAdminOrderFeesRenderForm', array(
            'controller' => &$this,
            'object' => &$current_object
        ));

        parent::renderForm();

        // Provide fees only on compatibility field
        $cart_rules = $this->context->smarty->getVariable('cart_rules');

        foreach ($cart_rules->value as $type => $data) {
            foreach ($data as $k => $cr) {
                if (!$cr['is_fee']) {
                    unset($cart_rules->value[$type][$k]);
                }
            }
        }

        $this->context->smarty->assign(
            array(
                'title' => array($this->l('Order Fees'), $this->l('Fee')),
                'defaultDateFrom' => $this->module->getConstant('DATE_FROM_MASK'),
                'defaultDateTo' => $this->module->getConstant('DATE_TO_MASK')
            )
        );

        // For translation
        $this->loadTranslationContext();

        $this->content = $this->createTemplate('form.tpl')->fetch();
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_cart_rule'] = array(
                'href' => self::$currentIndex . '&addcart_rule&token=' . $this->token,
                'desc' => $this->l('Add new fee'),
                'icon' => 'process-icon-new'
            );
        }
        
        Hook::exec('action'.$this->controller_name.'InitPageHeaderToolbar', array(
            'controller' => &$this,
            'toolbar' => &$this->page_header_toolbar_btn
        ));
    }

    public function getProductRuleGroupDisplay(
        $product_rule_group_id,
        $product_rule_group_quantity = 1,
        $product_rules = null
    ) {
        // For translation
        $this->loadTranslationContext();

        Context::getContext()->smarty->assign('product_rule_group_id', $product_rule_group_id);
        Context::getContext()->smarty->assign('product_rule_group_quantity', $product_rule_group_quantity);
        Context::getContext()->smarty->assign('product_rules', $product_rules);
        
        return $this->createTemplate('controllers/cart_rules/product_rule_group.tpl')->fetch();
    }

    public function createTemplate($tpl_name)
    {
        // Use override tpl if it exists
        // If view access is denied, we want to use the default template that will be used to display an error
        if ($this->viewAccess() && $this->override_folder) {
            if (file_exists(
                $this->module->getLocalPath() . 'views/templates/admin/orderfees/' . $tpl_name
            )) {
                return $this->context->smarty->createTemplate(
                    $this->module->getLocalPath() . 'views/templates/admin/orderfees/' . $tpl_name,
                    $this->context->smarty
                );
            } elseif (file_exists(
                $this->context->smarty->getTemplateDir(1) . DIRECTORY_SEPARATOR . $this->override_folder . $tpl_name
            )) {
                return $this->context->smarty->createTemplate(
                    $this->override_folder . $tpl_name,
                    $this->context->smarty
                );
            } elseif (file_exists(
                $this->context->smarty->getTemplateDir(0) . 'controllers' . DIRECTORY_SEPARATOR
                . $this->override_folder . $tpl_name
            )) {
                return $this->context->smarty->createTemplate(
                    'controllers' . DIRECTORY_SEPARATOR . $this->override_folder . $tpl_name,
                    $this->context->smarty
                );
            } elseif (file_exists(
                $this->context->smarty->getTemplateDir(0) . 'controllers' . DIRECTORY_SEPARATOR
                . 'cart_rules/' . $tpl_name
            )) {
                return $this->context->smarty->createTemplate(
                    'controllers' . DIRECTORY_SEPARATOR . 'cart_rules/' . $tpl_name,
                    $this->context->smarty
                );
            }
        }

        return $this->context->smarty->createTemplate(
            $this->context->smarty->getTemplateDir(0) . $tpl_name,
            $this->context->smarty
        );
    }
    
    public function processFilter()
    {
        parent::processFilter();
        
        Cache::clean('objectmodel_def_CartRule');
    }
    
    public function postProcess()
    {
        if (Tools::isSubmit('submitAddcart_rule') || Tools::isSubmit('submitAddcart_ruleAndStay')) {
            $current_object = $this->loadObject(true);
            
            Hook::exec('actionAdminOrderFeesValidateBefore', array(
                'controller' => &$this,
                'object' => &$current_object,
                'errors' => &$this->errors
            ));
            
            $is_fee = (int) $this->module->getConstant('CONTEXT_ALL');
            
            if (Tools::getValue('in_shipping')) {
                $is_fee = (int) $this->module->getConstant('IN_SHIPPING');
            }
            
            if (Tools::getValue('is_fee')) {
                $_POST['is_fee'] = (int) $this->module->getConstant('IS_FEE') | $is_fee;
            }
            
            if (!Tools::getValue('payment_restriction')) {
                $_POST['payment_restriction'] = 0;
            }
            
            if (!Tools::getValue('dimension_restriction')) {
                $_POST['dimension_restriction'] = 0;
            }
            
            if (!Tools::getValue('zipcode_restriction')) {
                $_POST['zipcode_restriction'] = 0;
            }
            
            if ((int)Tools::getValue('maximum_amount') < 0) {
                $this->errors[] = Tools::displayError('The maximum amount cannot be lower than zero.');
            }
            
            Hook::exec('actionAdminOrderFeesValidateAfter', array(
                'controller' => &$this,
                'object' => &$current_object,
                'errors' => &$this->errors
            ));
        }
        
        return parent::postProcess();
    }
    
    protected function afterAdd($current_object)
    {
        // Add restrictions for payment
        if (Tools::getValue('payment_restriction')
            && is_array($array = Tools::getValue('payment_select'))
            && count($array)
        ) {
            $values = array();
            
            foreach ($array as $id) {
                $values[] = '(' . (int) $current_object->id . ',' . (int) $id . ')';
            }
            
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_payment` 
                (`id_cart_rule`, `id_module`) VALUES '.implode(',', $values));
        }
        
        // Add dimension rule restrictions
        if (Tools::getValue('dimension_restriction')
            && is_array($ruleGroupArray = Tools::getValue('dimension_rule_group'))
            && count($ruleGroupArray)
        ) {
            foreach ($ruleGroupArray as $ruleGroupId) {
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'cart_rule_dimension_rule_group` (`id_cart_rule`, `base`)
                        VALUES ('.(int)$current_object->id.',
                            "'.pSQL(Tools::getValue('dimension_rule_group_base_'.$ruleGroupId)).'")'
                );
                
                $id_dimension_rule_group = Db::getInstance()->Insert_ID();

                if (is_array($ruleArray = Tools::getValue('dimension_rule_'.$ruleGroupId)) && count($ruleArray)) {
                    foreach ($ruleArray as $ruleId) {
                        Db::getInstance()->execute(
                            'INSERT INTO `'._DB_PREFIX_.'cart_rule_dimension_rule`
                            (`id_dimension_rule_group`, `type`, `operator`, `value`)
                            VALUES ('.(int)$id_dimension_rule_group.',
                            "'.pSQL(Tools::getValue('dimension_rule_'.$ruleGroupId.'_'.$ruleId.'_type'), true).'",
                            "'.pSQL(Tools::getValue('dimension_rule_'.$ruleGroupId.'_'.$ruleId.'_operator'), true).'",
                            "'.pSQL(Tools::getValue('dimension_rule_'.$ruleGroupId.'_'.$ruleId.'_value'), true).'")'
                        );
                    }
                }
            }
        }
        
        // Add zipcode rule restrictions
        if (Tools::getValue('zipcode_restriction')
            && is_array($ruleGroupArray = Tools::getValue('zipcode_rule_group'))
            && count($ruleGroupArray)
        ) {
            foreach ($ruleGroupArray as $ruleGroupId) {
                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.'cart_rule_zipcode_rule_group` (`id_cart_rule`)
                        VALUES ('.(int)$current_object->id.')'
                );
                
                $id_zipcode_rule_group = Db::getInstance()->Insert_ID();

                if (is_array($ruleArray = Tools::getValue('zipcode_rule_'.$ruleGroupId)) && count($ruleArray)) {
                    foreach ($ruleArray as $ruleId) {
                        Db::getInstance()->execute(
                            'INSERT INTO `'._DB_PREFIX_.'cart_rule_zipcode_rule`
                            (`id_zipcode_rule_group`, `type`, `operator`, `value`)
                            VALUES ('.(int)$id_zipcode_rule_group.',
                            "'.pSQL(Tools::getValue('zipcode_rule_'.$ruleGroupId.'_'.$ruleId.'_type'), true).'",
                            "'.pSQL(Tools::getValue('zipcode_rule_'.$ruleGroupId.'_'.$ruleId.'_operator'), true).'",
                            "'.pSQL(Tools::getValue('zipcode_rule_'.$ruleGroupId.'_'.$ruleId.'_value'), true).'")'
                        );
                    }
                }
            }
        }
        
        Hook::exec('actionAdminOrderFeesAfterAdd', array(
            'controller' => &$this,
            'object' => &$current_object
        ));
        
        parent::afterAdd($current_object);
    }
    
    public function processDelete()
    {
        $r = parent::processDelete();
        
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            $id_cart_rule = $object->id;
            
            // Payment restriction
            $r &= Db::getInstance()->delete('cart_rule_payment', '`id_cart_rule` = '.(int)$id_cart_rule);

            // Dimension restriction
            $r &= Db::getInstance()->delete('cart_rule_dimension_rule_group', '`id_cart_rule` = '.(int)$id_cart_rule);
            $r &= Db::getInstance()->delete(
                'cart_rule_dimension_rule',
                'NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'cart_rule_dimension_rule_group`
                    WHERE `'._DB_PREFIX_.'cart_rule_dimension_rule`.`id_dimension_rule_group`
                        = `'._DB_PREFIX_.'cart_rule_dimension_rule_group`.`id_dimension_rule_group`)'
            );

            // Zipcode restriction
            $r &= Db::getInstance()->delete('cart_rule_zipcode_rule_group', '`id_cart_rule` = '.(int)$id_cart_rule);
            $r &= Db::getInstance()->delete(
                'cart_rule_zipcode_rule',
                'NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'cart_rule_zipcode_rule_group`
                    WHERE `'._DB_PREFIX_.'cart_rule_zipcode_rule`.`id_zipcode_rule_group`
                        = `'._DB_PREFIX_.'cart_rule_zipcode_rule_group`.`id_zipcode_rule_group`)'
            );
            
            Hook::exec('actionAdminOrderFeesAfterDelete', array(
                'controller' => &$this,
                'object' => &$object
            ));
        }
        
        return $r;
    }
    
    protected function afterUpdate($current_object)
    {
        $id_cart_rule = Tools::getValue('id_cart_rule');
        
        // Payment restriction
        Db::getInstance()->delete('cart_rule_payment', '`id_cart_rule` = '.(int)$id_cart_rule);
        
        // Dimension restriction
        Db::getInstance()->delete('cart_rule_dimension_rule_group', '`id_cart_rule` = '.(int)$id_cart_rule);
        Db::getInstance()->delete(
            'cart_rule_dimension_rule',
            'NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'cart_rule_dimension_rule_group`
                WHERE `'._DB_PREFIX_.'cart_rule_dimension_rule`.`id_dimension_rule_group`
                    = `'._DB_PREFIX_.'cart_rule_dimension_rule_group`.`id_dimension_rule_group`)'
        );
        
        // Zipcode restriction
        Db::getInstance()->delete('cart_rule_zipcode_rule_group', '`id_cart_rule` = '.(int)$id_cart_rule);
        Db::getInstance()->delete(
            'cart_rule_zipcode_rule',
            'NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'cart_rule_zipcode_rule_group`
                WHERE `'._DB_PREFIX_.'cart_rule_zipcode_rule`.`id_zipcode_rule_group`
                    = `'._DB_PREFIX_.'cart_rule_zipcode_rule_group`.`id_zipcode_rule_group`)'
        );
        
        Hook::exec('actionAdminOrderFeesAfterUpdate', array(
            'controller' => &$this,
            'object' => &$current_object
        ));
        
        parent::afterUpdate($current_object);
    }
    
    public function ajaxProcess()
    {
        if (Tools::isSubmit('newDimensionRule')) {
            die($this->getDimensionRuleDisplay(
                Tools::getValue('dimension_rule_group_id'),
                Tools::getValue('dimension_rule_id'),
                Tools::getValue('dimension_rule_type')
            ));
        }
        
        if (Tools::isSubmit('newDimensionRuleGroup')
            && $dimension_rule_group_id = Tools::getValue('dimension_rule_group_id')
        ) {
            die($this->getDimensionRuleGroupDisplay(
                $dimension_rule_group_id,
                Tools::getValue('dimension_rule_group_base_'.$dimension_rule_group_id, 'product')
            ));
        }
        
        if (Tools::isSubmit('newZipcodeRule')) {
            die($this->getZipcodeRuleDisplay(
                Tools::getValue('zipcode_rule_group_id'),
                Tools::getValue('zipcode_rule_id'),
                Tools::getValue('zipcode_rule_type')
            ));
        }
        
        if (Tools::isSubmit('newZipcodeRuleGroup')
            && $zipcode_rule_group_id = Tools::getValue('zipcode_rule_group_id')
        ) {
            die($this->getZipcodeRuleGroupDisplay($zipcode_rule_group_id));
        }
        
        Hook::exec('actionAdminOrderFeesAjaxProcess', array(
            'controller' => &$this
        ));

        parent::ajaxProcess();
    }
    
    public function getDimensionRuleGroupDisplay(
        $dimension_rule_group_id,
        $dimension_rule_group_base = 'product',
        $dimension_rules = null
    ) {
        // For translation
        $this->loadTranslationContext();

        Context::getContext()->smarty->assign('dimension_rule_group_id', $dimension_rule_group_id);
        Context::getContext()->smarty->assign('dimension_rule_group_base', $dimension_rule_group_base);
        Context::getContext()->smarty->assign('dimension_rules', $dimension_rules);
        
        return $this->createTemplate('dimension_rule_group.tpl')->fetch();
    }
    
    public function getDimensionRuleGroupsDisplay($cart_rule)
    {
        $dimensionRuleGroupsArray = array();
        
        if (Tools::getValue('dimension_restriction')
            && is_array($array = Tools::getValue('dimension_rule_group'))
            && count($array)
        ) {
            $i = 1;
            
            foreach ($array as $ruleGroupId) {
                $dimensionRulesArray = array();
                if (is_array($array = Tools::getValue('dimension_rule_'.$ruleGroupId)) && count($array)) {
                    foreach ($array as $ruleId) {
                        $dimensionRulesArray[] = $this->getDimensionRuleDisplay(
                            $ruleGroupId,
                            $ruleId,
                            Tools::getValue('dimension_rule_'.$ruleGroupId.'_'.$ruleId.'_type'),
                            Tools::getValue('dimension_rule_'.$ruleGroupId.'_'.$ruleId)
                        );
                    }
                }

                $dimensionRuleGroupsArray[] = $this->getDimensionRuleGroupDisplay(
                    $i++,
                    Tools::getValue('dimension_rule_group_base_'.$ruleGroupId),
                    $dimensionRulesArray
                );
            }
        } else {
            $i = 1;
            foreach ($this->module->getDimensionRuleGroups($cart_rule) as $dimensionRuleGroup) {
                $j = 1;
                $dimensionRulesDisplay = array();
                
                foreach ($dimensionRuleGroup['dimension_rules'] as $dimensionRule) {
                    $dimensionRulesDisplay[] = $this->getDimensionRuleDisplay(
                        $i,
                        $j++,
                        $dimensionRule['type'],
                        $dimensionRule['operator'],
                        $dimensionRule['value']
                    );
                }
                
                $dimensionRuleGroupsArray[] = $this->getDimensionRuleGroupDisplay(
                    $i++,
                    $dimensionRuleGroup['base'],
                    $dimensionRulesDisplay
                );
            }
        }
        return $dimensionRuleGroupsArray;
    }
    
    public function getDimensionRuleDisplay(
        $dimension_rule_group_id,
        $dimension_rule_id,
        $dimension_rule_type,
        $dimension_rule_operator = '=',
        $dimension_rule_value = ''
    ) {
        // For translation
        $this->loadTranslationContext();


        $this->context->smarty->assign(
            array(
                'dimension_rule_group_id' => (int)$dimension_rule_group_id,
                'dimension_rule_id' => (int)$dimension_rule_id,
                'dimension_rule_type' => $dimension_rule_type,
                'id_lang' => (int)$this->context->language->id,
                'operator' => $dimension_rule_operator,
                'value' => $dimension_rule_value,
                'ps_dimension_unit' => Configuration::get('PS_DIMENSION_UNIT'),
                'ps_weight_unit' => Configuration::get('PS_WEIGHT_UNIT')
            )
        );

        if (Tools::getValue('dimension_restriction')) {
            $this->context->smarty->assign(
                array(
                    'operator' => Tools::getValue(
                        'dimension_rule_'.$dimension_rule_group_id.'_'.$dimension_rule_id.'_operator'
                    ),
                    'value' => Tools::getValue(
                        'dimension_rule_'.$dimension_rule_group_id.'_'.$dimension_rule_id.'_value'
                    )
                )
            );
        }

        return $this->createTemplate('dimension_rule.tpl')->fetch();
    }
    
    public function getZipcodeRuleGroupDisplay(
        $zipcode_rule_group_id,
        $zipcode_rules = null
    ) {
        // For translation
        $this->loadTranslationContext();

        Context::getContext()->smarty->assign('zipcode_rule_group_id', $zipcode_rule_group_id);
        Context::getContext()->smarty->assign(
            'zipcode_countries',
            Country::getCountries($this->context->language->id, true, false, false)
        );
        Context::getContext()->smarty->assign('zipcode_rules', $zipcode_rules);
        
        return $this->createTemplate('zipcode_rule_group.tpl')->fetch();
    }
    
    public function getZipcodeRuleGroupsDisplay($cart_rule)
    {
        $zipcodeRuleGroupsArray = array();
        
        if (Tools::getValue('zipcode_restriction')
            && is_array($array = Tools::getValue('zipcode_rule_group'))
            && count($array)
        ) {
            $i = 1;
            
            foreach ($array as $ruleGroupId) {
                $zipcodeRulesArray = array();
                if (is_array($array = Tools::getValue('zipcode_rule_'.$ruleGroupId)) && count($array)) {
                    foreach ($array as $ruleId) {
                        $zipcodeRulesArray[] = $this->getZipcodeRuleDisplay(
                            $ruleGroupId,
                            $ruleId,
                            Tools::getValue('zipcode_rule_'.$ruleGroupId.'_'.$ruleId.'_type'),
                            Tools::getValue('zipcode_rule_'.$ruleGroupId.'_'.$ruleId)
                        );
                    }
                }

                $zipcodeRuleGroupsArray[] = $this->getZipcodeRuleGroupDisplay(
                    $i++,
                    $zipcodeRulesArray
                );
            }
        } else {
            $i = 1;
            foreach ($this->module->getZipcodeRuleGroups($cart_rule) as $zipcodeRuleGroup) {
                $j = 1;
                $zipcodeRulesDisplay = array();
                
                foreach ($zipcodeRuleGroup['zipcode_rules'] as $zipcodeRule) {
                    $zipcodeRulesDisplay[] = $this->getZipcodeRuleDisplay(
                        $i,
                        $j++,
                        $zipcodeRule['type'],
                        $zipcodeRule['operator'],
                        $zipcodeRule['value']
                    );
                }
                
                $zipcodeRuleGroupsArray[] = $this->getZipcodeRuleGroupDisplay(
                    $i++,
                    $zipcodeRulesDisplay
                );
            }
        }
        return $zipcodeRuleGroupsArray;
    }
    
    public function getZipcodeRuleDisplay(
        $zipcode_rule_group_id,
        $zipcode_rule_id,
        $zipcode_rule_type,
        $zipcode_rule_operator = '=',
        $zipcode_rule_value = ''
    ) {
        // For translation
        $this->loadTranslationContext();


        $this->context->smarty->assign(
            array(
                'zipcode_rule_group_id' => (int)$zipcode_rule_group_id,
                'zipcode_rule_id' => (int)$zipcode_rule_id,
                'zipcode_rule_type' => $zipcode_rule_type,
                'id_lang' => (int)$this->context->language->id,
                'operator' => $zipcode_rule_operator,
                'value' => $zipcode_rule_value
            )
        );

        if (Tools::getValue('zipcode_restriction')) {
            $this->context->smarty->assign(
                array(
                    'operator' => Tools::getValue(
                        'zipcode_rule_'.$zipcode_rule_group_id.'_'.$zipcode_rule_id.'_operator'
                    ),
                    'value' => Tools::getValue(
                        'zipcode_rule_'.$zipcode_rule_group_id.'_'.$zipcode_rule_id.'_value'
                    )
                )
            );
        }

        return $this->createTemplate('zipcode_rule.tpl')->fetch();
    }
    
    public function getProductRuleDisplay(
        $product_rule_group_id,
        $product_rule_id,
        $product_rule_type,
        $selected = array()
    ) {
        $this->loadTranslationContext();
        
        Context::getContext()->smarty->assign(
            array(
                'product_rule_group_id' => (int)$product_rule_group_id,
                'product_rule_id' => (int)$product_rule_id,
                'product_rule_type' => $product_rule_type,
            )
        );

        switch ($product_rule_type) {
            case 'attributes':
                $attributes = array('selected' => array(), 'unselected' => array());
                $results = Db::getInstance()->executeS('
				SELECT CONCAT(agl.name, " - ", al.name) as name, a.id_attribute as id
				FROM '._DB_PREFIX_.'attribute_group_lang agl
				LEFT JOIN '._DB_PREFIX_.'attribute a ON a.id_attribute_group = agl.id_attribute_group
				LEFT JOIN '._DB_PREFIX_.'attribute_lang al
                    ON (a.id_attribute = al.id_attribute AND al.id_lang = '.(int)Context::getContext()->language->id.')
				WHERE agl.id_lang = '.(int)Context::getContext()->language->id.'
				ORDER BY agl.name, al.name');
                
                foreach ($results as $row) {
                    $attributes[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
                }
                
                Context::getContext()->smarty->assign('product_rule_itemlist', $attributes);
                $choose_content = $this->createTemplate('controllers/cart_rules/product_rule_itemlist.tpl')->fetch();
                Context::getContext()->smarty->assign('product_rule_choose_content', $choose_content);
                
                break;
            case 'products':
                $display_sku = (bool)Configuration::get('MS_ORDERFEES_CONDITIONS_DISPLAY_SKU');
                $products = array('selected' => array(), 'unselected' => array());
                $results = Db::getInstance()->executeS('
				SELECT DISTINCT ' . ($display_sku ? 'CONCAT(p.reference, " - ", name) AS name' : 'name') . ',
                    p.id_product as id
				FROM '._DB_PREFIX_.'product p
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)Context::getContext()->language->id.Shop::addSqlRestrictionOnLang('pl').')
				'.Shop::addSqlAssociation('product', 'p').'
				WHERE id_lang = '.(int)Context::getContext()->language->id.'
				ORDER BY name');
                
                foreach ($results as $row) {
                    $products[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
                }
                
                Context::getContext()->smarty->assign('product_rule_itemlist', $products);
                $choose_content = $this->createTemplate('product_rule_itemlist.tpl')->fetch();
                Context::getContext()->smarty->assign('product_rule_choose_content', $choose_content);
                
                break;
            case 'manufacturers':
                $products = array('selected' => array(), 'unselected' => array());
                $results = Db::getInstance()->executeS('
				SELECT name, id_manufacturer as id
				FROM '._DB_PREFIX_.'manufacturer
				ORDER BY name');
                
                foreach ($results as $row) {
                    $products[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
                }
                
                Context::getContext()->smarty->assign('product_rule_itemlist', $products);
                $choose_content = $this->createTemplate('product_rule_itemlist.tpl')->fetch();
                Context::getContext()->smarty->assign('product_rule_choose_content', $choose_content);
                
                break;
            case 'suppliers':
                $products = array('selected' => array(), 'unselected' => array());
                $results = Db::getInstance()->executeS('
				SELECT name, id_supplier as id
				FROM '._DB_PREFIX_.'supplier
				ORDER BY name');
                
                foreach ($results as $row) {
                    $products[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
                }
                
                Context::getContext()->smarty->assign('product_rule_itemlist', $products);
                $choose_content = $this->createTemplate('product_rule_itemlist.tpl')->fetch();
                Context::getContext()->smarty->assign('product_rule_choose_content', $choose_content);
                
                break;
            case 'categories':
                $categories = array('selected' => array(), 'unselected' => array());
                $results = Db::getInstance()->executeS('
				SELECT DISTINCT name, c.id_category as id
				FROM '._DB_PREFIX_.'category c
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (c.`id_category` = cl.`id_category`
					AND cl.`id_lang` = '.(int)Context::getContext()->language->id.Shop::addSqlRestrictionOnLang('cl').')
				'.Shop::addSqlAssociation('category', 'c').'
				WHERE id_lang = '.(int)Context::getContext()->language->id.'
				ORDER BY name');
                
                foreach ($results as $row) {
                    $categories[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
                }
                
                Context::getContext()->smarty->assign('product_rule_itemlist', $categories);
                $choose_content = $this->createTemplate('product_rule_itemlist.tpl')->fetch();
                Context::getContext()->smarty->assign('product_rule_choose_content', $choose_content);
                
                break;
            default:
                Context::getContext()->smarty->assign(
                    'product_rule_itemlist',
                    array('selected' => array(), 'unselected' => array())
                );
                
                Context::getContext()->smarty->assign('product_rule_choose_content', '');
        }

        return $this->createTemplate('product_rule.tpl')->fetch();
    }
    
    public function getCurrentDisplay()
    {
        return $this->display;
    }
    
    protected function l($string, $class = 'AdminCartRules', $addslashes = false, $htmlentities = true)
    {
        return parent::l($string, $class, $addslashes, $htmlentities);
    }

    protected function loadTranslationContext()
    {
        $ctx = Context::getContext();
        $ctx->controller = MotionSeedModule::cast('AdminCartRulesController', $ctx->controller);
    }
}
