{*
* Order Fees
*
* @author    motionSeed <ecommerce@motionseed.com>
* @copyright 2016 motionSeed. All rights reserved.
* @license   https://www.motionseed.com/license
*}
{if count($fees) > 0}
    <script id="fees" type="text/template">
        {foreach from=$fees item='fee'}
            <tr>
                <td colspan="2">{$fee.name|escape:'html':'UTF-8'}</td>
                <td class="text-right">{displayWtPriceWithCurrency price=$fee.value_real*-1 currency=$currency}</td>
                <td class="text-center">{$fee['obj']->quantity|intval}</td>
                <td class="text-center"></td>
                <td class="text-right">{displayWtPriceWithCurrency price=$fee.value_real*-1 currency=$currency}</td>
            </tr>
        {/foreach}
    </script>
    
    <script type="text/javascript">        
        $('#orderProducts tbody tr')
                .filter(function (id, el) {
                    var children = $(el).children().length;
                    return children > 3;
                })
                .last()
                .after($('#fees').html());
    </script>
{/if}