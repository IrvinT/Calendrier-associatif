{*
* Order Fees
*
* @author    motionSeed <ecommerce@motionseed.com>
* @copyright 2016 motionSeed. All rights reserved.
* @license   https://www.motionseed.com/license
*}
<tr id="dimension_rule_group_{$dimension_rule_group_id|intval}_tr">
    <input type="hidden" name="dimension_rule_group[]" value="{$dimension_rule_group_id|intval}" />
    
    <td>
        <a class="btn btn-default" href="javascript:removeDimensionRuleGroup({$dimension_rule_group_id|intval});">
            <i class="icon-remove text-danger"></i>
        </a>
    </td>
    <td>
        <div class="form-group">
            <label class="control-label col-lg-4">{l s='Basis for the calculation' mod='orderfees'}</label>
            
            <div class="col-lg-4">
                <select class="form-control" id="dimension_rule_group_base_{$dimension_rule_group_id|intval}" name="dimension_rule_group_base_{$dimension_rule_group_id|intval}">
                    <option value="product">{l s='Per product' mod='orderfees'}</option>
                    <option value="all">{l s='Set of all products' mod='orderfees'}</option>
                </select>
            </div>
        </div>
                
        <div class="form-group">
            <label class="control-label col-lg-4">{l s='Add a rule concerning' mod='orderfees'}</label>
            <div class="col-lg-4">
                <select class="form-control" id="dimension_rule_type_{$dimension_rule_group_id|intval}">
                    <option value="">{l s='-- Choose --' mod='orderfees'}</option>
                    <option value="width">{l s='Width' mod='orderfees'}</option>
                    <option value="height">{l s='Height' mod='orderfees'}</option>
                    <option value="depth">{l s='Depth' mod='orderfees'}</option>
                    <option value="weight">{l s='Weight' mod='orderfees'}</option>
                    <option value="volume">{l s='Volume' mod='orderfees'}</option>
                </select>
            </div>

            <div class="col-lg-2">
                <a class="btn btn-default" href="javascript:addDimensionRule({$dimension_rule_group_id|intval});">
                    <i class="icon-plus-sign"></i>
                    {l s='Add' mod='orderfees'}
                </a>
            </div>

        </div>
        
        {l s='The dimension(s) are matching one of these:' mod='orderfees'}
        <table id="dimension_rule_table_{$dimension_rule_group_id|intval}" class="table table-bordered">
            {if isset($dimension_rules) && $dimension_rules|@count}
                {foreach from=$dimension_rules item='dimension_rule'}
                    {$dimension_rule}
                {/foreach}
            {/if}
        </table>

    </td>
</tr>

<script type="text/javascript">
    var dimension_rule_counters = dimension_rule_counters || new Array();
    
    dimension_rule_counters[{$dimension_rule_group_id|intval}] = {count($dimension_rules)|intval};
    
    $('#dimension_rule_group_base_{$dimension_rule_group_id|intval}').val('{$dimension_rule_group_base|escape:'html':'UTF-8'}');
</script>