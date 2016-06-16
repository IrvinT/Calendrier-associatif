{*
* Order Fees
*
* @author    motionSeed <ecommerce@motionseed.com>
* @copyright 2016 motionSeed. All rights reserved.
* @license   https://www.motionseed.com/license
*}
<tr id="zipcode_rule_group_{$zipcode_rule_group_id|intval}_tr">
    <input type="hidden" name="zipcode_rule_group[]" value="{$zipcode_rule_group_id|intval}" />
    
    <td>
        <a class="btn btn-default" href="javascript:removeZipcodeRuleGroup({$zipcode_rule_group_id|intval});">
            <i class="icon-remove text-danger"></i>
        </a>
    </td>
    <td>      
        <div class="form-group">
            <label class="control-label col-lg-4">{l s='Add a rule concerning' mod='orderfees'}</label>
            <div class="col-lg-4">
                <select class="form-control" id="zipcode_rule_type_{$zipcode_rule_group_id|intval}">
                    <option value="">{l s='-- Choose --' mod='orderfees'}</option>
                    {foreach $zipcode_countries as $zipcode_country}
                        <option value="{$zipcode_country.id_country|intval}">{$zipcode_country.country|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>

            <div class="col-lg-2">
                <a class="btn btn-default" href="javascript:addZipcodeRule({$zipcode_rule_group_id|intval});">
                    <i class="icon-plus-sign"></i>
                    {l s='Add' mod='orderfees'}
                </a>
            </div>

        </div>
        
        <label class="control-label">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='You can define multiple values by separating them with a comma.' mod='orderfees'}">
                {l s='The zipcode(s) are matching one of these:' mod='orderfees'}
            </span>
        </label>
        
        <table id="zipcode_rule_table_{$zipcode_rule_group_id|intval}" class="table table-bordered">
            {if isset($zipcode_rules) && $zipcode_rules|@count}
                {foreach from=$zipcode_rules item='zipcode_rule'}
                    {$zipcode_rule}
                {/foreach}
            {/if}
        </table>
    </td>
</tr>

<script type="text/javascript">
    var zipcode_rule_counters = zipcode_rule_counters || new Array();
    
    zipcode_rule_counters[{$zipcode_rule_group_id|intval}] = {count($zipcode_rules)|intval};
    
    $('.label-tooltip', $('#zipcode_rule_group_{$zipcode_rule_group_id|intval}_tr')).tooltip();
</script>