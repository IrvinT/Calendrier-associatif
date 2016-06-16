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
