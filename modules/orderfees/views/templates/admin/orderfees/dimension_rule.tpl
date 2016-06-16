{*
* Order Fees
*
* @author    motionSeed <ecommerce@motionseed.com>
* @copyright 2016 motionSeed. All rights reserved.
* @license   https://www.motionseed.com/license
*}
<tr id="dimension_rule_{$dimension_rule_group_id|intval}_{$dimension_rule_id|intval}_tr">
	<td>
		<input type="hidden" name="dimension_rule_{$dimension_rule_group_id|intval}[]" value="{$dimension_rule_id|intval}" />
		<input type="hidden" name="dimension_rule_{$dimension_rule_group_id|intval}_{$dimension_rule_id|intval}_type" value="{$dimension_rule_type|escape:'html':'UTF-8'}" />
		{if $dimension_rule_type == 'width'}
                    {l s='Width' mod='orderfees'}
                {elseif $dimension_rule_type == 'height'}
                    {l s='Height' mod='orderfees'}
                {elseif $dimension_rule_type == 'depth'}
                    {l s='Depth' mod='orderfees'}
                {elseif $dimension_rule_type == 'weight'}
                    {l s='Weight' mod='orderfees'}
                {elseif $dimension_rule_type == 'volume'}
                    {l s='Volume' mod='orderfees'}
                {/if}
	</td>
        <td>
		<select class="form-control" id="dimension_rule_{$dimension_rule_group_id|intval}_{$dimension_rule_id|intval}_operator" name="dimension_rule_{$dimension_rule_group_id|intval}_{$dimension_rule_id|intval}_operator">
                    <option value="=">{l s='=' mod='orderfees'}</option>
                    <option value=">">{l s='>' mod='orderfees'}</option>
                    <option value="<">{l s='<' mod='orderfees'}</option>
                    <option value=">=">{l s='>=' mod='orderfees'}</option>
                    <option value="<=">{l s='<=' mod='orderfees'}</option>
                    <option value="!=">{l s='!=' mod='orderfees'}</option>
                </select>
	</td>
	<td>
            <div class="input-group col-lg-12">
                <span class="input-group-addon">
                    {if $dimension_rule_type == 'width' or $dimension_rule_type == 'height' or $dimension_rule_type == 'depth'}
                        {$ps_dimension_unit|escape:'html':'UTF-8'}
                    {elseif $dimension_rule_type == 'weight'}
                        {$ps_weight_unit|escape:'html':'UTF-8'}
                    {elseif $dimension_rule_type == 'volume'}
                        {$ps_dimension_unit|escape:'html':'UTF-8'}3
                    {/if}
                </span>
		<input type="text" name="dimension_rule_{$dimension_rule_group_id|intval}_{$dimension_rule_id|intval}_value" value="{if isset($value)}{$value|escape:'html':'UTF-8'}{/if}" />
            </div>
        </td>
	<td class="text-right">
		<a class="btn btn-default" href="javascript:removeDimensionRule({$dimension_rule_group_id|intval}, {$dimension_rule_id|intval});">
			<i class="icon-remove"></i>
		</a>
	</td>
</tr>

<script type="text/javascript">
	$('#dimension_rule_{$dimension_rule_group_id|intval}_{$dimension_rule_id|intval}_operator').val('{$operator|escape:'html':'UTF-8'}');
</script>