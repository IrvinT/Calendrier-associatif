<?php
/**
 *  Order Fees
 *
 *  @author    motionSeed <ecommerce@motionseed.com>
 *  @copyright 2016 motionSeed. All rights reserved.
 *  @license   https://www.motionseed.com/license
 */
class AdminOrdersController extends AdminOrdersControllerCore
{
    /*
    * module: orderfees
    * date: 2016-06-14 11:46:29
    * version: 1.7.1
    */
    public function setHelperDisplay(Helper $helper)
    {
        if (isset($this->tpl_view_vars['order'])) {
            Hook::exec('actionAdminOrdersControllerHelperDisplay', array(
                'controller' => &$this,
                'helper' => &$helper
            ));
        }
        
        parent::setHelperDisplay($helper);
    }
}
