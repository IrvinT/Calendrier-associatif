<?php
/**
 *  Order Fees
 *
 *  @author    motionSeed <ecommerce@motionseed.com>
 *  @copyright 2016 motionSeed. All rights reserved.
 *  @license   https://www.motionseed.com/license
 */

class PaymentModule extends PaymentModuleCore
{
    public function formatProductAndVoucherForEmail($content)
    {
        $cart_rules = $this->context->cart->getCartRules();

        foreach ($cart_rules as $cart_rule) {
            if ($cart_rule['obj']->is_fee > 0) {
                $pattern = '#' . Tools::displayError('Voucher name:') . ' '
                    . preg_quote($cart_rule['obj']->name) . '(.*)(555454">)(\s*)(?:-)?(.*?)(</font>)#s';
                $replace = $cart_rule['obj']->name . '$1$2$3$4$5';

                $content = preg_replace($pattern, $replace, $content);
            }
        }

        return $content;
    }

    protected function getEmailTemplateContent($template_name, $mail_type, $var)
    {
        if (strpos($template_name, 'order_conf_cart_rules') === 0) {
            $cart_rules = $this->context->cart->getCartRules();

            foreach ($cart_rules as $cart_rule) {
                if ($cart_rule['obj']->is_fee > 0) {
                    foreach ($var as &$v) {
                        if ($v['voucher_name'] == $cart_rule['obj']->name) {
                            $v['voucher_reduction'] = str_replace('-', '', $v['voucher_reduction']);
                        }
                    }
                }
            }
        }

        return parent::getEmailTemplateContent($template_name, $mail_type, $var);
    }
    
    public function display($file, $template, $cache_id = null, $compile_id = null)
    {
        $content = parent::display($file, $template, $cache_id, $compile_id);
        
        if (strpos($template, 'payment.tpl') !== false) {
            $items = Db::getInstance()->executeS(
                'SELECT cr.id_cart_rule
                FROM '._DB_PREFIX_.'cart_rule cr
                INNER JOIN '._DB_PREFIX_.'cart_rule_payment crp
                    ON cr.id_cart_rule = crp.id_cart_rule 
                        AND cr.is_fee > 0 
                        AND cr.payment_restriction = 1
                INNER JOIN '._DB_PREFIX_.'module m
                    ON m.id_module = crp.id_module
                        AND m.active = 1
                        AND m.name = \'' . pSQL($this->name) . '\''
            );

            if ($items) {
                $total = 0;
                
                foreach ($items as $item) {
                    $cart_rule = new CartRule($item['id_cart_rule']);

                    $total += abs($cart_rule->getContextualValue(
                        true,
                        $this->context,
                        CartRule::FILTER_ACTION_ALL_NOCAP
                    ));
                }
                
                $module = Module::getInstanceByName('orderfees');
                
                $this->context->smarty->assign(array(
                    'total' => $total
                ));
                
                $content = str_replace(
                    '</a>',
                    $module->display('orderfees', 'payment.tpl') . '</a>',
                    $content
                );
            }
        }
        
        return $content;
    }
}
