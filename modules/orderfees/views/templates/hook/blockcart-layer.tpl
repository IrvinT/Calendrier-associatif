{*
* Order Fees
*
* @author    motionSeed <ecommerce@motionseed.com>
* @copyright 2016 motionSeed. All rights reserved.
* @license   https://www.motionseed.com/license
*}
<div id="ajax_block_fees_overlay">
    {foreach from=$fees item=fee}
        <div class="layer_cart_row">
            <strong class="dark">
                {$fee.name|escape:'html':'UTF-8'}
            </strong>
            <span class="ajax_block_fees">
                {convertPrice price=$fee.value_real*-1}
            </span>
        </div>
    {/foreach}
</div>
