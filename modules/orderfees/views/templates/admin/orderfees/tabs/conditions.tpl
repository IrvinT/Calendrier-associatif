{*
* Order Fees
*
* @author    motionSeed <ecommerce@motionseed.com>
* @copyright 2016 motionSeed. All rights reserved.
* @license   https://www.motionseed.com/license
*}

{hook h="displayOrderFeesFormConditionsBefore" module=$module controller=$currentTab object=$currentObject}

<div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='Optional, the fee will be available for everyone if you leave this field blank.' mod='orderfees'}">
			{l s='Limit to a single customer' mod='orderfees'}
		</span>
	</label>
	<div class="input-group col-lg-9">
		<span class="input-group-addon"><i class="icon-user"></i></i></span>
		<input type="hidden" id="id_customer" name="id_customer" value="{$currentTab->getFieldValue($currentObject, 'id_customer')|intval}" />
		<input type="text" id="customerFilter" class="input-xlarge" name="customerFilter" value="{$customerFilter|escape:'html':'UTF-8'}" />
		<span class="input-group-addon"><i class="icon-search"></i></span>
	</div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='You can choose a minimum amount for the fee either with or without the taxes and shipping.' mod='orderfees'}">
			{l s='Minimum amount' mod='orderfees'}
		</span>
	</label>
	<div class="col-lg-9">
		<div class="row">
			<div class="col-lg-3">
				<input type="text" name="minimum_amount" value="{$currentTab->getFieldValue($currentObject, 'minimum_amount')|floatval}" />
			</div>
			<div class="col-lg-2">
				<select name="minimum_amount_currency">
				{foreach from=$currencies item='currency'}
					<option value="{$currency.id_currency|intval}"
					{if $currentTab->getFieldValue($currentObject, 'minimum_amount_currency') == $currency.id_currency
						|| (!$currentTab->getFieldValue($currentObject, 'minimum_amount_currency') && $currency.id_currency == $defaultCurrency)}
						selected="selected"
					{/if}
					>
						{$currency.iso_code|escape:'html':'UTF-8'}
					</option>
				{/foreach}
				</select>
			</div>
			<div class="col-lg-3">
				<select name="minimum_amount_tax">
					<option value="0" {if $currentTab->getFieldValue($currentObject, 'minimum_amount_tax') == 0}selected="selected"{/if}>{l s='Tax excluded' mod='orderfees'}</option>
					<option value="1" {if $currentTab->getFieldValue($currentObject, 'minimum_amount_tax') == 1}selected="selected"{/if}>{l s='Tax included' mod='orderfees'}</option>
				</select>
			</div>
			<div class="col-lg-4">
				<select name="minimum_amount_shipping">
					<option value="0" {if $currentTab->getFieldValue($currentObject, 'minimum_amount_shipping') == 0}selected="selected"{/if}>{l s='Shipping excluded' mod='orderfees'}</option>
					<option value="1" {if $currentTab->getFieldValue($currentObject, 'minimum_amount_shipping') == 1}selected="selected"{/if}>{l s='Shipping included' mod='orderfees'}</option>
				</select>
			</div>
		</div>
	</div>
</div>
                                
<div class="form-group">
        <label class="control-label col-lg-3">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='You can choose a maximum amount for the fee either with or without the taxes and shipping.' mod='orderfees'}">{l s='Maximum amount' mod='orderfees'}
            </span>
        </label>
        <div class="col-lg-9">
            <div class="row">
                <div class="col-lg-3">
                    <input type="text" name="maximum_amount" value="{$currentTab->getFieldValue($currentObject, 'maximum_amount')|floatval}" />
                </div>
                <div class="col-lg-2">
                    <select name="maximum_amount_currency">
                        {foreach from=$currencies item='currency'}
                            <option value="{$currency.id_currency|intval}" {if $currentTab->getFieldValue($currentObject, 'maximum_amount_currency') == $currency.id_currency || (!$currentTab->getFieldValue($currentObject, 'maximum_amount_currency') && $currency.id_currency == $defaultCurrency)}selected="selected"{/if}>
                                {$currency.iso_code|escape:'html':'UTF-8'}
                            </option>
                        {/foreach}
                    </select>
                </div>
                <div class="col-lg-3">
                    <select name="maximum_amount_tax">
                        <option value="0" {if $currentTab->getFieldValue($currentObject, 'maximum_amount_tax') == 0}selected="selected"{/if}>{l s='Tax excluded' mod='orderfees'}</option>
                        <option value="1" {if $currentTab->getFieldValue($currentObject, 'maximum_amount_tax') == 1}selected="selected"{/if}>{l s='Tax included' mod='orderfees'}</option>
                    </select>
                </div>
                        <div class="col-lg-4">
                            <select name="maximum_amount_shipping">
                                <option value="0" {if $currentTab->getFieldValue($currentObject, 'maximum_amount_shipping') == 0}selected="selected"{/if}>{l s='Shipping excluded' mod='orderfees'}</option>
                                <option value="1" {if $currentTab->getFieldValue($currentObject, 'maximum_amount_shipping') == 1}selected="selected"{/if}>{l s='Shipping included' mod='orderfees'}</option>
                            </select>
                        </div>
            </div>
        </div>
