{*
* Order Fees
*
* @author    motionSeed <ecommerce@motionseed.com>
* @copyright 2016 motionSeed. All rights reserved.
* @license   https://www.motionseed.com/license
*}
{foreach $fees as $fee}
    <tr style="line-height:5px;">
            <td style="text-align: right; font-weight: bold">
                {$fee.name|escape:'html':'UTF-8'}
            </td>
            <td style="width: 15%; text-align: right;">
                {displayPrice currency=$order->id_currency price=$fee.value}
            </td>
    </tr>
{/foreach}