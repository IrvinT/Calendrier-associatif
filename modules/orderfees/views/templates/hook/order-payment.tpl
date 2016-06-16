{*
* Order Fees
*
* @author    motionSeed <ecommerce@motionseed.com>
* @copyright 2016 motionSeed. All rights reserved.
* @license   https://www.motionseed.com/license
*}
{if count($fees)}
    <tbody>
    {foreach from=$fees item=fee name=feeLoop}
        <tr class="cart_discount {if $smarty.foreach.feeLoop.last}last_item{elseif $smarty.foreach.feeLoop.first}first_item{else}item{/if}" id="cart_discount_{$fee.id_discount|intval}">
            <td class="cart_discount_name" colspan="3">{$fee.name|escape:'html':'UTF-8'}</td>
            <td class="cart_discount_price">
                <span class="price-discount">
                    {if !$priceDisplay}
                        {displayPrice price=$fee["obj"]->unit_value_real*-1}
                    {else}
                        {displayPrice price=$fee["obj"]->unit_value_tax_exc*-1}}
                    {/if}
                </span>
            </td>
            <td class="cart_discount_delete">{$fee["obj"]->quantity|intval}</td>
            <td class="cart_discount_price">
                <span class="price-discount">
                    {if !$priceDisplay}
                        {displayPrice price=$fee.value_real*-1}
                    {else}
                        {displayPrice price=$fee.value_tax_exc*-1}
                    {/if}
                </span>
            </td>
        </tr>
    {/foreach}
    </tbody>
{/if}
