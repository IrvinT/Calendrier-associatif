{*
* Order Fees
*
* @author    motionSeed <ecommerce@motionseed.com>
* @copyright 2016 motionSeed. All rights reserved.
* @license   https://www.motionseed.com/license
*}

{hook h="displayOrderFeesFormInformationsBefore" module=$module controller=$currentTab object=$currentObject}

<div class="form-group">
	<label class="control-label col-lg-3 required">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l s='This will be displayed in the cart summary, as well as on the invoice.' mod='orderfees'}">
			{l s='Name' mod='orderfees'}
		</span>
	</label>
	<div class="col-lg-8">	
		{foreach from=$languages item=language}
		{if $languages|count > 1}
		<div class="row">
			<div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $id_lang_default}style="display:none"{/if}>
				<div class="col-lg-9">
		{/if}
					<input id="name_{$language.id_lang|intval}" type="text"  name="name_{$language.id_lang|intval}" value="{$currentTab->getFieldValue($currentObject, 'name', $language.id_lang|intval)|escape:'html':'UTF-8'}">
		{if $languages|count > 1}
				</div>
				<div class="col-lg-2">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						{$language.iso_code|escape:'html':'UTF-8'}
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						{foreach from=$languages item=language}
						<li><a href="javascript:hideOtherLanguage({$language.id_lang|intval});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
						{/foreach}
					</ul>
				</div>		
			</div>
		</div>
		{/if}
		{/foreach}
	</div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l s='For your eyes only. This will never be displayed to the customer.' mod='orderfees'}">
			{l s='Description' mod='orderfees'}
		</span>
	</label>
	<div class="col-lg-8">
		<textarea name="description" rows="2" class="textarea-autosize">{$currentTab->getFieldValue($currentObject, 'description')|escape:'html':'UTF-8'}</textarea>
	</div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l s='Fees are applied to the cart by priority. A fee with priority of "1" will be processed before a fee with a priority of "2".' mod='orderfees'}">
			{l s='Priority' mod='orderfees'}
		</span>
	</label>
	<div class="col-lg-1">
		<input type="text" class="input-mini" name="priority" value="{$currentTab->getFieldValue($currentObject, 'priority')|intval}" />
	</div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">{l s='Status' mod='orderfees'}</label>
	<div class="input-group col-lg-2">
		<span class="switch prestashop-switch">
			<input type="radio" name="active" id="active_on" value="1" {if $currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if} />
			<label class="t" for="active_on">{l s='Yes' mod='orderfees'}</label>
			<input type="radio" name="active" id="active_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if} />
			<label class="t" for="active_off">{l s='No' mod='orderfees'}</label>
			<a class="slide-button btn"></a>
		</span>
	</div>
</div>
                        
{hook h="displayOrderFeesFormInformationsAfter" module=$module controller=$currentTab object=$currentObject}
                        
<script type="text/javascript">
	$(".textarea-autosize").autosize();
</script>