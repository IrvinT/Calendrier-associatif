{*
* Order Fees
*
* @author    motionSeed <ecommerce@motionseed.com>
* @copyright 2016 motionSeed. All rights reserved.
* @license   https://www.motionseed.com/license
*}
{assign var="shipping_discount_tax_incl" value="0"}
{foreach $fees as $fee}
{cycle values='#FFF,#DDD' assign=bgcolor}
        <tr class="discount">
                <td class="white right" colspan="5">
                        {$fee.name|escape:'html':'UTF-8'}
                </td>
                <td class="right white">
                    {if $tax_excluded_display}
                        {displayPrice price=$fee.value_tax_excl}
                    {else}
                        {displayPrice price=$fee.value}
                    {/if}
                </td>
        </tr>
{/foreach}