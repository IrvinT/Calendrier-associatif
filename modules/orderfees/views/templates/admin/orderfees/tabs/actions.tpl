{*
* Order Fees
*
* @author    motionSeed <ecommerce@motionseed.com>
* @copyright 2016 motionSeed. All rights reserved.
* @license   https://www.motionseed.com/license
*}

{hook h="displayOrderFeesFormActionsBefore" module=$module controller=$currentTab object=$currentObject}

<div class="form-group">
    <label class="control-label col-lg-3">{l s='Apply a fee' mod='orderfees'}</label>
    <div class="col-lg-9">
        <div class="radio">
            <label for="apply_discount_percent">
                <input type="radio" name="apply_discount" id="apply_discount_percent" value="percent" {if $currentTab->getFieldValue($currentObject, 'reduction_percent')|intval}checked="checked"{/if} />
                {l s='Percent (%)' mod='orderfees'}
            </label>
        </div>
        <div class="radio">
            <label for="apply_discount_amount">
                <input type="radio" name="apply_discount" id="apply_discount_amount" value="amount"  {if $currentTab->getFieldValue($currentObject, 'reduction_amount')|intval}checked="checked"{/if} />
                {l s='Amount' mod='orderfees'}
            </label>
        </div>
        <div class="radio">
            <label for="apply_discount_off">
                <input type="radio" name="apply_discount" id="apply_discount_off" value="off"  {if !$currentTab->getFieldValue($currentObject, 'reduction_amount')|intval && !$currentTab->getFieldValue($currentObject, 'reduction_percent')|intval}checked="checked"{/if} />
                <i class="icon-ban-circle color_danger"></i> {l s='None' mod='orderfees'}
            </label>
        </div>
    </div>
</div>

<div id="apply_discount_percent_div" class="form-group">
    <label class="control-label col-lg-3">{l s='Value' mod='orderfees'}</label>
    <div class="input-group col-lg-2">
        <span class="input-group-addon">%</span>
        <input type="text" id="reduction_percent" class="input-mini" name="reduction_percent" value="{$currentTab->getFieldValue($currentObject, 'reduction_percent')|floatval}" />
    </div>
    <div class="alert alert-danger col-lg-offset-3 col-lg-3" style="margin-bottom: 0px; margin-top: 10px;"> {l s='Does not apply to the shipping costs' mod='orderfees'}</div>
</div>

<div id="apply_discount_amount_div" class="form-group">
    <label class="control-label col-lg-3">{l s='Amount' mod='orderfees'}</label>
    <div class="col-lg-7">
        <div class="row">
            <div class="col-lg-4">
                <input type="text" id="reduction_amount" name="reduction_amount" value="{$currentTab->getFieldValue($currentObject, 'reduction_amount')|floatval}" onchange="this.value = this.value.replace(/,/g, '.');" />
            </div>
            <div class="col-lg-4">
                <select name="reduction_currency" >
                    {foreach from=$currencies item='currency'}
                        <option value="{$currency.id_currency|intval}" {if $currentTab->getFieldValue($currentObject, 'reduction_currency') == $currency.id_currency || (!$currentTab->getFieldValue($currentObject, 'reduction_currency') && $currency.id_currency == $defaultCurrency)}selected="selected"{/if}>{$currency.iso_code|escape:'quotes':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
            <div class="col-lg-4">
                <select name="reduction_tax" >
                    <option value="0" {if $currentTab->getFieldValue($currentObject, 'reduction_tax') == 0}selected="selected"{/if}>{l s='Tax excluded' mod='orderfees'}</option>
                    <option value="1" {if $currentTab->getFieldValue($currentObject, 'reduction_tax') == 1}selected="selected"{/if}>{l s='Tax included' mod='orderfees'}</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div id="apply_discount_to_div" class="form-group">
    <label class="control-label col-lg-3">{l s='Apply a fee to' mod='orderfees'}</label>
    <div class="col-lg-7">
        <div class="radio" for="apply_discount_to_order">
            <label for="apply_discount_to_order">
                <input type="radio" name="apply_discount_to" id="apply_discount_to_order" value="order" {if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval == 0}checked="checked"{/if} />
                {l s='Order (without shipping)' mod='orderfees'}
            </label>
        </div>
        <div class="radio" for="apply_discount_to_product">
            <label for="apply_discount_to_product">
                <input type="radio" name="apply_discount_to" id="apply_discount_to_product" value="specific"  {if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval > 0}checked="checked"{/if} />
                {l s='Specific product' mod='orderfees'}
            </label>
        </div>
        <div class="radio" for="apply_discount_to_cheapest">
            <label for="apply_discount_to_cheapest">
                <input type="radio" name="apply_discount_to" id="apply_discount_to_cheapest" value="cheapest"  {if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval == -1}checked="checked"{/if} />
                {l s='Cheapest product' mod='orderfees'}
            </label>
        </div>
        <div class="radio" for="apply_discount_to_selection">
            <label for="apply_discount_to_selection">
                <input type="radio" name="apply_discount_to" id="apply_discount_to_selection" value="selection"  {if $currentTab->getFieldValue($currentObject, 'reduction_product')|intval == -2}checked="checked"{/if} />
                {l s='Selected product(s)' mod='orderfees'}
            </label>
        </div>
    </div>
</div>
    
<div id="apply_discount_to_product_div" class="form-group">
	<label class="control-label col-lg-3">{l s='Product' mod='orderfees'}</label>
	<div class="input-group col-lg-5">
		<input type="text" id="reductionProductFilter" name="reductionProductFilter" value="{$reductionProductFilter|escape:'html':'UTF-8'}" />
		<input type="hidden" id="reduction_product" name="reduction_product" value="{$currentTab->getFieldValue($currentObject, 'reduction_product')|intval}" />
		<span class="input-group-addon"><i class="icon-search"></i></span>
	</div>
</div>
                
<div class="form-group">        
        <label class="control-label col-lg-3">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='This fee will not be displayed but included in shipping costs.' mod='orderfees'}">
                {l s='Include in the shipping costs' mod='orderfees'}
            </span>
        </label>
	<div class="input-group col-lg-2">
		<span class="switch prestashop-switch">
			<input type="radio" name="in_shipping" id="in_shipping_on" value="1" {if $currentTab->getFieldValue($currentObject, 'is_fee')|intval & $module->getConstant('IN_SHIPPING')}checked="checked"{/if} />
			<label class="t" for="in_shipping_on">{l s='Yes' mod='orderfees'}</label>
			<input type="radio" name="in_shipping" id="in_shipping_off" value="0"  {if !($currentTab->getFieldValue($currentObject, 'is_fee')|intval & $module->getConstant('IN_SHIPPING'))}checked="checked"{/if} />
			<label class="t" for="in_shipping_off">{l s='No' mod='orderfees'}</label>
			<a class="slide-button btn"></a>
		</span>
	</div>
</div>
                        
{hook h="displayOrderFeesFormActionsAfter" module=$module controller=$currentTab object=$currentObject}