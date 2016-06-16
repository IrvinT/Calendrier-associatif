{*
* Order Fees
*
* @author    motionSeed <ecommerce@motionseed.com>
* @copyright 2016 motionSeed. All rights reserved.
* @license   https://www.motionseed.com/license
*}
<tbody>
    {foreach $fees as $fee}
        <tr class="cart_discount {if $fee@last}last_item{elseif $fee@first}first_item{else}item{/if}" id="cart_discount_{$fee.id_discount|intval}">
            <td class="cart_discount_name" colspan="{if $PS_STOCK_MANAGEMENT}3{else}2{/if}">{$fee.name|escape:'html':'UTF-8'}</td>
            <td class="cart_discount_price">
                <span class="price-discount">
                {if !$priceDisplay}{displayPrice price=$fee["obj"]->unit_value_real*-1}{else}{displayPrice price=$fee["obj"]->unit_value_tax_exc*-1}{/if}
                </span>
            </td>
            <td class="cart_discount_delete">{$fee["obj"]->quantity|intval}</td>
            <td class="price_discount_del text-center">
            </td>
            <td class="cart_discount_price">
                <span class="price-discount price">
                    {if !$priceDisplay}{displayPrice price=$fee.value_real*-1}{else}{displayPrice price=$fee.value_tax_exc*-1}{/if}
                </span>
            </td>
        </tr>
    {/foreach}
</tbody>
