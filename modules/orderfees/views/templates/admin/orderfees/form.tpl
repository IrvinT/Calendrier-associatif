{*
* Order Fees
*
* @author    motionSeed <ecommerce@motionseed.com>
* @copyright 2016 motionSeed. All rights reserved.
* @license   https://www.motionseed.com/license
*}

<div class="panel">
    <h3><i class="icon-folder-open"></i> {l s='Fee' mod='orderfees'}</h3>
    
    {hook h="displayOrderFeesFormBefore" module=$module controller=$currentTab object=$currentObject}
    
    <div class="productTabs">
        <ul class="tab nav nav-tabs">
            <li class="tab-row">
                <a class="tab-page" id="cart_rule_link_informations" href="javascript:displayCartRuleTab('informations');"><i class="icon-info"></i> {l s='Information' mod='orderfees'}</a>
            </li>
            <li class="tab-row">
                <a class="tab-page" id="cart_rule_link_conditions" href="javascript:displayCartRuleTab('conditions');"><i class="icon-random"></i> {l s='Conditions' mod='orderfees'}</a>
            </li>
            <li class="tab-row">
                <a class="tab-page" id="cart_rule_link_actions" href="javascript:displayCartRuleTab('actions');"><i class="icon-wrench"></i> {l s='Actions' mod='orderfees'}</a>
            </li>
        </ul>
    </div>

    <form action="{$currentIndex|escape:'html':'UTF-8'}&token={$currentToken|escape:'html':'UTF-8'}&addcart_rule" id="cart_rule_form" method="post" class="form-horizontal">
        {if $currentObject->id}<input type="hidden" name="id_cart_rule" value="{$currentObject->id|intval}" />{/if}
        <input type="hidden" id="currentFormTab" name="currentFormTab" value="informations" />
        <input type="hidden" id="is_fee" name="is_fee" value="{$module->getConstant('IS_FEE')|intval}" />
        <input type="hidden" name="partial_use" value="0" />
        <div id="cart_rule_informations" class="panel cart_rule_tab">
            {include './tabs/informations.tpl'}
        </div>
        <div id="cart_rule_conditions" class="panel cart_rule_tab">
            {include './tabs/conditions.tpl'}
        </div>
        <div id="cart_rule_actions" class="panel cart_rule_tab">
            {include './tabs/actions.tpl'}
        </div>
        
        {hook h="displayOrderFeesFormAfter" module=$module controller=$currentTab object=$currentObject}
        
        <div class="separation"></div>
        <div style="text-align:center">
            <input type="submit" value="{l s='Save' mod='orderfees'}" class="button" name="submitAddcart_rule" id="{$table|escape:'quotes':'UTF-8'}_form_submit_btn" />
            <!--<input type="submit" value="{l s='Save and stay' mod='orderfees'}" class="button" name="submitAddcart_ruleAndStay" id="" />-->
        </div>
    </form>
    <script type="text/javascript">
        var product_rule_groups_counter = {if isset($product_rule_groups_counter)}{$product_rule_groups_counter|intval}{else}0{/if};
        var product_rule_counters = new Array();

        var dimension_rule_groups_counter = {if isset($dimension_rule_groups_counter)}{$dimension_rule_groups_counter|intval}{else}0{/if};
        var dimension_rule_counters = dimension_rule_counters || new Array();
        
        var zipcode_rule_groups_counter = {if isset($zipcode_rule_groups_counter)}{$zipcode_rule_groups_counter|intval}{else}0{/if};
        var zipcode_rule_counters = zipcode_rule_counters || new Array();

        var currentToken = '{$adminCartRulesToken|escape:'quotes':'UTF-8'}';
        var currentFormTab = '{if isset($smarty.post.currentFormTab)}{$smarty.post.currentFormTab|escape:'quotes':'UTF-8'}{else}informations{/if}';
        var currentText = '{l s='Now' js=1 mod='orderfees'}';
        var closeText = '{l s='Done' js=1 mod='orderfees'}';
        var timeOnlyTitle = '{l s='Choose Time' js=1 mod='orderfees'}';
        var timeText = '{l s='Time' js=1 mod='orderfees'}';
        var hourText = '{l s='Hour' js=1 mod='orderfees'}';
        var minuteText = '{l s='Minute' js=1 mod='orderfees'}';

        var languages = new Array();
        {foreach from=$languages item=language key=k}
                languages[{$k|intval}] = {
                    id_lang: {$language.id_lang|intval},
                    iso_code: '{$language.iso_code|escape:'quotes':'UTF-8'}',
                    name: '{$language.name|escape:'quotes':'UTF-8'}'
                };
        {/foreach}
            
        displayFlags(languages, {$id_lang_default|intval});
    </script>
    <script type="text/javascript" src="themes/default/template/controllers/cart_rules/form.js"></script>
    {include file="footer_toolbar.tpl"}
</div>