</div>
                                
<div class="form-group">
	<label class="control-label col-lg-3">
		{l s='Restrictions' mod='orderfees'}
	</label>
	<div class="col-lg-9">
                {hook h="displayOrderFeesFormConditionsRestrictionsBefore" module=$module controller=$currentTab object=$currentObject}
                
		{if $countries.unselected|@count + $countries.selected|@count > 1}
			<p class="checkbox">
				<label>
					<input type="checkbox" id="country_restriction" name="country_restriction" value="1" {if $countries.unselected|@count}checked="checked"{/if} />
					{l s='Country selection' mod='orderfees'}
				</label>
			</p>
			<div id="country_restriction_div">
                                <span class="help-block">{l s='This restriction applies to the country of delivery.' mod='orderfees'}</span>
				<table class="table">
					<tr>
						<td>
							<p>{l s='Unselected countries' mod='orderfees'}</p>
							<select id="country_select_1" multiple>
								{foreach from=$countries.unselected item='country'}
									<option value="{$country.id_country|intval}">&nbsp;{$country.name|escape}</option>
								{/foreach}
							</select>
							<a id="country_select_add" class="btn btn-default btn-block clearfix">{l s='Add' mod='orderfees'} <i class="icon-arrow-right"></i></a>
						</td>
						<td>
							<p>{l s='Selected countries' mod='orderfees'}</p>
							<select name="country_select[]" id="country_select_2" class="input-large" multiple>
								{foreach from=$countries.selected item='country'}
									<option value="{$country.id_country|intval}">&nbsp;{$country.name|escape}</option>
								{/foreach}
							</select>
							<a id="country_select_remove" class="btn btn-default btn-block clearfix"><i class="icon-arrow-left"></i> {l s='Remove' mod='orderfees'} </a>
						</td>
					</tr>
				</table>
			</div>
		{/if}
                
                {if $zipcode_countries_nb > 0}
                    <p class="checkbox">
                        <label>
                            <input type="checkbox" id="zipcode_restriction" name="zipcode_restriction" value="1" {if $zipcode_rule_groups|@count}checked="checked"{/if} />
                            {l s='Zip/Postal Codes selection' mod='orderfees'}
                        </label>
                    </p>
                    <div id="zipcode_restriction_div">
                        <span class="help-block">{l s='This restriction applies to the country of delivery.' mod='orderfees'}</span>
                        <table id="zipcode_rule_group_table" class="table">
                            {foreach from=$zipcode_rule_groups item='zipcode_rule_group'}
                                {$zipcode_rule_group}
                            {/foreach}
                        </table>
                        <a href="javascript:addZipcodeRuleGroup();" class="btn btn-default ">
                            <i class="icon-plus-sign"></i> {l s='Zip/Postal Codes selection' mod='orderfees'}
                        </a>
                    </div>
		{/if}

		{if $carriers.unselected|@count + $carriers.selected|@count > 1}
			<p class="checkbox">
				<label>
					<input type="checkbox" id="carrier_restriction" name="carrier_restriction" value="1" {if $carriers.unselected|@count}checked="checked"{/if} />
					{l s='Carrier selection' mod='orderfees'}
				</label>
			</p>
			<div id="carrier_restriction_div">
				<table class="table">
					<tr>
						<td>
							<p>{l s='Unselected carriers' mod='orderfees'}</p>
							<select id="carrier_select_1" class="input-large" multiple>
								{foreach from=$carriers.unselected item='carrier'}
									<option value="{$carrier.id_reference|intval}">&nbsp;{$carrier.name|escape}</option>
								{/foreach}
							</select>
							<a id="carrier_select_add" class="btn btn-default btn-block clearfix" >{l s='Add' mod='orderfees'} <i class="icon-arrow-right"></i></a>
						</td>
						<td>
							<p>{l s='Selected carriers' mod='orderfees'}</p>
							<select name="carrier_select[]" id="carrier_select_2" class="input-large" multiple>
								{foreach from=$carriers.selected item='carrier'}
									<option value="{$carrier.id_reference|intval}">&nbsp;{$carrier.name|escape}</option>
								{/foreach}
							</select>
							<a id="carrier_select_remove" class="btn btn-default btn-block clearfix"><i class="icon-arrow-left"></i> {l s='Remove' mod='orderfees'} </a>
						</td>
					</tr>
				</table>
			</div>
		{/if}

		{if $groups.unselected|@count + $groups.selected|@count > 1}
			<p class="checkbox">
				<label>
					<input type="checkbox" id="group_restriction" name="group_restriction" value="1" {if $groups.unselected|@count}checked="checked"{/if} />
					{l s='Customer group selection' mod='orderfees'}
				</label>
			</p>
			<div id="group_restriction_div">
				<table class="table">
					<tr>
						<td>
							<p>{l s='Unselected groups' mod='orderfees'}</p>
							<select id="group_select_1" class="input-large" multiple>
								{foreach from=$groups.unselected item='group'}
									<option value="{$group.id_group|intval}">&nbsp;{$group.name|escape}</option>
								{/foreach}
							</select>
							<a id="group_select_add" class="btn btn-default btn-block clearfix" >{l s='Add' mod='orderfees'} <i class="icon-arrow-right"></i></a>
						</td>
						<td>
							<p>{l s='Selected groups' mod='orderfees'}</p>
							<select name="group_select[]" class="input-large" id="group_select_2" multiple>
								{foreach from=$groups.selected item='group'}
									<option value="{$group.id_group|intval}">&nbsp;{$group.name|escape}</option>
								{/foreach}
							</select>
							<a id="group_select_remove" class="btn btn-default btn-block clearfix" ><i class="icon-arrow-left"></i> {l s='Remove' mod='orderfees'}</a>
						</td>
					</tr>
				</table>
			</div>
		{/if}

		{if $cart_rules.unselected|@count + $cart_rules.selected|@count > 0}
			<p class="checkbox">
				<label>
					<input type="checkbox" id="cart_rule_restriction" name="cart_rule_restriction" value="1" {if $cart_rules.unselected|@count}checked="checked"{/if} />
					{l s='Compatibility with other fees' mod='orderfees'}
				</label>
			</p>
			<div id="cart_rule_restriction_div" >
				<table  class="table">
					<tr>
						<td>
							<p>{l s='Uncombinable fees' mod='orderfees'}</p>
							<select id="cart_rule_select_1" multiple="">
								{foreach from=$cart_rules.unselected item='cart_rule'}
									<option value="{$cart_rule.id_cart_rule|intval}">&nbsp;{$cart_rule.name|escape}</option>
								{/foreach}
							</select>
							<a id="cart_rule_select_add" class="btn btn-default btn-block clearfix">{l s='Add' mod='orderfees'} <i class="icon-arrow-right"></i></a>
						</td>
						<td>
							<p>{l s='Combinable fees' mod='orderfees'}</p>
							<select name="cart_rule_select[]" id="cart_rule_select_2" multiple>
								{foreach from=$cart_rules.selected item='cart_rule'}
									<option value="{$cart_rule.id_cart_rule|intval}">&nbsp;{$cart_rule.name|escape}</option>
								{/foreach}
							</select>
							<a id="cart_rule_select_remove" class="btn btn-default btn-block clearfix" ><i class="icon-arrow-left"></i> {l s='Remove' mod='orderfees'}</a>
						</td>
					</tr>
				</table>
			</div>
		{/if}

			<p class="checkbox">
				<label>
					<input type="checkbox" id="product_restriction" name="product_restriction" value="1" {if $product_rule_groups|@count}checked="checked"{/if} />
					{l s='Product selection' mod='orderfees'}
				</label>
			</p>
			<div id="product_restriction_div">
				<table id="product_rule_group_table" class="table">
					{foreach from=$product_rule_groups item='product_rule_group'}
						{$product_rule_group}
					{/foreach}
				</table>
				<a href="javascript:addProductRuleGroup();" class="btn btn-default ">
					<i class="icon-plus-sign"></i> {l s='Product selection' mod='orderfees'}
				</a>
			</div>
                                
                                
                                
                {if $payments.unselected|@count + $payments.selected|@count > 0}
			<p class="checkbox">
				<label>
					<input type="checkbox" id="payment_restriction" name="payment_restriction" value="1" {if $payments.unselected|@count}checked="checked"{/if} />
					{l s='Payment modules selection' mod='orderfees'}
				</label>
			</p>
			<div id="payment_restriction_div">
				<table class="table">
					<tr>
						<td>
							<p>{l s='Unselected payment modules' mod='orderfees'}</p>
							<select id="payment_select_1" class="input-large" multiple>
								{foreach from=$payments.unselected item='payment'}
									<option value="{$payment.id_module|intval}">&nbsp;{Module::getModuleName($payment.name)|escape}</option>
								{/foreach}
							</select>
							<a id="payment_select_add" class="btn btn-default btn-block clearfix" >{l s='Add' mod='orderfees'} <i class="icon-arrow-right"></i></a>
						</td>
						<td>
							<p>{l s='Selected payment modules' mod='orderfees'}</p>
							<select name="payment_select[]" id="payment_select_2" class="input-large" multiple>
								{foreach from=$payments.selected item='payment'}
									<option value="{$payment.id_module|intval}">&nbsp;{Module::getModuleName($payment.name)|escape}</option>
								{/foreach}
							</select>
							<a id="payment_select_remove" class="btn btn-default btn-block clearfix"><i class="icon-arrow-left"></i> {l s='Remove' mod='orderfees'} </a>
						</td>
					</tr>
				</table>
			</div>
		{/if}
                                
                <p class="checkbox">
                    <label>
                        <input type="checkbox" id="dimension_restriction" name="dimension_restriction" value="1" {if $dimension_rule_groups|@count}checked="checked"{/if} />
                        {l s='Dimension selection' mod='orderfees'}
                    </label>
                </p>
                <div id="dimension_restriction_div">
                    <table id="dimension_rule_group_table" class="table">
                        {foreach from=$dimension_rule_groups item='dimension_rule_group'}
                            {$dimension_rule_group}
                        {/foreach}
                    </table>
                    <a href="javascript:addDimensionRuleGroup();" class="btn btn-default ">
                        <i class="icon-plus-sign"></i> {l s='Dimension selection' mod='orderfees'}
                    </a>
                </div>
                    
                {hook h="displayOrderFeesFormConditionsRestrictionsAfter" module=$module controller=$currentTab object=$currentObject}

		{if $shops.unselected|@count + $shops.selected|@count > 1}
			<p class="checkbox">
				<label>
					<input type="checkbox" id="shop_restriction" name="shop_restriction" value="1" {if $shops.unselected|@count}checked="checked"{/if} />
					{l s='Shop selection' mod='orderfees'}
				</label>
			</p>
			<div id="shop_restriction_div">
				<br/>
				<table class="table">
					<tr>
						<td>
							<p>{l s='Unselected shops' mod='orderfees'}</p>
							<select id="shop_select_1" multiple>
								{foreach from=$shops.unselected item='shop'}
									<option value="{$shop.id_shop|intval}">&nbsp;{$shop.name|escape}</option>
								{/foreach}
							</select>
							<br/>
							<a id="shop_select_add" class="btn btn-default" >{l s='Add' mod='orderfees'} &gt;&gt; </a>
						</td>
						<td>
							<p>{l s='Selected shops' mod='orderfees'}</p>
							<select name="shop_select[]" id="shop_select_2" multiple>
								{foreach from=$shops.selected item='shop'}
									<option value="{$shop.id_shop|intval}">&nbsp;{$shop.name|escape}</option>
								{/foreach}
							</select>
							<br/>
							<a id="shop_select_remove" class="btn btn-default" > &lt;&lt; {l s='Remove' mod='orderfees'} </a>
						</td>
					</tr>
				</table>
			</div>
		{/if}
	</div>
</div>

<input type="hidden" name="date_from" value="{$defaultDateFrom|escape:'html':'UTF-8'}" />
<input type="hidden" name="date_to" value="{$defaultDateTo|escape:'html':'UTF-8'}" />

{hook h="displayOrderFeesFormConditionsAfter" module=$module controller=$currentTab object=$currentObject}

<script type="text/javascript">
    var message_errors_zipcode_select_country = "{l s='Please select a country.' mod='orderfees'}";
    var message_errors_dimension_select_dimension = "{l s='Please select a dimension (eg. : width, height, ...).' mod='orderfees'}";
</script>