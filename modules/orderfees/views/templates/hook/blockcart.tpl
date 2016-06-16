{*
* Order Fees
*
* @author    motionSeed <ecommerce@motionseed.com>
* @copyright 2016 motionSeed. All rights reserved.
* @license   https://www.motionseed.com/license
*}
<table class="vouchers fees{if $fees|@count == 0} unvisible{/if}">
        {foreach from=$fees item=fee}
            <tr class="bloc_cart_voucher" data-id="bloc_cart_voucher_{$fee.id_discount|intval}">
                <td class="quantity">{$fee["obj"]->quantity|intval}x</td>
                <td class="name" title="{$fee.description|escape:'html':'UTF-8'}">
                    {$fee.name|escape:'html':'UTF-8'}
                </td>
                <td class="price">
                    {if $priceDisplay == 1}{convertPrice price=$fee.value_tax_exc*-1}{else}{convertPrice price=$fee.value_real*-1}{/if}
                </td>
                <td class="delete">
                </td>
            </tr>
        {/foreach}
</table>
